<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\Resolution;
use App\Models\IssueLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResolusiController extends Controller
{
    public function create(Issue $issue)
    {
        $issue->load(['interventions', 'assignments.petugas']);
        return view('resolusi.create', compact('issue'));
    }

    public function store(Request $request, Issue $issue)
    {
        $validated = $request->validate([
            'harga_sebelum_intervensi' => 'nullable|numeric|min:0',
            'harga_setelah_intervensi' => 'nullable|numeric|min:0',
            'kesimpulan'               => 'required|string|min:20',
            'catatan_akhir'            => 'nullable|string',
            'status_akhir'             => 'required|in:selesai,ditutup_hoaks,ditutup_tanpa_intervensi',
        ]);

        $durasi = $issue->created_at->diffInDays(now());

        Resolution::create(array_merge($validated, [
            'issue_id'             => $issue->id,
            'ditutup_by'           => Auth::id(),
            'durasi_penanganan_hari' => $durasi,
            'tanggal_penutupan'    => now(),
        ]));

        $oldStatus = $issue->status;
        $issue->update(['status' => 'selesai']);

        IssueLog::create([
            'issue_id'    => $issue->id,
            'user_id'     => Auth::id(),
            'action'      => 'Isu Ditutup',
            'status_dari' => $oldStatus,
            'status_ke'   => 'selesai',
            'deskripsi'   => "Isu diselesaikan. Durasi: {$durasi} hari. " . $validated['kesimpulan'],
            'ip_address'  => request()->ip(),
        ]);

        return redirect()->route('issues.show', $issue)->with('success', "Isu {$issue->kode_isu} berhasil ditutup.");
    }
}
