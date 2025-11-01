<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Orangtua;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class SiswaController extends Controller
{
    // 🔹 Tampilkan semua siswa
    public function index()
    {
        $siswa = Siswa::with(['kelas', 'orangtua'])->get();
        return view('siswa.index', compact('siswa'));
    }

    // 🔹 Tampilkan form tambah
    public function create()
    {
        $kelas = Kelas::all();
        $orangtua = Orangtua::all();
        return view('siswa.create', compact('kelas', 'orangtua'));
    }

    // 🔹 Simpan data baru
    public function store(Request $request)
    {
        $request->validate([
            'nis' => 'required|unique:siswa,nis',
            'nama' => 'required|string|max:100',
            'jenis_kelamin' => 'required|in:L,P',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string',
            'id_kelas' => 'required|exists:kelas,id_kelas',
            'id_orangtua' => 'nullable|exists:orangtua,id_orangtua',
        ]);

        Siswa::create($request->all());
        return redirect()->route('siswa.index')->with('success', 'Data siswa berhasil ditambahkan!');
    }

    // 🔹 Form edit siswa
    public function edit($nis)
    {
        $siswa = Siswa::findOrFail($nis);
        $kelas = Kelas::all();
        $orangtua = Orangtua::all();
        return view('siswa.edit', compact('siswa', 'kelas', 'orangtua'));
    }

    // 🔹 Update data siswa
    public function update(Request $request, $nis)
    {
        $siswa = Siswa::findOrFail($nis);

        $request->validate([
            'nama' => 'required|string|max:100',
            'jenis_kelamin' => 'required|in:L,P',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string',
            'id_kelas' => 'required|exists:kelas,id_kelas',
            'id_orangtua' => 'nullable|exists:orangtua,id_orangtua',
        ]);

        $siswa->update($request->all());
        return redirect()->route('siswa.index')->with('success', 'Data siswa berhasil diperbarui!');
    }

    // 🔹 Hapus siswa
    public function destroy($nis)
    {
        $siswa = Siswa::findOrFail($nis);
        $siswa->delete();
        return redirect()->route('siswa.index')->with('success', 'Data siswa berhasil dihapus!');
    }

    // ✅ Generate QR Code
    public function generateQR($nis)
    {
        $siswa = Siswa::findOrFail($nis);

        $fileName = $nis . '.svg';
        $filePath = 'qr/' . $fileName;

        // // Generate QR
        $qr = QrCode::format('svg')
            ->size(300)
            ->errorCorrection('H')
            ->generate($nis);

        // Simpan ke storage/app/public/qr/
        Storage::disk('public')->put($filePath, $qr);

        // Simpan nama file ke DB
        $siswa->qr_code = $fileName;
        $siswa->save();

        return back()->with('success', 'QR Code berhasil dibuat!');
    }

    // ✅ Download QR Code
    public function downloadQR($nis)
    {
        $siswa = Siswa::findOrFail($nis);
        $filePath = storage_path('app/public/qr/' . $siswa->qr_code);

        if (!file_exists($filePath)) {
            return back()->with('error', 'QR Code belum dibuat.');
        }

        return response()->download($filePath, $siswa->nis . '_qrcode.svg');
    }
}
