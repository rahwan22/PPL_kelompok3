<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Guru;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class GuruController extends Controller
{
    public function index()
    {
        $data = Guru::latest()->paginate(10);
        return view('guru.index', compact('data'))->with('title', 'Data Guru');
    }

    public function create()
    {
        return view('guru.create')->with('title', 'Tambah Guru');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'nip' => 'nullable|string|max:20|unique:guru,nip',
            'email' => 'required|email|unique:users,email|unique:guru,email',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'required|string|max:100',
            'no_hp' => 'required|string|max:100',
            'mapel' => 'nullable|string|max:100',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            
        ]);

        $fotoPath = null;

        // 1. TANGANI UPLOAD FOTO
        if ($request->hasFile('foto')) {
            try {
                // Simpan file ke storage/app/public/guru_photos
                // $tempPath akan berisi string seperti 'public/guru_photos/xxxxxxxx.jpg'
                $tempPath = $request->file('foto')->store('guru_photos', 'public'); 
                
                // *** PERBAIKAN KRITIS DI SINI ***
                // Path yang disimpan ke DB HARUSNYA TANPA 'public/' agar konsisten dengan metode update
                // $fotoPath = str_replace('public/', '', $tempPath); 
            
            } catch (\Exception $e) {
                // Tangkap error disk, misalnya permission denied
                return redirect()->back()->withInput()->with('error', 'Gagal menyimpan file ke disk: ' . $e->getMessage());
            }
        }

        DB::beginTransaction();
        try {
            $guru = Guru::create([
                'nama' => $request->nama,
                'nip' => $request->nip ?? null,
                'email' => $request->email,
                'jenis_kelamin' => $request->jenis_kelamin,
                'alamat'=>$request->alamat,
                'no_hp'=> $request->no_hp,
                'mapel' => $request->mapel ?? null,
                'foto' => $fotoPath, // Path yang sudah bersih (misal: guru_photos/file.jpg)
            ]);

            $user = User::create([
                'nama' => $request->nama,
                'nip' => $request->nip ?? null,
                'email' => $request->email,
                'password' => Hash::make('password123'),
                'role' => 'guru',
                'status' => 'active',
            ]);

            // Link guru to user if id_user column exists
            if (Schema::hasColumn('guru', 'id_user')) {
                $guru->id_user = $user->id_user;
                $guru->save();
            }

            DB::commit();
            return redirect()->route('guru.index')->with('success', 'Data guru & akun berhasil dibuat. Password default: password123');
        } catch (\Throwable $e) {
            DB::rollBack();
            // Jika transaksi DB gagal, hapus foto yang baru di-upload (jika ada)
            if ($fotoPath) {
                Storage::delete('public/' . $fotoPath);
            }
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function edit(Guru $guru)
    {
        return view('guru.edit', compact('guru'))->with('title', 'Edit Guru');
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
            'mapel' => 'nullable|string|max:100',
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
                'mapel' => $request->mapel ?? null,
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