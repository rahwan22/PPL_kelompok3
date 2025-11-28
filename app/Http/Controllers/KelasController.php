<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule; // Diperlukan untuk validasi unik di method update

class KelasController extends Controller
{
    /**
     * Menampilkan daftar semua Kelas.
     * Dapat diakses oleh semua role (admin, kepala_sekolah, guru).
     */
    // public function index()
    // {
    //     // Eager load relasi waliKelas dan siswa untuk optimasi performa
    //     $kelas = Kelas::with(['waliKelas', 'siswa'])
    //                   ->orderBy('tahun_ajaran', 'desc')
    //                   ->orderBy('nama_kelas')
    //                   ->get();
        
    //     return view('kelas.index', compact('kelas'));
    // }
    public function index()
{
    // Eager load relasi waliKelas dan siswa untuk optimasi performa
    $query = Kelas::with(['waliKelas', 'siswa'])
                ->orderBy('tahun_ajaran', 'desc')
                ->orderBy('nama_kelas');
    
    $user = Auth::user();
    
    // Cek Peran
    if ($user->role === 'guru') {
        // Cari data Guru (asumsi: 'id_user' di tabel 'guru' merujuk ke 'id' di 'users')
        $guru = Guru::where('id_user', $user->id)->first();
        
        $allowedKelasIds = collect();

        if ($guru) {
            
            // A. KELAS YANG DIWALIKAN
            if ($guru->id_kelas_wali) {
                $allowedKelasIds->push($guru->id_kelas_wali);
            }

            // B. KELAS YANG DIAJAR (Menggunakan relasi many-to-many)
            // Relasi 'kelasAjar' sudah menggunakan klausa distinct() dan withPivot().
            $kelasDiAjarIds = $guru->kelasAjar()->pluck('id_kelas');
            
            // Gabungkan semua ID kelas (Wali + Ajar) dan pastikan unik
            $allowedKelasIds = $allowedKelasIds->merge($kelasDiAjarIds)->unique();
        }
        
        // 2. Terapkan Filter pada Query Utama
        if ($allowedKelasIds->isNotEmpty()) {
            // Tampilkan kelas yang ID-nya ada di daftar allowedKelasIds
            $query->whereIn('id_kelas', $allowedKelasIds);
        } else {
            // Guru login tetapi tidak punya kelas wali maupun kelas ajar
            $query->whereRaw('1 = 0'); 
        }
    }
    // Admin dan Kepala Sekolah melihat semua kelas karena tidak ada filter yang ditambahkan.

    $kelas = $query->get();
    
    return view('kelas.index', compact('kelas'));
}

    // --- CREATE ---

