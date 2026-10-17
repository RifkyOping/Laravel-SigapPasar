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
    public function index()
    {
        $assignments = FieldAssignment::with(['issue.komoditas', 'issue.kabupaten', 'assignedBy'])
            ->where('petugas_id', Auth::id())
            ->orderByDesc('tanggal_penugasan')
            ->paginate(15);

        return view('laporan.index', compact('assignments'));
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

        $validated = $request->validate([
            'latitude'  => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $assignment->update([
            'status'     => 'sedang_dikerjakan',
            'latitude'   => $validated['latitude'],
            'longitude'  => $validated['longitude'],
            'checkin_at' => now(),
        ]);

        $assignment->issue->update(['status' => 'di_lapangan']);

        IssueLog::create([
            'issue_id'  => $assignment->issue_id,
            'user_id'   => Auth::id(),
            'action'    => 'Check-in Lokasi',
            'deskripsi' => "Petugas check-in di koordinat: {$validated['latitude']}, {$validated['longitude']}",
            'ip_address'=> request()->ip(),
        ]);

        return back()->with('success', 'Check-in berhasil dicatat.');
    }

    public function submitLaporan(Request $request, FieldAssignment $assignment)
    {
        abort_unless($assignment->petugas_id === Auth::id(), 403);

        $validated = $request->validate([
            'harga_aktual'       => 'required|numeric|min:0',
            'catatan_temuan'     => 'required|string|min:20',
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
                'metadata'    => ['timestamp' => now()->toIso8601String(), 'lat' => $assignment->latitude, 'lng' => $assignment->longitude],
            ]);
        }

        $assignment->update(array_merge($validated, ['status' => 'selesai']));
        $assignment->issue->update(['status' => 'di_lapangan']);

        IssueLog::create([
            'issue_id'  => $assignment->issue_id,
            'user_id'   => Auth::id(),
            'action'    => 'Laporan Lapangan Dikirim',
            'status_ke' => 'laporan_selesai',
            'deskripsi' => "Harga aktual: Rp " . number_format($validated['harga_aktual'], 0, ',', '.'),
            'ip_address'=> request()->ip(),
        ]);

        return redirect()->route('laporan.index')->with('success', 'Laporan lapangan berhasil dikirim.');
    }
}
