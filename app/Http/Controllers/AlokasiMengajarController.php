<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\GuruMapelKelas;

use Illuminate\Support\Facades\DB;

class AlokasiMengajarController extends Controller
{
    /**
     * Tampilkan daftar alokasi pengajaran.
     * Dapat diakses oleh Admin/Kepala Sekolah.
     */
    public function index()
    {
        // Ambil data alokasi pengajaran dengan eager loading
        $alokasi = GuruMapelKelas::with(['guru', 'mataPelajaran', 'kelas'])
            ->orderBy('id_guru')
            ->get();

        return view('alokasi.index', compact('alokasi'));
    }

    /**
     * Tampilkan form untuk membuat alokasi baru.
     */
    public function create()
    {
        $gurus = Guru::all();
        $mapels = MataPelajaran::all();
        $kelas = Kelas::all();
        
        return view('alokasi.create', compact('gurus', 'mapels', 'kelas'));
    }

    /**
     * Simpan alokasi pengajaran baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_guru' => 'required|exists:guru,id_guru',
            'id_mapel' => 'required|exists:mata_pelajaran,id_mapel',
            'id_kelas' => 'required|exists:kelas,id_kelas',
        ]);

        try {
            GuruMapelKelas::create([
                'id_guru' => $request->id_guru,
                'id_mapel' => $request->id_mapel,
                'id_kelas' => $request->id_kelas,
            ]);
        } catch (\Exception $e) {
            // Tangani error duplikasi (karena ada unique constraint)
            if (str_contains($e->getMessage(), 'unique_pengajaran')) {
                return redirect()->route('alokasi.create')
                    ->with('error', 'Guru tersebut sudah mengajar Mata Pelajaran ini di Kelas yang sama.');
            }
            return redirect()->route('alokasi.create')
                ->with('error', 'Gagal menyimpan alokasi: ' . $e->getMessage());
        }

        return redirect()->route('alokasi.index')
            ->with('success', 'Alokasi pengajaran berhasil ditambahkan.');
    }

    /**
     * Hapus alokasi pengajaran.
     */
    public function destroy(GuruMapelKelas $alokasi)
    {
        $alokasi->delete();

        return redirect()->route('alokasi.index')
            ->with('success', 'Alokasi pengajaran berhasil dihapus.');
    }
    
    // ---------------------------------------------------------------------
    // Tambahan: Fungsi untuk Guru melihat jadwal mengajar mereka sendiri
    // ---------------------------------------------------------------------

    /**
     * Tampilkan jadwal mengajar spesifik untuk Guru yang sedang login.
     * Dapat diakses oleh role 'guru'.
     * Asumsi: User ID (Auth::id()) terhubung ke tabel Guru
     */
    public function jadwalGuru()
    {
        // Asumsi: Anda memiliki Auth guard dan model User terhubung ke Guru
        $user = auth()->user();
        
        if (!$user || $user->role !== 'guru') {
            return redirect('/dashboard')->with('error', 'Akses ditolak.');
        }

        // Ambil ID Guru berdasarkan ID User yang login
        $guru = Guru::where('id_user', $user->id_user)->first();

        if (!$guru) {
            return redirect('/dashboard')->with('error', 'Profil Guru tidak ditemukan.');
        }

        $jadwal = GuruMapelKelas::where('id_guru', $guru->id_guru)
            ->with(['mataPelajaran', 'kelas'])
            ->get();

        return view('alokasi.jadwal_guru', compact('guru', 'jadwal'));
    }


    public function getAvailableKelas(Request $request)
    {
        $guruId = $request->query('guru_id');
        $mapelId = $request->query('mapel_id');

        if (!$guruId || !$mapelId) {
            return response()->json([]);
        }

        // 1. Ambil semua Kelas yang SUDAH dialokasikan untuk Mapel ini.
        $allocatedKelasIds = GuruMapelKelas::where('id_mapel', $mapelId)
                                            // Optional: Anda mungkin ingin mengecualikan alokasi yang melibatkan Guru yang sama
                                            ->pluck('id_kelas') 
                                            ->unique()
                                            ->toArray();

        // 2. Ambil semua Kelas yang TIDAK ADA dalam daftar ID yang sudah dialokasikan.
        $availableKelas = Kelas::whereNotIn('id_kelas', $allocatedKelasIds)
                               ->get(['id_kelas', 'nama_kelas']);

        return response()->json($availableKelas);
    }
}