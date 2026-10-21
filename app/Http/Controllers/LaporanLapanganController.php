<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\FieldAssignment;
use App\Models\Evidence;
use App\Models\IssueLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LaporanLapanganController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'belum_selesai');
        $petugasId = Auth::id();

        // 1. Tugas Belum Selesai (Aktif) milik Petugas Login
        $myActiveAssignments = FieldAssignment::with(['issue.komoditas', 'issue.kabupaten', 'assignedBy'])
            ->where('petugas_id', $petugasId)
            ->whereIn('status', ['ditugaskan', 'sedang_dikerjakan'])
            ->orderByDesc('tanggal_penugasan')
            ->get();

        $myActiveIssueIds = $myActiveAssignments->pluck('issue_id')->toArray();
        $completedIssueIds = FieldAssignment::where('petugas_id', $petugasId)
            ->where('status', 'selesai')
            ->pluck('issue_id')
            ->toArray();

        // 2. Semua Isu yang telah di-inputkan dan tersedia untuk dipilih oleh Petugas Lapangan
        $availableIssues = Issue::with(['komoditas', 'kabupaten'])
            ->whereNotIn('id', array_merge($myActiveIssueIds, $completedIssueIds))
            ->whereNotIn('status', ['selesai', 'ditutup', 'hoaks'])
            ->orderByDesc('virality_score')
            ->orderByDesc('created_at')
            ->paginate(15, ['*'], 'page_tersedia');

        // 3. Tugas Sudah Selesai milik Petugas Login
        $completedAssignments = FieldAssignment::with(['issue.komoditas', 'issue.kabupaten', 'assignedBy', 'issue.evidences'])
            ->where('petugas_id', $petugasId)
            ->where('status', 'selesai')
            ->orderByDesc('updated_at')
            ->paginate(15, ['*'], 'page_selesai');

        $totalBelumSelesai = $myActiveAssignments->count() + $availableIssues->total();
        $totalSelesai = FieldAssignment::where('petugas_id', $petugasId)->where('status', 'selesai')->count();

        // Compatibility fallback
        $assignments = ($tab === 'selesai') ? $completedAssignments : $myActiveAssignments;

        return view('laporan.index', compact(
            'tab',
            'myActiveAssignments',
            'availableIssues',
            'completedAssignments',
            'totalBelumSelesai',
            'totalSelesai',
            'assignments'
        ));
    }

    public function ambilTugas(Issue $issue)
    {
        if (in_array($issue->status, ['selesai', 'ditutup', 'hoaks'])) {
            return back()->with('error', 'Isu ini sudah tidak aktif atau telah diselesaikan.');
        }

        // Cek jika petugas sudah memiliki penugasan aktif untuk isu ini
        $existingAssignment = FieldAssignment::where('issue_id', $issue->id)
            ->where('petugas_id', Auth::id())
            ->where('status', '!=', 'selesai')
            ->first();

        if ($existingAssignment) {
            return redirect()->route('laporan.show', $existingAssignment)
                ->with('info', "Anda sudah mengambil isu {$issue->kode_isu}. Silakan lanjutkan pelaporan lapangan.");
        }

        // Buat penugasan baru langsung oleh petugas
        $assignment = FieldAssignment::create([
            'issue_id'          => $issue->id,
            'petugas_id'        => Auth::id(),
            'assigned_by'       => Auth::id(),
            'tanggal_penugasan' => now(),
            'status'            => 'sedang_dikerjakan',
            'catatan_admin'     => 'Dipilih langsung oleh petugas lapangan untuk inspeksi fisik.',
        ]);

        $oldStatus = $issue->status;
        $issue->update([
            'status'    => 'di_lapangan',
            'assign_by' => Auth::id(),
        ]);

        IssueLog::create([
            'issue_id'    => $issue->id,
            'user_id'     => Auth::id(),
            'action'      => 'Tugas Dipilih oleh Petugas',
            'status_dari' => $oldStatus,
            'status_ke'   => 'di_lapangan',
            'deskripsi'   => "Petugas " . Auth::user()->name . " memilih isu ini untuk dilakukan pemeriksaan fisik lapangan.",
            'ip_address'  => request()->ip(),
        ]);

        return redirect()->route('laporan.show', $assignment)
            ->with('success', "Isu {$issue->kode_isu} berhasil dipilih! Silakan lakukan check-in GPS dan laporkan kondisi lapangan.");
    }

    public function show(FieldAssignment $assignment)
    {
        abort_unless($assignment->petugas_id === Auth::id(), 403);
        $assignment->load(['issue.komoditas', 'issue.kabupaten', 'assignedBy', 'issue.evidences']);
        return view('laporan.show', compact('assignment'));
    }

    public function checkin(Request $request, FieldAssignment $assignment)
    {
        abort_unless($assignment->petugas_id === Auth::id(), 403);

        $metode = $request->input('metode_verifikasi', 'offline');

        if ($metode === 'online') {
            $validated = $request->validate([
                'metode_verifikasi' => 'required|in:offline,online',
                'no_telepon_sumber' => 'nullable|string|max:50',
                'sosmed_sumber'     => 'nullable|string|max:255',
            ]);

            if (empty(trim($validated['no_telepon_sumber'] ?? '')) && empty(trim($validated['sosmed_sumber'] ?? ''))) {
                return back()->withErrors([
                    'no_telepon_sumber' => 'Untuk cek online, harap masukkan nomor telepon/WA atau akun/sosial media dari sumber.',
                ])->withInput();
            }

            $assignment->update([
                'status'            => 'sedang_dikerjakan',
                'metode_verifikasi' => 'online',
                'no_telepon_sumber' => $validated['no_telepon_sumber'] ?? null,
                'sosmed_sumber'     => $validated['sosmed_sumber'] ?? null,
                'latitude'          => null,
                'longitude'         => null,
                'checkin_at'        => now(),
            ]);

            $assignment->issue->update(['status' => 'di_lapangan']);

            $kontak = array_filter([$validated['no_telepon_sumber'] ?? null, $validated['sosmed_sumber'] ?? null]);
            $deskripsiKontak = implode(' / ', $kontak);

            IssueLog::create([
                'issue_id'   => $assignment->issue_id,
                'user_id'    => Auth::id(),
                'action'     => 'Verifikasi Online',
                'deskripsi'  => "Petugas melakukan validasi online melalui sumber: {$deskripsiKontak}",
                'ip_address' => request()->ip(),
            ]);

            return back()->with('success', 'Verifikasi online dari sumber berhasil dicatat.');
        } else {
            $validated = $request->validate([
                'metode_verifikasi' => 'nullable|in:offline,online',
                'latitude'          => 'required|numeric|between:-90,90',
                'longitude'         => 'required|numeric|between:-180,180',
            ]);

            $assignment->update([
                'status'            => 'sedang_dikerjakan',
                'metode_verifikasi' => 'offline',
                'latitude'          => $validated['latitude'],
                'longitude'         => $validated['longitude'],
                'checkin_at'        => now(),
            ]);

            $assignment->issue->update(['status' => 'di_lapangan']);

            IssueLog::create([
                'issue_id'   => $assignment->issue_id,
                'user_id'    => Auth::id(),
                'action'     => 'Check-in Lokasi',
                'deskripsi'  => "Petugas check-in di koordinat: {$validated['latitude']}, {$validated['longitude']}",
                'ip_address' => request()->ip(),
            ]);

            return back()->with('success', 'Check-in koordinat GPS lokasi pasar berhasil dicatat.');
        }
    }

    public function submitLaporan(Request $request, FieldAssignment $assignment)
    {
        abort_unless($assignment->petugas_id === Auth::id(), 403);

        $validated = $request->validate([
            'harga_aktual'       => 'required|numeric|min:0',
            'catatan_temuan'     => 'nullable|string',
            'catatan_wawancara'  => 'nullable|string',
            'tanggal_kunjungan'  => 'required|date',
            'foto_bukti'         => 'nullable|file|mimes:jpg,jpeg,png,webp|max:10240',
        ]);

        // Upload foto
        if ($request->hasFile('foto_bukti')) {
            $path = $request->file('foto_bukti')->store("evidences/lapangan-{$assignment->issue_id}", 'public');
            $validated['foto_bukti'] = $path;

            Evidence::create([
                'issue_id'    => $assignment->issue_id,
                'uploaded_by' => Auth::id(),
                'jenis'       => 'foto_lapangan',
                'tahap'       => 'lapangan',
                'file_path'   => $path,
                'file_name'   => $request->file('foto_bukti')->getClientOriginalName(),
                'file_type'   => $request->file('foto_bukti')->getClientMimeType(),
                'file_size'   => $request->file('foto_bukti')->getSize(),
                'metadata'    => [
                    'timestamp' => now()->toIso8601String(),
                    'metode'    => $assignment->metode_verifikasi,
                    'lat'       => $assignment->latitude,
                    'lng'       => $assignment->longitude,
                    'kontak'    => $assignment->no_telepon_sumber ?: $assignment->sosmed_sumber,
                ],
            ]);
        }

        $extraUpdates = ['status' => 'selesai'];
        if (!$assignment->checkin_at) {
            $extraUpdates['checkin_at'] = now();
        }

        $assignment->update(array_merge($validated, $extraUpdates));
        $assignment->issue->update(['status' => 'di_lapangan']);

        $metodeLabel = ($assignment->metode_verifikasi === 'online') ? 'Cek Online' : 'Cek Offline';
        IssueLog::create([
            'issue_id'   => $assignment->issue_id,
            'user_id'    => Auth::id(),
            'action'     => 'Laporan Lapangan Dikirim',
            'status_ke'  => 'laporan_selesai',
            'deskripsi'  => "Harga aktual: Rp " . number_format($validated['harga_aktual'], 0, ',', '.') . " ({$metodeLabel})",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('laporan.index', ['tab' => 'selesai'])->with('success', 'Laporan lapangan berhasil dikirim.');
    }
}
