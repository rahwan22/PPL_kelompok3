<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MataPelajaran; 
use App\Models\Kelas;
use App\Models\Guru;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class GuruController extends Controller
{
    public function index()
    {
        $gurus = Guru::all();
        return view('guru.index', compact('gurus'));
    }

    public function create()
    {
        $mapel = MataPelajaran::all(); // Ambil semua data Mata Pelajaran
        $kelas = Kelas::all();
        $gurus = Guru::all();

        // Kirim $mapel dan $kelas ke view.
        // Kirim $guru sebagai null atau objek kosong untuk menghindari error 'Undefined variable $guru' di form create.
        return view('guru.create', [
            'mapel' => $mapel,
            'kelas' => $kelas,
            'guru' => null, // Dummy untuk form create
        ]);
    }

    // public function store(Request $request)
    // {
    //     // 1. Validasi Input
    //     $request->validate([
    //         'nama' => 'required|string|max:100',
    //         'email' => 'required|email|unique:users,email', // Pastikan email unik di tabel users
    //         'nip' => 'nullable|string|max:20|unique:guru,nip',
    //         'jenis_kelamin' => ['nullable', Rule::in(['L', 'P'])],
    //         'id_mapel' => 'required|exists:mata_pelajaran,id_mapel',
    //         'id_kelas_wali' => 'nullable|unique:guru,id_kelas_wali|exists:kelas,id_kelas', // Validasi Wali Kelas
    //         'no_hp' => 'nullable|string|max:20',
    //         'alamat' => 'nullable|string',
         
    //         'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // 2MB
    //     ]);

    //     // Gunakan transaksi untuk memastikan kedua operasi berhasil atau gagal bersamaan
    //     try {
    //         DB::beginTransaction();

    //         // =======================================================
    //         // TAHAP 1: BUAT AKUN USER
    //         // =======================================================
    //         $user = User::create([
    //             'nama' => $request->nama,
    //             'nip' => $request->nip,
    //             'email' => $request->email,
    //             'password' => Hash::make('12345'), // Password default, bisa diubah
    //             'role' => 'guru',
    //             'status_aktif' => true,
    //         ]);

    //         // =======================================================
    //         // TAHAP 2: BUAT PROFIL GURU menggunakan ID User yang baru
    //         // =======================================================
    //         $path_foto = null;
    //         if ($request->hasFile('foto')) {
    //             // Simpan file foto (sesuaikan dengan logika penyimpanan file Anda)
    //             $path_foto = $request->file('foto')->store('guru_photos', 'public'); 
    //         }

    //         Guru::create([
    //             'id_user' => $user->id_user, // **KRUSIAL**: Ambil ID dari User yang baru dibuat
    //             'id_mapel' => $request->id_mapel,
    //             'id_kelas_wali' => $request->id_kelas_wali, // Tambahkan Wali Kelas
    //             'nama' => $request->nama,
    //             'nip' => $request->nip,
    //             'jenis_kelamin' => $request->jenis_kelamin,
    //             'alamat' => $request->alamat,
    //             'no_hp' => $request->no_hp,
    //             'email' => $request->email,
    //             'foto' => $path_foto,
    //         ]);

    //         DB::commit();

    //         return redirect()->route('guru.index')->with('success', 'Data Guru berhasil ditambahkan!');

    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         // Logging error untuk debugging
    //         \Log::error("Gagal menyimpan data guru: " . $e->getMessage());
            
    //         return back()->withInput()->with('error', 'Gagal menyimpan data guru. Silakan coba lagi. Error: ' . $e->getMessage());
    //     }
    // }
    
public function store(Request $request)
{
    // 1. VALIDASI
    $request->validate([
        'nama' => 'required|string|max:100',
        'nip' => 'nullable|string|max:20|unique:guru,nip',
        'email' => 'required|email|max:100|unique:users,email',
        'jenis_kelamin' => ['nullable', Rule::in(['L', 'P'])],
        'no_hp' => 'nullable|string|max:20',
        'alamat' => 'nullable|string',
        'id_mapel' => 'required|exists:mata_pelajaran,id_mapel', // Wajib untuk pivot
        'id_kelas_wali' => 'nullable|unique:guru,id_kelas_wali|exists:kelas,id_kelas',
        'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    ]);

    // Menggunakan transaksi untuk memastikan kedua operasi (User & Guru) berhasil
    DB::beginTransaction();

    try {
        // 2. TANGANI FOTO (Jika ada)
        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('public/fotos/guru');
            $fotoPath = str_replace('public/', '', $fotoPath); // Simpan path tanpa 'public/'
        }

        // 3. BUAT AKUN USER
        $user = User::create([
            'nama' => $request->nama,
            'nip' => $request->nip, // NIP juga di User untuk login
            'email' => $request->email,
            'password' => Hash::make('password123'), // Password default
            'role' => 'guru',
        ]);

        // 4. BUAT DATA GURU (Tabel 'guru' tidak lagi menyimpan id_mapel)
        $guru = Guru::create([
            'id_user' => $user->id_user,
            'nip' => $request->nip,
            'nama' => $request->nama,
            'jenis_kelamin' => $request->jenis_kelamin,
            'alamat' => $request->alamat,
            'no_hp' => $request->no_hp,
            'email' => $request->email,
            'foto' => $fotoPath,
            'id_kelas_wali' => $request->id_kelas_wali,
            // id_mapel TIDAK LAGI DISIMPAN DI SINI
        ]);

        // 5. SIMPAN DATA PENGAJARAN (Tabel pivot: guru_mapel_kelas)
        // Kita perlu tahu di kelas mana guru ini mengajar mapel ini.
        // Karena form Anda tidak menyertakan input id_kelas (kelas yang diajar), 
        // kita asumsikan guru ini akan mengajar mata pelajaran ini di kelas walinya. 
        // Jika tidak ada kelas wali, ini akan gagal. Anda perlu menambahkan input 'id_kelas_ajar' di form.
        
        // Asumsi: Guru mengajar mapel di KELAS WALI-nya (ini mungkin salah secara logika bisnis)
        // --- ATAU YANG LEBIH BAIK: TAMBAHKAN INPUT id_kelas_ajar DI FORM ---
        
        // SOLUSI SEMENTARA (Mengikuti skema pivot yang logis):
        // Jika id_kelas_wali terisi, gunakan itu sebagai kelas ajar:
        if ($request->filled('id_kelas_wali')) {
            DB::table('guru_mapel_kelas')->insert([
                'id_guru' => $guru->id_guru,
                'id_mapel' => $request->id_mapel,
                'id_kelas' => $request->id_kelas_wali, // Menggunakan kelas wali sebagai kelas ajar
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
             // Jika id_mapel required, dan tidak ada id_kelas, ini menimbulkan masalah.
             // Anda HARUS MENGUBAH FORM untuk memasukkan id_kelas_ajar jika id_mapel wajib.
             // Untuk sementara, kita lewati.
        }
        
        DB::commit();

        return redirect()->route('guru.index')->with('success', 'Data Guru berhasil ditambahkan.');

    } catch (\Exception $e) {
        DB::rollBack();
        // Hapus file foto jika transaksi gagal
        if ($fotoPath) {
            Storage::delete('public/' . $fotoPath);
        }
        return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data guru. Error: ' . $e->getMessage());
    }
}

    public function edit(Guru $guru)
    {
        $mapel = MataPelajaran::all();
        $kelas = Kelas::all();
        $gurus = Guru::all();
        
        
        return view('guru.edit', compact('guru', 'mapel', 'kelas'))->with('title', 'Edit Guru');
    }

    public function update(Request $request, Guru $guru)
    {
        // Validasi Email dan NIP mengabaikan data guru saat ini
        $request->validate([
            'nama' => 'required|string|max:100',
            'nip' => 'nullable|string|max:20|unique:guru,nip,' . $guru->id_guru . ',id_guru',
            // Perhatikan validasi email untuk user/guru, memastikan unik kecuali untuk dirinya sendiri
            'email' => [
                'required',
                'email',
                'unique:guru,email,' . $guru->id_guru . ',id_guru',
                'unique:users,email,' . ($guru->id_user ?? 'null') . ',id_user',
            ],
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'required|string|max:100',
            'no_hp' => 'required|string|max:100',
            
            // PERBAIKAN: Mengganti 'mapel' menjadi 'id_mapel'
            'id_mapel' => 'required|exists:mata_pelajaran,id_mapel',
            
            // Tambahkan validasi id_kelas_wali
            'id_kelas_wali' => [
                'nullable', 
                'exists:kelas,id_kelas',
                Rule::unique('guru', 'id_kelas_wali')->ignore($guru->id_guru, 'id_guru'),
            ],

            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $newFotoPath = $guru->foto;

        // LOGIKA UPDATE FOTO
        if ($request->hasFile('foto')) {
            try {
                // 1. Hapus foto lama jika ada
                if ($guru->foto) {
                    // $guru->foto seharusnya sudah bersih dari 'public/'
                    Storage::delete('public/' . $guru->foto);
                }
            
                // 2. Simpan foto baru dan bersihkan path sebelum disimpan ke DB
                $tempPath = $request->file('foto')->store('guru_photos', 'public');
                $newFotoPath = str_replace('public/', '', $tempPath);

            } catch (\Exception $e) {
                return redirect()->back()->withInput()->with('error', 'Gagal menyimpan file ke disk: ' . $e->getMessage());
            }
        }

        DB::beginTransaction();
        try {
            $oldEmail = $guru->email;

            $guru->update([
                'nama' => $request->nama,
                'nip' => $request->nip ?? null,
                'email' => $request->email,
                'jenis_kelamin' => $request->jenis_kelamin,
                'alamat'=>$request->alamat,
                'no_hp'=> $request->no_hp,
                
                // PERBAIKAN: Mengganti 'mapel' menjadi 'id_mapel'
                'id_mapel' => $request->id_mapel, 
                'id_kelas_wali' => $request->id_kelas_wali, // Tambahkan Wali Kelas

                'foto' => $newFotoPath,
            ]);

            // Update user account if exists
            $user = null;
            if (Schema::hasColumn('guru', 'id_user') && $guru->id_user) {
                $user = User::find($guru->id_user);
            } else {
                $user = User::where('email', $oldEmail)->first();
            }

            if ($user) {
                $user->nama = $request->nama;
                $user->nip = $request->nip ?? null;
                $user->email = $request->email;
                $user->save();
            }

            DB::commit();
            return redirect()->route('guru.index')->with('success', 'Data guru berhasil diperbarui!');
        } catch (\Throwable $e) {
            DB::rollBack();
            // Jika transaksi DB gagal, Anda mungkin ingin menghapus foto baru yang tersimpan di disk
            if ($request->hasFile('foto') && $newFotoPath !== $guru->getOriginal('foto')) {
                Storage::delete('public/' . $newFotoPath);
            }
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, Guru $guru)
    {
        DB::beginTransaction();
        
        $oldFotoPath = $guru->foto; // Ambil path foto sebelum dihapus
        
        try {
            $deleteUser = $request->input('delete_user', '0') === '1';

            $user = null;
            if (Schema::hasColumn('guru', 'id_user') && $guru->id_user) {
                $user = User::find($guru->id_user);
            } elseif ($guru->email) {
                $user = User::where('email', $guru->email)->first();
            }

            // Hapus data guru dari database
            $guru->delete();

            // Hapus foto dari disk
            if ($oldFotoPath) {
                Storage::delete('public/' . $oldFotoPath);
            }

            // Hapus user jika diminta
            if ($deleteUser && $user) {
                $user->delete();
            }

            DB::commit();
            return redirect()->route('guru.index')->with('success', 'Data guru berhasil dihapus!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }

    public function show($id_guru)
    {
        $guru = Guru::findOrFail($id_guru); // Cari guru berdasarkan ID

        return view('guru.show', compact('guru')); // Kirim data ke view baru
    }
    
    public function laporanguru()
    {
        return view('guru.laporanGuruKelas1');
    }
}