<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Orangtua;

class OrangtuaController extends Controller
{
    // 🔹 Tampilkan semua orang tua
    public function index()
    {
        $orangtua = Orangtua::all();
        return view('orangtua.index', compact('orangtua'));
    }

    // 🔹 Form tambah data
    public function create()
    {
        return view('orangtua.create');
    }

    // 🔹 Simpan data baru
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'email' => 'nullable|email|max:100',
            'no_wa' => 'nullable|string|max:50',
            'preferensi_notif' => 'nullable|array',
        ]);

        Orangtua::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'no_wa' => $request->no_wa,
            'preferensi_notif' => $request->preferensi_notif ? json_encode($request->preferensi_notif) : null,
        ]);

        return redirect()->route('orangtua.index')->with('success', 'Data orang tua berhasil ditambahkan!');
    }

    // 🔹 Form edit data
    public function edit($id)
    {
        $orangtua = Orangtua::findOrFail($id);
        return view('orangtua.edit', compact('orangtua'));
    }

    // 🔹 Update data
    public function update(Request $request, $id)
    {
        $orangtua = Orangtua::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:100',
            'email' => 'nullable|email|max:100',
            'no_wa' => 'nullable|string|max:50',
            'preferensi_notif' => 'nullable|array',
        ]);

        $orangtua->update([
            'nama' => $request->nama,
            'email' => $request->email,
            'no_wa' => $request->no_wa,
            'preferensi_notif' => $request->preferensi_notif ? json_encode($request->preferensi_notif) : null,
        ]);

        return redirect()->route('orangtua.index')->with('success', 'Data orang tua berhasil diperbarui!');
    }

    // 🔹 Hapus data
    public function destroy($id)
    {
        $orangtua = Orangtua::findOrFail($id);
        $orangtua->delete();
        return redirect()->route('orangtua.index')->with('success', 'Data orang tua berhasil dihapus!');
    }
}
