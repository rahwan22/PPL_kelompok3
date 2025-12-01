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
        // Ambil data yang mungkin diperlukan untuk dropdown (misalnya Wali Kelas)
        $mapel = MataPelajaran::all(); 
        $kelas = Kelas::all();
        
        // Kirim $mapel dan $kelas ke view 'guru.create'
        return view('guru.create', compact('mapel', 'kelas'));
    }

    
    public function store(Request $request)
    {
        // 1. VALIDASI DATA DASAR GURU (id_mapel TIDAK diwajibkan)
        $request->validate([
            'nama' => 'required|string|max:100',
            'nip' => 'nullable|string|max:20|unique:guru,nip',
            'email' => 'required|email|max:100|unique:users,email',
            'jenis_kelamin' => ['nullable', Rule::in(['L', 'P'])],
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            
            // id_mapel dihilangkan dari validasi karena diisi terpisah
            // id_kelas_wali tetap opsional dan unik jika diisi
            'id_kelas_wali' => 'nullable|unique:guru,id_kelas_wali|exists:kelas,id_kelas',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        DB::beginTransaction();

        try {
            // 2. TANGANI FOTO
            $fotoPath = null;
            if ($request->hasFile('foto')) {
                $fotoPath = $request->file('foto')->store('public/fotos/guru');
                $fotoPath = str_replace('public/', '', $fotoPath);
            }

            // 3. BUAT AKUN USER
            $user = User::create([
                'nama' => $request->nama,
                'nip' => $request->nip,
                'email' => $request->email,
                'password' => Hash::make('password123'), // Default Password
                'role' => 'guru',
            ]);

            // 4. BUAT DATA GURU
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
            ]);

            // 5. LOGIKA PIVOT DIHILANGKAN
            // Alokasi mata pelajaran dan kelas ajar dilakukan terpisah.
            
            DB::commit();

            return redirect()->route('guru.index')->with('success', 'Data Guru berhasil ditambahkan. Anda dapat mengalokasikan Mata Pelajaran dan Kelas Ajar pada menu terpisah.');

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
        
        // Kirim variabel yang diperlukan ke view 'guru.edit'
        return view('guru.edit', compact('guru', 'mapel', 'kelas'))->with('title', 'Edit Guru');
    }

    public function update(Request $request, Guru $guru)
    {
        // 1. Validasi
        $request->validate([
            'nama' => 'required|string|max:100',
            // Pengecekan unique NIP, mengabaikan data guru yang sedang diedit
            'nip' => 'nullable|string|max:20|unique:guru,nip,' . $guru->id_guru . ',id_guru',
            'email' => [
                'required',
                'email',
                // Pengecekan unique email di tabel guru dan users
                Rule::unique('guru', 'email')->ignore($guru->id_guru, 'id_guru'),
                Rule::unique('users', 'email')->ignore($guru->id_user ?? 'null', 'id_user'),
            ],
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'required|string|max:100',
            'no_hp' => 'required|string|max:100',
            
            // id_mapel dihilangkan dari validasi update
            
            'id_kelas_wali' => [
                'nullable', 
                'exists:kelas,id_kelas',
                // Pengecekan unique id_kelas_wali, mengabaikan data guru yang sedang diedit
                Rule::unique('guru', 'id_kelas_wali')->ignore($guru->id_guru, 'id_guru'),
            ],

            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $newFotoPath = $guru->foto;

        // LOGIKA UPDATE FOTO
        if ($request->hasFile('foto')) {
            try {
                if ($guru->foto) {
                    Storage::delete('public/' . $guru->foto); // Hapus foto lama
                }
                $tempPath = $request->file('foto')->store('guru_photos', 'public');
                $newFotoPath = str_replace('public/', '', $tempPath);

            } catch (\Exception $e) {
                return redirect()->back()->withInput()->with('error', 'Gagal menyimpan file ke disk: ' . $e->getMessage());
            }
        }

        DB::beginTransaction();
        try {
            $oldEmail = $guru->email;

            // 1. UPDATE TABEL GURU
            $guru->update([
                'nama' => $request->nama,
                'nip' => $request->nip ?? null,
                'email' => $request->email,
                'jenis_kelamin' => $request->jenis_kelamin,
                'alamat'=>$request->alamat,
                'no_hp'=> $request->no_hp,
                'id_kelas_wali' => $request->id_kelas_wali, 
                'foto' => $newFotoPath,
            ]);

            // 2. UPDATE TABEL USER
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

            // 3. LOGIKA PIVOT DIHILANGKAN
            
            DB::commit();
            return redirect()->route('guru.index')->with('success', 'Data guru berhasil diperbarui!');
            
        } catch (\Throwable $e) {
            DB::rollBack();
            if ($request->hasFile('foto') && $newFotoPath !== $guru->getOriginal('foto')) {
                // Hapus foto baru jika update gagal
                Storage::delete('public/' . $newFotoPath);
            }
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }


    public function destroy(Request $request, Guru $guru)
    {
        DB::beginTransaction();
        
        $oldFotoPath = $guru->foto;
        
        try {
            $deleteUser = $request->input('delete_user', '0') === '1';

            $user = null;
            if (Schema::hasColumn('guru', 'id_user') && $guru->id_user) {
                $user = User::find($guru->id_user);
            } elseif ($guru->email) {
                $user = User::where('email', $guru->email)->first();
            }

            // Hapus relasi di tabel pivot guru_mapel_kelas
            DB::table('guru_mapel_kelas')->where('id_guru', $guru->id_guru)->delete();
            
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
        $guru = Guru::findOrFail($id_guru);
        return view('guru.show', compact('guru'));
    }
    
    public function laporanguru()
    {
        return view('guru.laporanGuruKelas1');
    }
}