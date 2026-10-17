<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\FieldAssignment;
use App\Models\IssueLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class PenugasanController extends Controller
{
    public function index()
    {
        $issues = Issue::with(['komoditas', 'kabupaten', 'verifications.verifikator'])
            ->where('status', 'diverifikasi')
            ->orderByDesc('prioritas')
            ->paginate(15);

        $petugas = User::where('role', 'petugas_lapangan')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('penugasan.index', compact('issues', 'petugas'));
    }

    public function store(Request $request, Issue $issue)
    {
        $validated = $request->validate([
            'petugas_id'      => 'required|exists:users,id',
            'catatan_admin'   => 'nullable|string',
            'tanggal_penugasan'=> 'required|date',
        ]);

        $assignment = FieldAssignment::create([
            'issue_id'          => $issue->id,
            'petugas_id'        => $validated['petugas_id'],
            'assigned_by'       => Auth::id(),
            'tanggal_penugasan' => $validated['tanggal_penugasan'],
            'status'            => 'ditugaskan',
            'catatan_admin'     => $validated['catatan_admin'],
        ]);

        $oldStatus = $issue->status;
        $issue->update(['status' => 'ditugaskan', 'assign_by' => Auth::id()]);

        // Log
        $petugas = User::find($validated['petugas_id']);
        IssueLog::create([
            'issue_id'    => $issue->id,
            'user_id'     => Auth::id(),
            'action'      => 'Ditugaskan ke Lapangan',
            'status_dari' => $oldStatus,
            'status_ke'   => 'ditugaskan',
            'deskripsi'   => "Ditugaskan kepada: {$petugas->name}",
            'ip_address'  => request()->ip(),
        ]);

        // Email notification (log driver)
        // Mail::to($petugas->email)->send(new \App\Mail\PenugasanMail($issue, $assignment, $petugas));

        return redirect()->route('penugasan.index')
            ->with('success', "Isu {$issue->kode_isu} berhasil ditugaskan kepada {$petugas->name}.");
    }
}
