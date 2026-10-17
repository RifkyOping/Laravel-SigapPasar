<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\IssueVerification;
use App\Models\IssueLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifikasiController extends Controller
{
    public function index()
    {
        $issues = Issue::with(['komoditas', 'kabupaten', 'inputBy'])
            ->where('status', 'baru')
            ->orderByDesc('virality_score')
            ->paginate(15);

        return view('verifikasi.index', compact('issues'));
    }

    public function show(Issue $issue)
    {
        $issue->load(['komoditas', 'kabupaten', 'pasar', 'inputBy', 'evidences', 'logs.user']);
        $issuesDuplikasi = Issue::where('id', '!=', $issue->id)
            ->where('komoditas_id', $issue->komoditas_id)
            ->where('kabupaten_id', $issue->kabupaten_id)
            ->whereIn('status', ['baru', 'diverifikasi'])
            ->get();

        return view('verifikasi.show', compact('issue', 'issuesDuplikasi'));
    }

    public function store(Request $request, Issue $issue)
    {
        $validated = $request->validate([
            'hasil_verifikasi'       => 'required|in:valid,butuh_cek_lapangan,hoaks,duplikasi',
            'prioritas_rekomendasi'  => 'nullable|in:rendah,sedang,tinggi,kritis',
            'catatan_verifikasi'     => 'required|string|min:10',
            'saran_tindakan'         => 'nullable|string',
            'duplikasi_dari'         => 'nullable|exists:issues,id',
        ]);

        $validated['issue_id']          = $issue->id;
        $validated['verifikator_id']    = Auth::id();
        $validated['tanggal_verifikasi'] = now();

        IssueVerification::create($validated);

        // Update issue status
        $newStatus = match($validated['hasil_verifikasi']) {
            'hoaks'     => 'hoaks',
            'duplikasi' => 'ditutup',
            default     => 'diverifikasi',
        };

        $oldStatus = $issue->status;
        $issue->update([
            'status'       => $newStatus,
            'prioritas'    => $validated['prioritas_rekomendasi'] ?? $issue->prioritas,
            'verifikasi_by'=> Auth::id(),
            'is_merged'    => $validated['hasil_verifikasi'] === 'duplikasi',
            'merged_to'    => $validated['duplikasi_dari'] ?? null,
        ]);

        IssueLog::create([
            'issue_id'    => $issue->id,
            'user_id'     => Auth::id(),
            'action'      => 'Verifikasi Selesai',
            'status_dari' => $oldStatus,
            'status_ke'   => $newStatus,
            'deskripsi'   => "Hasil verifikasi: {$validated['hasil_verifikasi']}. " . $validated['catatan_verifikasi'],
            'ip_address'  => request()->ip(),
        ]);

        return redirect()->route('verifikasi.index')->with('success', "Isu {$issue->kode_isu} berhasil diverifikasi.");
    }
}
