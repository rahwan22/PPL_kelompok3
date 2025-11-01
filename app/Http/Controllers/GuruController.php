<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Guru;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
            'mapel' => 'nullable|string|max:100'
        ]);

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
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function edit(Guru $guru)
    {
        return view('guru.edit', compact('guru'))->with('title', 'Edit Guru');
    }

    public function update(Request $request, Guru $guru)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'nip' => 'nullable|string|max:20|unique:guru,nip,' . $guru->id_guru . ',id_guru',
            'email' => 'required|email|unique:users,email,' . ($guru->id_user ?? 'null') . ',id_user|unique:guru,email,' . $guru->id_guru . ',id_guru',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'required|string|max:100',
            'no_hp' => 'required|string|max:100',
            'mapel' => 'nullable|string|max:100'
        ]);

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
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, Guru $guru)
    {
        DB::beginTransaction();
        try {
            $deleteUser = $request->input('delete_user', '0') === '1';

            $user = null;
            if (Schema::hasColumn('guru', 'id_user') && $guru->id_user) {
                $user = User::find($guru->id_user);
            } elseif ($guru->email) {
                $user = User::where('email', $guru->email)->first();
            }

            $guru->delete();

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
}