    /**
     * Menampilkan form untuk membuat Kelas baru (Hanya Admin).
     */
    public function create()
    {
        // Pengecekan Akses Manual 
        if (Auth::check() && Auth::user()->role !== 'admin') {
            abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        // Ambil SEMUA GURU yang kolom id_kelas_wali-nya masih NULL
        $availableWalikelas = Guru::whereNull('id_kelas_wali')
                                  ->orderBy('nama')
                                  ->get();

        return view('kelas.create', compact('availableWalikelas'));
    }

    // --- STORE ---

    /**
     * Menyimpan Kelas baru ke database (Hanya Admin).
     */
    public function store(Request $request)
    {
        if (Auth::check() && Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak. Hanya Admin yang dapat menambah data kelas.');
        }

        $validated = $request->validate([
            'nama_kelas' => 'required|string|max:50|unique:kelas,nama_kelas',
            'tahun_ajaran' => 'required|string|max:50',
            // id_kelas_wali harus ada di tabel guru
            'id_kelas_wali' => 'nullable|exists:guru,id_guru', 
        ]);

        // 1. BUAT KELAS BARU
        $kelasBaru = Kelas::create([
            'nama_kelas' => $validated['nama_kelas'],
            'tahun_ajaran' => $validated['tahun_ajaran'],
        ]);

        // 2. LOGIC PENETAPAN WALI KELAS BARU (di tabel guru)
        if ($request->filled('id_kelas_wali')) {
            $idGuruWali = $request->id_kelas_wali;
            
            $guruWali = Guru::find($idGuruWali);

            if ($guruWali) {
                // Update kolom id_kelas_wali di tabel guru
                $guruWali->update([
                    'id_kelas_wali' => $kelasBaru->id_kelas 
                ]);
            }
        }

        return redirect()->route('kelas.index')->with('success', 'Kelas baru berhasil ditambahkan.');
    }

    // --- EDIT ---

    /**
     * Menampilkan form untuk mengedit Kelas (Hanya Admin).
     * Menggunakan Route Model Binding ($kela) dan Eager Loading relasi waliKelas.
     */
    public function edit(Kelas $kela)
    {
        if (Auth::check() && Auth::user()->role !== 'admin') {
            abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        // EAGER LOAD relasi waliKelas untuk memastikan $kela->waliKelas tersedia di view
        $kela->load('waliKelas');
        
        // 1. Ambil daftar guru yang tersedia (id_kelas_wali = null)
        $availableWalikelas = Guru::whereNull('id_kelas_wali')
                                  ->orderBy('nama')
                                  ->get();

        // 2. Jika kelas ini sudah memiliki wali, tambahkan wali tersebut ke daftar opsi 
        //    agar guru tersebut tetap muncul dan terpilih di dropdown
        if ($kela->waliKelas) {
            $availableWalikelas->prepend($kela->waliKelas);
        }

        // Mengirimkan $kela (data Kelas) dan $availableWalikelas (daftar Guru)
        return view('kelas.edit', compact('kela', 'availableWalikelas'));
    }


    // --- UPDATE ---

    /**
     * Memperbarui Kelas di database (Hanya Admin).
     */
    public function update(Request $request, Kelas $kela)
    {
        if (Auth::check() && Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak. Hanya Admin yang dapat mengubah data kelas.');
        }

        $validated = $request->validate([
            'nama_kelas' => [
                'required',
                'string',
                'max:50',
                // Pastikan nama kelas unik, kecuali untuk kelas yang sedang diedit
                Rule::unique('kelas')->ignore($kela->id_kelas, 'id_kelas'),
            ],
            'tahun_ajaran' => 'required|string|max:50',
            'id_kelas_wali' => 'nullable|exists:guru,id_guru',
        ]);

        // 1. UPDATE DATA KELAS (Nama dan Tahun Ajaran)
        $kela->update([
            'nama_kelas' => $validated['nama_kelas'],
            'tahun_ajaran' => $validated['tahun_ajaran'],
        ]);

        // 2. LOGIC UPDATE WALI KELAS DI TABEL GURU
        
        // a. Bersihkan Wali Kelas sebelumnya (set id_kelas_wali = NULL di guru yang lama)
        //    Ini memastikan guru tersebut bisa menjadi wali kelas lain atau tidak menjadi wali sama sekali.
        if ($kela->waliKelas) {
            $kela->waliKelas->update(['id_kelas_wali' => null]);
        }

        // b. Tetapkan Wali Kelas yang baru (set id_kelas_wali = id_kelas di guru yang baru)
        if ($request->filled('id_kelas_wali')) {
            $guruBaru = Guru::find($request->id_kelas_wali);
            if ($guruBaru) {
                $guruBaru->update(['id_kelas_wali' => $kela->id_kelas]);
            }
        }
        
        return redirect()->route('kelas.index')->with('success', 'Data Kelas berhasil diperbarui.');
    }

    // --- DESTROY ---

    /**
     * Menghapus Kelas dari database (Hanya Admin).
     */
    public function destroy(Kelas $kela)
    {
        if (Auth::check() && Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak. Hanya Admin yang dapat menghapus data kelas.');
        }
        
        // Catatan: Jika ada relasi cascade di database, relasi di tabel siswa/guru (id_kelas_wali) akan terhapus otomatis.
        // Jika tidak ada cascade: kita harus set id_kelas_wali pada guru dan id_kelas pada siswa menjadi NULL/default.
        $kela->delete();
        
        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil dihapus.');
    }

    // --- SHOW ---

    public function show($id_kelas)
    {
        $userRole = auth()->user()->role;
        
        if ($userRole !== 'admin' && $userRole !== 'kepala_sekolah' && $userRole !== 'guru') {
            abort(403, 'Akses Ditolak. Anda tidak memiliki izin untuk melihat detail kelas.');
        }
        
        // Eager Load relasi 'waliKelas' dan 'siswa'
        $kelas = Kelas::with(['waliKelas', 'siswa'])
                       ->findOrFail($id_kelas); 
        
        return view('kelas.show', compact('kelas'));
    }
}