<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MataPelajaran;

class MataPelajaranController extends Controller
{
    // 🔹 Menampilkan semua data mapel
    public function index()
    {
        $mapel = MataPelajaran::all();
        return view('mapel.index', compact('mapel'));
    }

    // 🔹 Form tambah dataya
    
    public function create()
    {
        return view('mapel.create');
    }

    // 🔹 Simpan data baru
    public function store(Request $request)
    {
        $request->validate([
            'kode_mapel' => 'required|string|max:10|unique:mata_pelajaran,kode_mapel',
            'nama_mapel' => 'required|string|max:100',
            
        ]);

        MataPelajaran::create($request->all());

        return redirect()->route('mapel.index')->with('success', 'Data mata pelajaran berhasil ditambahkan!');
    }

    // 🔹 Form edit data
    public function edit($id)
    {
        $mapel = MataPelajaran::findOrFail($id);
        return view('mapel.edit', compact('mapel'));
    }

    // 🔹 Update data
    public function update(Request $request, $id)
    {
        $mapel = MataPelajaran::findOrFail($id);

        $request->validate([
            'kode_mapel' => 'required|string|max:10|unique:mata_pelajaran,kode_mapel,' . $id . ',id_mapel',
            'nama_mapel' => 'required|string|max:100',
            
        ]);

        $mapel->update($request->all());

        return redirect()->route('mapel.index')->with('success', 'Data mata pelajaran berhasil diperbarui!');
    }

    // 🔹 Hapus data
    public function destroy($id)
    {
        $mapel = MataPelajaran::findOrFail($id);
        $mapel->delete();

        return redirect()->route('mapel.index')->with('success', 'Data mata pelajaran berhasil dihapus!');
    }
}
