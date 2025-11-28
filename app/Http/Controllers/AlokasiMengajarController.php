<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\GuruMapelKelas;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * Controller ini menangani logika Alokasi Mengajar.
 * - Admin: Mengelola (Create, Read All, Delete) alokasi.
 * - Guru: Melihat jadwal pengajarannya sendiri.
 */
class AlokasiMengajarController extends Controller
{
    /**
     * [ADMIN & GURU] Menampilkan daftar alokasi pengajaran.
     * Admin melihat semua alokasi. Guru hanya melihat alokasi miliknya.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Eager load relasi guru, mataPelajaran, dan kelas dari tabel pivot
        $query = GuruMapelKelas::with(['guru', 'mataPelajaran', 'kelas']);
        
        $guru = null; // Inisialisasi variabel guru
        $isGuruRole = ($user->role === 'guru'); 

        // Filter data jika user adalah 'guru'
        if ($isGuruRole) {
            // Cari data Guru (dari tabel 'guru') yang terhubung dengan User yang sedang login
            $guru = Guru::where('id_user', $user->id_user)->first(); 
            
            if ($guru) {
                // Filter hanya berdasarkan ID Guru yang login
                $query->where('id_guru', $guru->id_guru); 
            } else {
                // Fallback jika data guru belum sinkron di tabel 'guru'
                $guru = (object)['nama' => $user->nama ?? 'User Guru']; 
            }
        }

        // Eksekusi Query
        $alokasi = $query->get();
        
        // Grouping Data berdasarkan ID Guru (berguna untuk tampilan Admin)
        $alokasiGrouped = $alokasi->groupBy('id_guru'); 
        
        return view('alokasi.index', compact('alokasi', 'alokasiGrouped', 'guru', 'isGuruRole'));
    }
    
    /**
     * [ADMIN] Menampilkan formulir untuk membuat alokasi pengajaran baru.
     */
    public function create()
{
    // Ambil semua data master untuk dropdown
    $guru = Guru::all();
    $mapels = MataPelajaran::all();
    $kelas = Kelas::all(); // Semua kelas

    // 🌟 Tambahkan ini: Ambil kombinasi id_mapel dan id_kelas yang sudah terpakai
    $allocatedCombinations = GuruMapelKelas::select('id_mapel', 'id_kelas')
                                           ->distinct() // Ambil hanya kombinasi unik
                                           ->get()
                                           ->toArray();

    // Kirim data master dan data alokasi ke view
    return view('alokasi.create', compact('guru', 'mapels', 'kelas', 'allocatedCombinations'));
}
    
    /**
     * [ADMIN] Menyimpan alokasi pengajaran baru.
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $validatedData = $request->validate([
            'id_guru' => 'required|exists:guru,id_guru',
            'id_mapel' => 'required|exists:mata_pelajaran,id_mapel',
            'id_kelas' => 'required|exists:kelas,id_kelas',
        ], [
            'required' => ':attribute wajib diisi.',
            'exists' => ':attribute tidak valid.',
        ]);

        // 2. Cek Logika Bisnis: Satu Mapel di satu Kelas hanya boleh diajar oleh satu Guru.
        $existingAllocation = GuruMapelKelas::where('id_mapel', $request->id_mapel)
                                                ->where('id_kelas', $request->id_kelas)
                                                ->first();
                                                
        if ($existingAllocation) {
            $existingAllocation->load('guru');
            
            $namaGuru = $existingAllocation->guru->nama ?? 'Guru Lain'; 
            $namaMapel = MataPelajaran::find($request->id_mapel)->nama_mapel ?? 'Mata Pelajaran';
            $namaKelas = Kelas::find($request->id_kelas)->nama_kelas ?? 'Kelas';

            return redirect()->back()->withInput()
                ->withErrors([
                    'id_mapel' => "Mata pelajaran **{$namaMapel}** di **{$namaKelas}** sudah dialokasikan kepada **{$namaGuru}**. Satu mata pelajaran di satu kelas hanya boleh diajar oleh satu guru."
                ]);
        }

        // 3. Simpan Data
        GuruMapelKelas::create($validatedData);

        // 4. Redirect dengan pesan sukses
        return redirect()->route('alokasi.index')->with('success', 'Alokasi pengajaran berhasil ditambahkan!');
    }

    /**
     * [ADMIN] Menghapus alokasi pengajaran tertentu.
     */
    public function destroy($id)
    {
        $alokasi = GuruMapelKelas::findOrFail($id);
        $alokasi->delete();

        return redirect()->route('alokasi.index')->with('success', 'Alokasi pengajaran berhasil dihapus.');
    }

    /**
     * [GURU] Menampilkan jadwal mengajar spesifik untuk Guru yang sedang login.
     */
    public function jadwalGuru()
    {
        $user = Auth::user();
        // Cari data Guru di tabel 'guru' berdasarkan id_user yang login
        $guru = Guru::where('id_user', $user->id_user)->first(); 
        
        if (!$guru) {
            // Case 1: Guru tidak ditemukan di tabel 'guru' (belum sinkron)
            $jadwal = collect([]);
            // Fallback nama untuk ditampilkan di view
            $guru = (object)['nama' => $user->nama ?? 'Guru Belum Sinkron']; 
        } else {
            // Case 2: Guru ditemukan, ambil jadwalnya
            
            // Menggunakan eager loading: 'mataPelajaran' dan 'kelas'
            $jadwal = GuruMapelKelas::with(['mataPelajaran', 'kelas'])
                                    ->where('id_guru', $guru->id_guru) 
                                    ->get();
        }

        // Kirim $jadwal dan $guru ke view
        return view('alokasi.jadwal_guru', compact('jadwal', 'guru')); 
    }
}