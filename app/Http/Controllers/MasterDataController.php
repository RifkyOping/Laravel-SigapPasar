<?php

namespace App\Http\Controllers;

use App\Models\MasterData;
use Illuminate\Http\Request;

class MasterDataController extends Controller
{
    public function index(Request $request)
    {
        $type  = $request->get('type', 'komoditas');
        $items = MasterData::where('type', $type)->orderBy('name')->paginate(20);
        return view('master.index', compact('items', 'type'));
    }

    public function create()
    {
        return view('master.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type'            => 'required|in:komoditas,kabupaten,pasar',
            'name'            => 'required|string|max:255',
            'code'            => 'nullable|string|max:50',
            'satuan'          => 'nullable|string|max:50',
            'harga_referensi' => 'nullable|numeric|min:0',
            'deskripsi'       => 'nullable|string',
        ]);

        MasterData::create($validated);
        return redirect()->route('master-data.index', ['type' => $validated['type']])
            ->with('success', 'Data master berhasil ditambahkan.');
    }

    public function edit(MasterData $masterDatum)
    {
        return view('master.edit', ['item' => $masterDatum]);
    }

    public function update(Request $request, MasterData $masterDatum)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'code'            => 'nullable|string|max:50',
            'satuan'          => 'nullable|string|max:50',
            'harga_referensi' => 'nullable|numeric|min:0',
            'deskripsi'       => 'nullable|string',
            'is_active'       => 'boolean',
        ]);

        $masterDatum->update($validated);
        return redirect()->route('master-data.index', ['type' => $masterDatum->type])
            ->with('success', 'Data master berhasil diperbarui.');
    }

    public function destroy(MasterData $masterDatum)
    {
        $masterDatum->update(['is_active' => false]);
        return back()->with('success', 'Data master berhasil dinonaktifkan.');
    }
}
