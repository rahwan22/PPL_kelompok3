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
    public function index()
    {
        // Eager load relasi 'kelas' agar nama kelas bisa ditampilkan
        $nilai = Nilai::with(['siswa', 'mapel', 'kelas'])->get();
        return view('nilai.index', compact('nilai'));
    }
    public function nilaiSiswaByAdmin($nis)
    {
        // 1. Ambil data Siswa untuk memastikan siswa ada dan menampilkan nama
        $siswa = Siswa::where('nis', $nis)->firstOrFail();

        // 2. Ambil semua data nilai siswa tersebut
        // Eager load relasi 'mapel' dan 'kelas' agar nama-namanya muncul di view
        $data_nilai = Nilai::where('nis', $nis)
                        ->with(['mapel', 'kelas']) 
                        ->orderBy('semester', 'asc') // Urutkan berdasarkan semester
                        ->get();

        // 3. Kirim data ke view baru
        return view('nilai.admin_show_siswa', compact('siswa', 'data_nilai'));
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
            // ✅ PERBAIKAN UTAMA: Validasi ke 'kelas,id_kelas'
            'id_kelas' => 'required|exists:kelas,id_kelas', 
            'nilai_tugas' => 'required|numeric|min:0|max:100',
            'nilai_uts' => 'required|numeric|min:0|max:100',
            'nilai_uas' => 'required|numeric|min:0|max:100',
            'catatan' => 'nullable|string|max:1000', // Catatan bisa kosong (nullable)
            'semester' => 'required|string|max:10',
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
            'catatan' => $request->catatan,
            'semester' => $request->semester,
        ]);

        return redirect()->route('nilai.index')->with('success', 'Data nilai berhasil ditambahkan!');
}
        // ⭐ FUNGSI BARU: Tampilkan form edit data nilai
        public function edit($id)
        {
            // 1. Ambil data nilai yang akan di edit (termasuk relasinya)
            $nilai = Nilai::findOrFail($id);
            
            // 2. Ambil data pendukung untuk dropdown (Siswa, MataPelajaran, Kelas)
            $siswa = Siswa::all();
            $mapel = MataPelajaran::all();
            $kelas = Kelas::all(); 

            // 3. Kirim semua data ke view 'nilai.edit'
            return view('nilai.edit', compact('nilai', 'siswa', 'mapel', 'kelas'));
        }

    // ✅ Update data
    public function update(Request $request, $id)
    {
        $request->validate([
            'nis' => 'required|exists:siswa,nis',
            'id_mapel' => 'required|exists:mata_pelajaran,id_mapel',
            // ✅ PERBAIKAN UTAMA: Validasi ke 'kelas,id_kelas'
            'id_kelas' => 'required|exists:kelas,id_kelas', 
            'nilai_tugas' => 'required|numeric|min:0|max:100',
            'nilai_uts' => 'required|numeric|min:0|max:100',
            'nilai_uas' => 'required|numeric|min:0|max:100',
            'catatan' => 'nullable|string|max:1000',
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
            'catatan' => $request->catatan,
            'semester' => $request->semester
        ]);

        return redirect()->route('nilai.index')->with('success', 'Data nilai berhasil diperbarui!');
    }

    // ✅ Hapus data
    public function destroy($id)
    {
        // Menggunakan id_nilai sebagai primary key
        Nilai::findOrFail($id)->delete(); 
        return redirect()->route('nilai.index')->with('success', 'Data nilai berhasil dihapus!');
    }
    public function show($id)
    {
        // Cari data nilai berdasarkan $id, dan lakukan Eager Loading
        // 1. Ambil data Siswa untuk memastikan siswa ada dan menampilkan nama
    ;
        // untuk relasi 'siswa', 'mapel', dan 'kelas'
        $nilai = Nilai::with(['siswa', 'mapel', 'kelas'])->findOrFail($id);
        
        // Kirim data ke view 'nilai.show'
        return view('nilai.show', compact('nilai'));
    }
}