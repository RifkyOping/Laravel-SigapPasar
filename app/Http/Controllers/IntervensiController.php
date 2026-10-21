<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\Intervention;
use App\Models\IssueLog;
use App\Models\Evidence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class IntervensiController extends Controller
{
    public function index(Request $request)
    {
        // 1. Antrian Isu yang Menunggu Tindakan Intervensi (Tahap 4 Awal):
        // Isu yang telah dicek lapangan (di_lapangan), ditugaskan, atau diverifikasi,
        // yang belum memiliki intervensi dan belum selesai/ditutup.
        $antrianIsu = Issue::with([
                'komoditas',
                'kabupaten',
                'pasar',
                'assignments' => fn($q) => $q->latest(),
                'assignments.petugas',
            ])
            ->whereIn('status', ['di_lapangan', 'ditugaskan', 'diverifikasi'])
            ->whereDoesntHave('interventions')
            ->orderByRaw("CASE WHEN status = 'di_lapangan' THEN 1 WHEN status = 'ditugaskan' THEN 2 ELSE 3 END")
            ->orderByDesc('virality_score')
            ->latest()
            ->paginate(15, ['*'], 'antrian_page');

        // 2. Intervensi yang Sedang Berjalan & Selesai (Monitoring):
        $interventions = Intervention::with(['issue.komoditas', 'issue.kabupaten', 'dibuatBy'])
            ->latest()
            ->paginate(15, ['*'], 'berjalan_page');

        $activeTab = $request->get('tab', ($antrianIsu->total() > 0 ? 'antrian' : 'berjalan'));

        return view('intervensi.index', compact('antrianIsu', 'interventions', 'activeTab'));
    }

    public function create(Issue $issue)
    {
        return view('intervensi.create', compact('issue'));
    }

    public function store(Request $request, Issue $issue)
    {
        $validated = $request->validate([
            'jenis_intervensi'    => 'required|in:operasi_pasar,sidak_gudang,koordinasi_bulog,rapat_koordinasi,penyuluhan,lainnya',
            'nama_intervensi'     => 'required|string|max:255',
            'deskripsi'           => 'required|string',
            'hasil_diharapkan'    => 'nullable|string',
            'tanggal_mulai'       => 'required|date',
            'target_selesai'      => 'nullable|date|after_or_equal:tanggal_mulai',
            'pic_nama'            => 'required|string|max:255',
            'pic_jabatan'         => 'nullable|string|max:255',
            'instansi_terlibat'   => 'nullable|array',
            'instansi_terlibat.*' => 'string|max:255',
            'dokumen_pendukung'   => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ], [
            'dokumen_pendukung.mimes' => 'Format file dokumen pendukung harus berupa file PDF atau Word (.pdf, .doc, .docx).',
            'dokumen_pendukung.max'   => 'Ukuran file dokumen pendukung maksimal 10 MB.',
        ]);

        $validated['issue_id']   = $issue->id;
        $validated['dibuat_by']  = Auth::id();
        $validated['status']     = 'sedang_berjalan';

        if ($request->hasFile('dokumen_pendukung')) {
            $file = $request->file('dokumen_pendukung');
            $path = $file->store("intervensi/{$issue->id}", 'public');
            $validated['file_dokumen'] = $path;
            $validated['nama_dokumen'] = $file->getClientOriginalName();

            Evidence::create([
                'issue_id'    => $issue->id,
                'uploaded_by' => Auth::id(),
                'jenis'       => 'dokumen',
                'tahap'       => 'intervensi',
                'file_path'   => $path,
                'file_name'   => $file->getClientOriginalName(),
                'file_type'   => $file->getClientMimeType(),
                'file_size'   => $file->getSize(),
                'deskripsi'   => 'Dokumen Intervensi: ' . $validated['nama_intervensi'],
            ]);
        }

        Intervention::create($validated);

        $oldStatus = $issue->status;
        $issue->update(['status' => 'intervensi']);

        IssueLog::create([
            'issue_id'    => $issue->id,
            'user_id'     => Auth::id(),
            'action'      => 'Intervensi Dibuat',
            'status_dari' => $oldStatus,
            'status_ke'   => 'intervensi',
            'deskripsi'   => "Jenis: {$validated['jenis_intervensi']} - {$validated['nama_intervensi']}",
            'ip_address'  => request()->ip(),
        ]);

        return redirect()->route('issues.show', $issue)->with('success', 'Data intervensi berhasil disimpan.');
    }

    public function updateProgress(Request $request, Intervention $intervention)
    {
        $validated = $request->validate([
            'progres_terakhir' => 'required|string',
            'status'           => 'required|in:direncanakan,sedang_berjalan,selesai,dibatalkan',
            'tanggal_selesai_aktual' => 'nullable|date',
        ]);

        $intervention->update($validated);

        IssueLog::create([
            'issue_id'  => $intervention->issue_id,
            'user_id'   => Auth::id(),
            'action'    => 'Update Progres Intervensi',
            'deskripsi' => "Status: {$validated['status']} - {$validated['progres_terakhir']}",
            'ip_address'=> request()->ip(),
        ]);

        return back()->with('success', 'Progres intervensi diperbarui.');
    }
}
