<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\MasterData;
use App\Models\IssueLog;
use App\Models\Evidence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class IssueController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->isPimpinan()) {
            return redirect()->route('dashboard')->with('info', 'Akses Pimpinan difokuskan pada Ringkasan Eksekutif dan Tren Perkembangan Harga.');
        }

        $query = Issue::with(['komoditas', 'kabupaten', 'pasar', 'inputBy'])
            ->latest();

        // Filter
        if ($request->status)    $query->where('status', $request->status);
        if ($request->prioritas) $query->where('prioritas', $request->prioritas);
        if ($request->kabupaten) $query->where('kabupaten_id', $request->kabupaten);
        if ($request->komoditas) $query->where('komoditas_id', $request->komoditas);
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('kode_isu', 'like', "%{$request->search}%")
                  ->orWhere('kutipan_postingan', 'like', "%{$request->search}%")
                  ->orWhere('nama_akun', 'like', "%{$request->search}%");
            });
        }

        // Restrict pengawas siber to their own issues
        if (Auth::user()->isPengawasSiber()) {
            $query->where('input_by', Auth::id());
        }

        $issues     = $query->paginate(15)->withQueryString();
        $kabupaten  = MasterData::kabupaten()->orderBy('name')->get();
        $komoditas  = MasterData::komoditas()->orderBy('name')->get();

        return view('issues.index', compact('issues', 'kabupaten', 'komoditas'));
    }

    public function create()
    {
        $komoditas = MasterData::komoditas()->orderBy('name')->get();
        $kabupaten = MasterData::kabupaten()->orderBy('name')->get();
        $pasar     = MasterData::pasar()->orderBy('name')->get();
        return view('issues.create', compact('komoditas', 'kabupaten', 'pasar'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal_temuan'    => 'required|date',
            'platform'          => 'required|in:Twitter/X,Facebook,Instagram,TikTok,YouTube,WhatsApp,Online News,Lainnya',
            'link_postingan'    => 'nullable|url',
            'nama_akun'         => 'nullable|string|max:255',
            'jumlah_interaksi'  => 'required|integer|min:0',
            'jumlah_mention'    => 'nullable|integer|min:0',
            'kutipan_postingan' => 'required|string',
            'komoditas_id'      => 'required|exists:master_data,id',
            'kategori_isu'      => 'required|string|max:100',
            'pasar_id'          => 'nullable|exists:master_data,id',
            'lokasi_spesifik'   => 'nullable|string|max:255',
            'kabupaten_id'      => 'required|exists:master_data,id',
            'catatan_input'     => 'nullable|string',
            'screenshot'        => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:10240',
        ]);

        $validated['input_by'] = Auth::id();
        $validated['status']   = 'baru';

        $issue = Issue::create($validated);

        // Upload screenshot
        if ($request->hasFile('screenshot')) {
            $path = $request->file('screenshot')->store("evidences/issue-{$issue->id}", 'public');
            Evidence::create([
                'issue_id'    => $issue->id,
                'uploaded_by' => Auth::id(),
                'jenis'       => 'screenshot',
                'tahap'       => 'input',
                'file_path'   => $path,
                'file_name'   => $request->file('screenshot')->getClientOriginalName(),
                'file_type'   => $request->file('screenshot')->getClientMimeType(),
                'file_size'   => $request->file('screenshot')->getSize(),
            ]);
        }

        // Log
        IssueLog::create([
            'issue_id'   => $issue->id,
            'user_id'    => Auth::id(),
            'action'     => 'Isu Dibuat',
            'status_dari'=> null,
            'status_ke'  => 'baru',
            'deskripsi'  => "Isu baru dibuat oleh " . Auth::user()->name,
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('issues.show', $issue)
            ->with('success', "Isu {$issue->kode_isu} berhasil disimpan.");
    }

    public function show(Issue $issue)
    {
        if (Auth::user()->isPimpinan()) {
            return redirect()->route('dashboard')->with('info', 'Akses Pimpinan difokuskan pada Ringkasan Eksekutif dan Tren Perkembangan Harga.');
        }

        $issue->load([
            'komoditas', 'kabupaten', 'pasar', 'inputBy', 'verifikasiBy', 'assignBy',
            'verifications.verifikator',
            'assignments.petugas', 'assignments.assignedBy',
            'interventions.dibuatBy',
            'resolution.ditutupBy',
            'evidences.uploadedBy',
            'logs.user',
        ]);

        return view('issues.show', compact('issue'));
    }

    public function edit(Issue $issue)
    {
        // Only editable if 'baru' and by the original input user or admin/super_admin
        if ($issue->status !== 'baru' && !Auth::user()->isRole(['admin', 'super_admin'])) {
            return back()->with('error', 'Isu tidak dapat diedit karena sudah diproses.');
        }

        $komoditas = MasterData::komoditas()->orderBy('name')->get();
        $kabupaten = MasterData::kabupaten()->orderBy('name')->get();
        $pasar     = MasterData::pasar()->orderBy('name')->get();

        return view('issues.edit', compact('issue', 'komoditas', 'kabupaten', 'pasar'));
    }

    public function update(Request $request, Issue $issue)
    {
        $validated = $request->validate([
            'tanggal_temuan'    => 'required|date',
            'platform'          => 'required',
            'link_postingan'    => 'nullable|url',
            'nama_akun'         => 'nullable|string|max:255',
            'jumlah_interaksi'  => 'required|integer|min:0',
            'jumlah_mention'    => 'nullable|integer|min:0',
            'kutipan_postingan' => 'required|string',
            'komoditas_id'      => 'required|exists:master_data,id',
            'kategori_isu'      => 'required|string|max:100',
            'pasar_id'          => 'nullable|exists:master_data,id',
            'lokasi_spesifik'   => 'nullable|string|max:255',
            'kabupaten_id'      => 'required|exists:master_data,id',
            'catatan_input'     => 'nullable|string',
        ]);

        $issue->update($validated);

        IssueLog::create([
            'issue_id'  => $issue->id,
            'user_id'   => Auth::id(),
            'action'    => 'Isu Diperbarui',
            'deskripsi' => "Data isu diperbarui oleh " . Auth::user()->name,
            'ip_address'=> request()->ip(),
        ]);

        return redirect()->route('issues.show', $issue)->with('success', 'Isu berhasil diperbarui.');
    }

    public function destroy(Issue $issue)
    {
        $issue->delete();
        return redirect()->route('issues.index')->with('success', 'Isu berhasil dihapus.');
    }
}
