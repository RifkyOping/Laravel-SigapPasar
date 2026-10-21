<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\PenugasanController;
use App\Http\Controllers\LaporanLapanganController;
use App\Http\Controllers\IntervensiController;
use App\Http\Controllers\ResolusiController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

// Landing page
Route::get('/', function () {
    $now = now();
    $isuBulanIni = \App\Models\Issue::whereMonth('created_at', $now->month)
        ->whereYear('created_at', $now->year)
        ->count();
    $selesaiCount = \App\Models\Issue::whereIn('status', ['selesai', 'ditutup'])->count();
    $avgSla = \Illuminate\Support\Facades\DB::table('resolutions')->avg('durasi_penanganan_hari');
    $isuAktif = \App\Models\Issue::whereNotIn('status', ['selesai', 'ditutup', 'ditolak', 'hoaks'])->count();

    $kabupatenList = \App\Models\MasterData::where('type', 'kabupaten')
        ->withCount(['issues' => function ($q) {
            $q->whereNotIn('status', ['selesai', 'ditutup', 'ditolak', 'hoaks']);
        }])
        ->get();

    return view('welcome', compact('isuBulanIni', 'selesaiCount', 'avgSla', 'isuAktif', 'kabupatenList'));
});

// Authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard (semua role)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile (semua role)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ─── Isu (Tahap 1) ──────────────────────────────────────────
    // View: semua role | Create/Store: pengawas_siber, admin, super_admin
    Route::get('/isu', [IssueController::class, 'index'])->name('issues.index');
    Route::middleware('role:pengawas_siber,admin,super_admin')->group(function () {
        Route::get('/isu/buat', [IssueController::class, 'create'])->name('issues.create');
        Route::get('/isu/create', fn () => redirect()->route('issues.create'));
        Route::post('/isu', [IssueController::class, 'store'])->name('issues.store');
        Route::get('/isu/{issue}/edit', [IssueController::class, 'edit'])->name('issues.edit')->whereNumber('issue');
        Route::patch('/isu/{issue}', [IssueController::class, 'update'])->name('issues.update')->whereNumber('issue');
    });
    Route::get('/isu/{issue}', [IssueController::class, 'show'])->name('issues.show')->whereNumber('issue');
    Route::delete('/isu/{issue}', [IssueController::class, 'destroy'])
        ->name('issues.destroy')
        ->middleware('role:admin,super_admin')
        ->whereNumber('issue');

    // ─── Penugasan (Tahap 2) ─────────────────────────────────────
    Route::middleware('role:admin,super_admin')->group(function () {
        Route::get('/penugasan', [PenugasanController::class, 'index'])->name('penugasan.index');
        Route::post('/penugasan/{issue}', [PenugasanController::class, 'store'])->name('penugasan.store');
    });

    // ─── Laporan Lapangan (Tahap 3) ──────────────────────────────
    Route::middleware('role:petugas_lapangan')->group(function () {
        Route::get('/laporan', [LaporanLapanganController::class, 'index'])->name('laporan.index');
        Route::post('/laporan/ambil/{issue}', [LaporanLapanganController::class, 'ambilTugas'])->name('laporan.ambil')->whereNumber('issue');
        Route::get('/laporan/{assignment}', [LaporanLapanganController::class, 'show'])->name('laporan.show');
        Route::post('/laporan/{assignment}/checkin', [LaporanLapanganController::class, 'checkin'])->name('laporan.checkin');
        Route::post('/laporan/{assignment}/submit', [LaporanLapanganController::class, 'submitLaporan'])->name('laporan.submit');
    });

    // ─── Intervensi (Tahap 4) ────────────────────────────────────
    Route::middleware('role:admin,super_admin')->group(function () {
        Route::get('/intervensi', [IntervensiController::class, 'index'])->name('intervensi.index');
        Route::get('/intervensi/buat/{issue}', [IntervensiController::class, 'create'])->name('intervensi.create');
        Route::post('/intervensi/{issue}', [IntervensiController::class, 'store'])->name('intervensi.store');
        Route::patch('/intervensi/{intervention}/progres', [IntervensiController::class, 'updateProgress'])->name('intervensi.update-progres');
    });

    // ─── Resolusi (Tahap 5) ──────────────────────────────────────
    Route::middleware('role:admin,super_admin')->group(function () {
        Route::get('/resolusi/{issue}/tutup', [ResolusiController::class, 'create'])->name('resolusi.create');
        Route::post('/resolusi/{issue}', [ResolusiController::class, 'store'])->name('resolusi.store');
    });

    // ─── Master Data (Super Admin) ───────────────────────────────
    Route::middleware('role:super_admin')->group(function () {
        Route::resource('master-data', MasterDataController::class);
    });

    // ─── Data Harga Harian Pasar ─────────────────────────────────
    Route::middleware('role:admin,super_admin,pimpinan')->group(function () {
        Route::get('/harga-pasar', [App\Http\Controllers\PriceImportController::class, 'index'])->name('prices.index');
    });

    // ─── Import & Template Harga Pasar (Khusus Admin & Super Admin) ──
    Route::middleware('role:admin,super_admin')->group(function () {
        Route::get('/harga-pasar/template', [App\Http\Controllers\PriceImportController::class, 'downloadTemplate'])->name('prices.template');
        Route::post('/harga-pasar/import', [App\Http\Controllers\PriceImportController::class, 'import'])->name('prices.import');
    });

    // ─── Manajemen Pengguna (Super Admin) ────────────────────────
    Route::middleware('role:super_admin')->group(function () {
        Route::resource('users', UserManagementController::class)->except(['show']);
    });
});

require __DIR__.'/auth.php';
