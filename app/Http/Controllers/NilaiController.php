<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Nilai;
use App\Models\Siswa;
use App\Models\MataPelajaran;
use App\Models\Kelas;

class NilaiController extends Controller
{
    // ✅ Tampilkan semua data nilai
    // ✅ Tampilkan semua data nilai
    public function index()
    {
        // 🚨 PERBAIKAN: Eager load relasi 'kelas' agar nama kelas bisa ditampilkan di index
        $nilai = Nilai::with(['siswa', 'mapel', 'kelas'])->get();
        return view('nilai.index', compact('nilai'));
    }

    public function create()
    {
        $siswa = Siswa::all();
        $mapel = MataPelajaran::all();
        $kelas = Kelas::all(); 

        return view('nilai.create', compact('siswa', 'mapel', 'kelas')); 
    }

    // ✅ Simpan data nilai
    public function store(Request $request)
    {
        $request->validate([
            'nis' => 'required|exists:siswa,nis',
            'id_mapel' => 'required|exists:mata_pelajaran,id_mapel',
            // 🚨 PERBAIKAN UTAMA: Validasi id_kelas harus ke tabel 'kelas' (asumsi kolom primary key-nya adalah 'id')
            'id_kelas' => 'required|exists:kelas,id', 
            'nilai_tugas' => 'required|numeric|min:0|max:100',
            'nilai_uts' => 'required|numeric|min:0|max:100',
            'nilai_uas' => 'required|numeric|min:0|max:100',
            'semester' => 'required|string|max:10' // Menambahkan batasan string
        ]);

        $nilai_akhir = ($request->nilai_tugas + $request->nilai_uts + $request->nilai_uas) / 3;

        Nilai::create([
            'nis' => $request->nis,
            'id_mapel' => $request->id_mapel,
            'id_kelas' => $request->id_kelas,
            'nilai_tugas' => $request->nilai_tugas,
            'nilai_uts' => $request->nilai_uts,
            'nilai_uas' => $request->nilai_uas,
            'nilai_akhir' => $nilai_akhir,
            'semester' => $request->semester,
            'catatan' => $request->catatan // Pastikan kolom 'catatan' ada di fillable Model Nilai
        ]);

        return redirect()->route('nilai.index')->with('success', 'Data nilai berhasil ditambahkan!');
    }

    // ✅ Update data
    public function update(Request $request, $id)
    {
        $request->validate([
            'nis' => 'required|exists:siswa,nis',
            'id_mapel' => 'required|exists:mata_pelajaran,id_mapel',
            // 🚨 PERBAIKAN UTAMA DI UPDATE
            'id_kelas' => 'required|exists:kelas,id', 
            'nilai_tugas' => 'required|numeric|min:0|max:100',
            'nilai_uts' => 'required|numeric|min:0|max:100',
            'nilai_uas' => 'required|numeric|min:0|max:100',
            'semester' => 'required|string|max:10'
        ]);

        $nilai = Nilai::findOrFail($id);
        $nilai_akhir = ($request->nilai_tugas + $request->nilai_uts + $request->nilai_uas) / 3;

        $nilai->update([
            'nis' => $request->nis,
            'id_mapel' => $request->id_mapel,
            'id_kelas' => $request->id_kelas,
            'nilai_tugas' => $request->nilai_tugas,
            'nilai_uts' => $request->nilai_uts,
            'nilai_uas' => $request->nilai_uas,
            'nilai_akhir' => $nilai_akhir,
            'semester' => $request->semester,
            'catatan' => $request->catatan
        ]);

        return redirect()->route('nilai.index')->with('success', 'Data nilai berhasil diperbarui!');
    }

    // ✅ Hapus data
    public function destroy($id)
    {
        Nilai::findOrFail($id)->delete();
        return redirect()->route('nilai.index')->with('success', 'Data nilai berhasil dihapus!');
    }
}
