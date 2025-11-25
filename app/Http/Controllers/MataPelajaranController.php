<?php

namespace App\Http\Controllers;

use App\Models\MataPelajaran;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MataPelajaranController extends Controller
{
    /**
     * Menampilkan daftar semua Mata Pelajaran (Index).
     */
    public function index()
    {
        // Ambil semua data mata pelajaran dan urutkan berdasarkan kode
        $mapels = MataPelajaran::orderBy('kode_mapel')->get();

        return view('mapel.index', compact('mapels'));
    }
//     public function index()
// {
//     // Mengambil semua data mata pelajaran
//     $allMapels = MataPelajaran::all();

//     // Mengelompokkan data berdasarkan 'nama_mapel'
//     // Setiap group akan berisi koleksi mapel yang memiliki nama yang sama
//     $groupedMapels = $allMapels->groupBy('nama_mapel');

//     // Mengirim data yang sudah dikelompokkan ke view
//     return view('mapel.index', compact('groupedMapels'));
// }

    /**
     * Menampilkan form untuk membuat Mata Pelajaran baru (Create).
     * (Anda belum meminta ini, tapi ini adalah pasangan alami dari Store)
     */
    public function create()
    {
        // Data untuk dropdown tingkat (sesuai migrasi: 1 sampai 6)
        $tingkat = range(1, 6);
        return view('mapel.create', compact('tingkat'));
    }

    /**
     * Menyimpan data Mata Pelajaran baru ke database (Store).
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_mapel' => 'required|string|max:100',
            'kode_mapel' => 'required|string|max:20|unique:mata_pelajaran,kode_mapel',
            'tingkat' => ['nullable', Rule::in(range(1, 6))],
        ]);

        MataPelajaran::create($request->all());

        return redirect()->route('mapel.index')->with('success', 'Mata Pelajaran berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail Mata Pelajaran tertentu (Show).
     */
    public function show(MataPelajaran $mapel)
    {
        // Untuk menampilkan detail pengajar, kita bisa memuat relasi pivot
        $mapel->load(['guruMengajar.user', 'guruMengajar.kelasWali']);
        
        return view('mapel.show', compact('mapel'));
    }

    /**
     * Menampilkan form untuk mengedit Mata Pelajaran (Edit).
     */
    public function edit(MataPelajaran $mapel)
    {
        // Data untuk dropdown tingkat (sesuai migrasi: 1 sampai 6)
        $tingkat = range(1, 6);
        return view('mapel.edit', compact('mapel', 'tingkat'));
    }

    /**
     * Memperbarui data Mata Pelajaran di database (Update).
     */
    public function update(Request $request, MataPelajaran $mapel)
    {
        $request->validate([
            'nama_mapel' => 'required|string|max:100',
            // Pastikan kode_mapel unik kecuali untuk mapel yang sedang di-edit
            'kode_mapel' => ['required', 'string', 'max:20', Rule::unique('mata_pelajaran', 'kode_mapel')->ignore($mapel->id_mapel, 'id_mapel')],
            'tingkat' => ['nullable', Rule::in(range(1, 6))],
        ]);

        $mapel->update($request->all());

        return redirect()->route('mapel.index')->with('success', 'Mata Pelajaran berhasil diperbarui.');
    }

    /**
     * Menghapus Mata Pelajaran dari database (Destroy).
     */
    public function destroy(MataPelajaran $mapel)
    {
        try {
            $mapel->delete();
            return redirect()->route('mapel.index')->with('success', 'Mata Pelajaran berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('mapel.index')->with('error', 'Gagal menghapus Mata Pelajaran. Ada data lain yang terkait.');
        }
    }
}