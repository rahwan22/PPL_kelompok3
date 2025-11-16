<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notifikasi;
use App\Models\Siswa;
use App\Models\Orangtua;

class NotifikasiController extends Controller
{
    // 📄 Tampilkan semua notifikasi
    public function index()
    {
       $notifikasi = Notifikasi::with(['siswa', 'orangtua']) 
                              ->orderBy('created_at', 'desc')
                              ->get();
        return view('notifikasi.index', compact('notifikasi'));
    }

    // ➕ Form tambah notifikasi
    public function create()
    {
        $siswa = Siswa::all();
        $orangtua = Orangtua::all();
        return view('notifikasi.create', compact('siswa', 'orangtua'));
    }

    // 💾 Simpan notifikasi baru
    public function store(Request $request)
    {
        $request->validate([
            'nis' => 'required|exists:siswa,nis',
            'id_orangtua' => 'required|exists:orangtua,id_orangtua',
            'pesan' => 'required|string|max:255',
        ]);

        Notifikasi::create([
            'nis' => $request->nis,
            'id_orangtua' => $request->id_orangtua,
            'pesan' => $request->pesan,
            'status' => 'Belum Dibaca',
        ]);

        return redirect()->route('notifikasi.index')->with('success', 'Notifikasi berhasil dikirim!');
    }

    // ✏️ Form edit notifikasi
    public function edit($id)
    {
        $notifikasi = Notifikasi::findOrFail($id);
        $siswa = Siswa::all();
        $orangtua = Orangtua::all();
        return view('notifikasi.edit', compact('notifikasi', 'siswa', 'orangtua'));
    }

    // 🔁 Update notifikasi
    public function update(Request $request, $id)
    {
        $request->validate([
            'pesan' => 'required|string|max:255',
            'status' => 'required|in:Dibaca,Belum Dibaca',
        ]);

        $notifikasi = Notifikasi::findOrFail($id);
        $notifikasi->update([
            'pesan' => $request->pesan,
            'status' => $request->status,
        ]);

        return redirect()->route('notifikasi.index')->with('success', 'Notifikasi berhasil diperbarui!');
    }

    // ❌ Hapus notifikasi
    public function destroy($id)
    {
        $notifikasi = Notifikasi::findOrFail($id);
        $notifikasi->delete();

        return redirect()->route('notifikasi.index')->with('success', 'Notifikasi berhasil dihapus!');
    }
}
