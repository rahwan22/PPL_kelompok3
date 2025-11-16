<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Orangtua;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

// Tambahan yang diperlukan untuk logika foto/transaksi:
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class SiswaController extends Controller
{
    // 🔹 Tampilkan semua siswa
    public function index()
    {
        // Pastikan relasi di-load untuk kolom Kelas
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

    // 🔹 Simpan data baru (Diperbarui dengan logika foto dan Orang Tua)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nis' => 'required|string|max:20|unique:siswa,nis',
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string',
            'id_kelas' => 'required|exists:kelas,id_kelas',
            'id_orangtua' => 'nullable|exists:orangtua,id_orangtua',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validasi Foto
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $fotoPath = null;
            if ($request->hasFile('foto')) {
                // Simpan foto di folder 'public/photos/siswa'
                $fotoPath = $request->file('foto')->store('photos/siswa', 'public');
            }
            
            // Menggunakan data request, dan menimpa dengan data yang diproses
            $data = $request->except(['_token', 'foto']);
            $data['tanggal_lahir'] = $request->tanggal_lahir ? Carbon::parse($request->tanggal_lahir) : null;
            $data['aktif'] = 1;
            $data['foto'] = $fotoPath;
            
            Siswa::create($data);

            DB::commit();
            return redirect()->route('siswa.index')->with('success', 'Data Siswa berhasil ditambahkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            // Hapus file yang sudah terlanjur diupload jika terjadi error database
            if ($fotoPath) {
                Storage::disk('public')->delete($fotoPath);
            }
            return redirect()->back()->with('error', 'Gagal menambahkan data siswa: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource (Halaman Detail).
     */
    public function show($nis)
    {
        // Pastikan relasi di-load untuk halaman detail
        $siswa = Siswa::where('nis', $nis)->with(['kelas', 'orangtua'])->firstOrFail();
        return view('siswa.detail', compact('siswa'));
    }

    // 🔹 Form edit siswa
    public function edit($nis)
    {
        $siswa = Siswa::where('nis', $nis)->firstOrFail();
        $kelas = Kelas::all();
        $orangtua = Orangtua::all();
        return view('siswa.edit', compact('siswa', 'kelas', 'orangtua'));
    }

    // 🔹 Update data siswa (Diperbarui dengan logika foto)
    public function update(Request $request, $nis)
    {
        $siswa = Siswa::where('nis', $nis)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string',
            'id_kelas' => 'required|exists:kelas,id_kelas',
            'id_orangtua' => 'nullable|exists:orangtua,id_orangtua',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'hapus_foto' => 'nullable|boolean', 
            'aktif' => 'nullable|boolean', 
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $data = $request->except(['_token', '_method', 'foto', 'hapus_foto']);
            $oldFotoPath = $siswa->foto;

            // Logika Hapus Foto Lama
            if ($request->boolean('hapus_foto') && $oldFotoPath) {
                Storage::disk('public')->delete($oldFotoPath);
                $data['foto'] = null;
            }

            // Logika Upload Foto Baru
            if ($request->hasFile('foto')) {
                // Hapus foto lama jika ada dan tidak sedang dihapus
                if ($oldFotoPath && !$request->boolean('hapus_foto')) {
                    Storage::disk('public')->delete($oldFotoPath);
                }
                $data['foto'] = $request->file('foto')->store('photos/siswa', 'public');
            }
            
            // Jika tidak ada foto baru dan tidak ada permintaan hapus, biarkan path foto lama
            if (!$request->hasFile('foto') && !$request->boolean('hapus_foto')) {
                 $data['foto'] = $oldFotoPath;
            }
            
            // Pastikan tanggal lahir diubah ke format Carbon atau null
            $data['tanggal_lahir'] = $request->tanggal_lahir ? Carbon::parse($request->tanggal_lahir) : null;
            
            // Tangani field 'aktif' jika ada di request
            $data['aktif'] = $request->has('aktif') ? $request->boolean('aktif') : $siswa->aktif;


            $siswa->update($data);

            DB::commit();
            return redirect()->route('siswa.index')->with('success', 'Data Siswa ' . $siswa->nama . ' berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memperbarui data siswa: ' . $e->getMessage());
        }
    }

    // 🔹 Hapus siswa
    public function destroy($nis)
    {
        $siswa = Siswa::where('nis', $nis)->firstOrFail();
        $fotoPath = $siswa->foto;
        $qrPath = $siswa->qr_code ? 'qr/' . $siswa->qr_code : null;

        try {
            DB::beginTransaction();
            $siswa->delete();

            // Hapus file foto terkait
            if ($fotoPath) {
                Storage::disk('public')->delete($fotoPath);
            }
            // Hapus file QR code terkait
            if ($qrPath) {
                Storage::disk('public')->delete($qrPath);
            }

            DB::commit();
            return redirect()->route('siswa.index')->with('success', 'Data Siswa ' . $siswa->nama . ' berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus data siswa: ' . $e->getMessage());
        }
    }

    // ✅ Generate QR Code
    public function generateQR($nis)
    {
        $siswa = Siswa::findOrFail($nis);

        $fileName = $nis . '.png';
        $filePath = 'qr/' . $fileName;

         // Generate QR
        $qr = QrCode::format('png')
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
        // Perbaiki path agar mengarah ke public disk
        $filePath = storage_path('app/public/qr/' . $siswa->qr_code);

        if (!file_exists($filePath)) {
            return back()->with('error', 'QR Code belum dibuat.');
        }

        return response()->download($filePath, $siswa->nis . '_qrcode.png');
    }
}