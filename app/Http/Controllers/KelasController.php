<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KelasController extends Controller
{
    // ASUMSI: Middleware 'admin' sudah didaftarkan di Kernel.php
    // Jika tidak, Anda dapat menggunakan pengecekan manual Auth::user()->role === 'admin'
    // di dalam setiap method modifikasi.

    /**
     * Menampilkan daftar semua Kelas.
     * Dapat diakses oleh semua role (admin, kepala_sekolah, guru).
     */
    public function index()
    {
        // Eager load relasi waliKelas untuk menampilkan nama guru
        $kelas = Kelas::with('waliKelas')->orderBy('tahun_ajaran', 'desc')->orderBy('nama_kelas')->get();
        return view('kelas.index', compact('kelas'));
    }

    /**
     * Menampilkan form untuk membuat Kelas baru (Hanya Admin).
     */
    public function create()
    {
        // Pengecekan Akses Manual (jika middleware tidak digunakan)
        if (Auth::check() && Auth::user()->role !== 'admin') {
            abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        // Data Guru untuk dropdown Wali Kelas
        $guru = Guru::orderBy('nama')->get();
        return view('kelas.create', compact('guru'));
    }

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
            'id_wali_kelas' => 'nullable|exists:guru,id_guru',
        ]);

        Kelas::create($validated);

        return redirect()->route('kelas.index')->with('success', 'Kelas baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit Kelas (Hanya Admin).
     */
    public function edit(Kelas $kela)
    {
        if (Auth::check() && Auth::user()->role !== 'admin') {
            abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini.');
        }
        
        $guru = Guru::orderBy('nama')->get();
        // Variabel $kela sudah otomatis berisi data kelas yang dicari oleh Laravel
        return view('kelas.edit', compact('kela', 'guru'));
    }

    /**
     * Memperbarui Kelas di database (Hanya Admin).
     */
    public function update(Request $request, Kelas $kela)
    {
        if (Auth::check() && Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak. Hanya Admin yang dapat mengubah data kelas.');
        }

        // Validasi, kecuali untuk nama kelas yang sedang di-update
        $validated = $request->validate([
            'nama_kelas' => 'required|string|max:50|unique:kelas,nama_kelas,' . $kela->id_kelas . ',id_kelas',
            'tahun_ajaran' => 'required|string|max:50',
            'id_wali_kelas' => 'nullable|exists:guru,id_guru',
        ]);

        $kela->update($validated);

        return redirect()->route('kelas.index')->with('success', 'Data Kelas berhasil diperbarui.');
    }

    /**
     * Menghapus Kelas dari database (Hanya Admin).
     */
    public function destroy(Kelas $kela)
    {
        if (Auth::check() && Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak. Hanya Admin yang dapat menghapus data kelas.');
        }
        
        $kela->delete();
        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil dihapus.');
    }
}
