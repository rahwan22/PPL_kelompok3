<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Orangtua;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

// Tambahan yang diperlukan untuk logika foto/transaksi:
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class SiswaController extends Controller
{
    /**
     * Tampilkan semua siswa.
     */
    public function index()
    {
        // Ambil Kelas, muat Siswa di dalamnya (termasuk relasi Orang Tua dari Siswa)
        $kelas = Kelas::with(['siswa', 'siswa.orangtua']) 
               ->orderBy('nama_kelas')
               ->get();
        return view('siswa.index', compact('kelas')); 
    }

    /**
     * Tampilkan form tambah siswa.
     */
    public function create()
    {
        $kelas = Kelas::all();
        $orangtua = Orangtua::all();
        return view('siswa.create', compact('kelas', 'orangtua'));
    }

    /**
     * Simpan data siswa baru, termasuk logika untuk Orang Tua.
     */
    public function store(Request $request)
    {
        // Aturan validasi
        $validator = Validator::make($request->all(), [
            'nis' => 'required|string|max:20|unique:siswa,nis',
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string',
            'id_kelas' => 'required|exists:kelas,id_kelas',
            
            'id_orangtua_pilihan' => 'nullable|exists:orangtua,id_orangtua', 
            'nama_ortu_baru' => 'nullable|string|max:255', 
            
            // ✅ PERBAIKAN KRUSIAL: Menggunakan 'no_wa' sesuai skema database Anda
            'no_wa_ortu_baru' => 'nullable|string|max:50|unique:orangtua,no_wa', 
            
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', 
        ], [
            // Pesan kustom untuk Orang Tua baru
            'no_wa_ortu_baru.unique' => 'Nomor WA Orang Tua sudah terdaftar di database.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $fotoPath = null;
            if ($request->hasFile('foto')) {
                $fotoPath = $request->file('foto')->store('photos/siswa', 'public');
            }

            // =========================================================
            // LOGIKA PENCARIAN/PEMBUATAN DATA ORANG TUA BARU
            // =========================================================
            $orangtua_id = $request->id_orangtua_pilihan; 

            // Cek apakah user mengisi data Orang Tua baru
            if ($request->filled('nama_ortu_baru') && $request->filled('no_wa_ortu_baru')) {
                
                // Cek duplikasi Orang Tua berdasarkan nomor WA (menggunakan 'no_wa')
                $existingOrangtua = Orangtua::where('no_wa', $request->no_wa_ortu_baru)->first();

                if ($existingOrangtua) {
                    // Gunakan ID yang sudah ada jika duplikasi ditemukan
                    $orangtua_id = $existingOrangtua->id_orangtua; 
                } else {
                    // Buat data Orang Tua baru
                    $ortu_baru = Orangtua::create([
                        'nama' => $request->nama_ortu_baru,
                        'no_wa' => $request->no_wa_ortu_baru, // ✅ Perbaikan field
                    ]);
                    $orangtua_id = $ortu_baru->id_orangtua;
                }
            }
            // =========================================================

            // Siapkan data Siswa
            $data = $request->except([
                '_token', 'foto', 
                'id_orangtua_pilihan', 'nama_ortu_baru', 'no_wa_ortu_baru' // Kecualikan field Ortu
            ]);
            
            $data['tanggal_lahir'] = $request->tanggal_lahir ? Carbon::parse($request->tanggal_lahir) : null;
            $data['aktif'] = 1;
            $data['foto'] = $fotoPath;
            $data['id_orangtua'] = $orangtua_id; // Masukkan ID Ortu yang sudah diproses
            
            Siswa::create($data);

            DB::commit();
            return redirect()->route('siswa.index')->with('success', 'Data Siswa berhasil ditambahkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            // Hapus file yang sudah terlanjur diupload jika terjadi error database
            if ($fotoPath) {
                Storage::disk('public')->delete($fotoPath);
            }
            return redirect()->back()->with('error', 'Gagal menambahkan data siswa: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Tampilkan detail siswa.
     */
    public function show($nis)
    {
        $userRole = auth()->user()->role;
        
        // Pastikan hanya role yang diizinkan yang bisa mengakses show
        if ($userRole !== 'admin' && $userRole !== 'kepala_sekolah' && $userRole !== 'guru') {
            abort(403, 'Akses Ditolak. Anda tidak memiliki izin untuk melihat detail kelas.');
        }
        
        $siswa = Siswa::with(['orangtua', 'kelas'])->where('nis', $nis)->firstOrFail();
        return view('siswa.detail', compact('siswa'));
    }

    /**
     * Tampilkan form edit siswa.
     */
    public function edit($nis)
    {
        $siswa = Siswa::where('nis', $nis)->firstOrFail();
        $kelas = Kelas::all();
        $orangtua = Orangtua::all();
        return view('siswa.edit', compact('siswa', 'kelas', 'orangtua'));
    }

    /**
     * Update data siswa.
     */
    public function update(Request $request, $nis)
    {
        $siswa = Siswa::where('nis', $nis)->firstOrFail();

        // Aturan validasi
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string',
            'id_kelas' => 'required|exists:kelas,id_kelas',
            'id_orangtua_pilihan' => 'nullable|exists:orangtua,id_orangtua',
            'nama_ortu_baru' => 'nullable|string|max:255',
            
            // ✅ PERBAIKAN KRUSIAL: Menggunakan 'no_wa' sesuai skema database Anda
            'no_wa_ortu_baru' => 'nullable|string|max:50',
            
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'hapus_foto' => 'nullable|boolean', 
            'aktif' => 'nullable|boolean', 
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            
            // =========================================================
            // LOGIKA PENCARIAN/PEMBUATAN DATA ORANG TUA (UPDATE)
            // =========================================================
            $orangtua_id = $request->id_orangtua_pilihan; 

            // Cek apakah user mengisi data Orang Tua baru
            if ($request->filled('nama_ortu_baru') && $request->filled('no_wa_ortu_baru')) {
                
                // Cek duplikasi Orang Tua berdasarkan nomor WA (menggunakan 'no_wa')
                $existingOrangtua = Orangtua::where('no_wa', $request->no_wa_ortu_baru)->first();

                if ($existingOrangtua) {
                    // Gunakan ID yang sudah ada
                    $orangtua_id = $existingOrangtua->id_orangtua; 
                } else {
                    // Buat data Orang Tua baru
                    $ortu_baru = Orangtua::create([
                        'nama' => $request->nama_ortu_baru,
                        'no_wa' => $request->no_wa_ortu_baru, // ✅ Perbaikan field
                    ]);
                    $orangtua_id = $ortu_baru->id_orangtua;
                }
            }
            // =========================================================

            $data = $request->except([
                '_token', '_method', 'foto', 'hapus_foto', 
                'id_orangtua_pilihan', 'nama_ortu_baru', 'no_wa_ortu_baru' // Kecualikan field Ortu
            ]);
            $oldFotoPath = $siswa->foto;

            // Logika Hapus Foto Lama
            if ($request->boolean('hapus_foto') && $oldFotoPath) {
                Storage::disk('public')->delete($oldFotoPath);
                $data['foto'] = null;
            }

            // Logika Upload Foto Baru
            if ($request->hasFile('foto')) {
                // Hapus foto lama jika ada (kecuali jika sedang dihapus)
                if ($oldFotoPath && !$request->boolean('hapus_foto')) {
                    Storage::disk('public')->delete($oldFotoPath);
                }
                $data['foto'] = $request->file('foto')->store('photos/siswa', 'public');
            }
            
            // Jika tidak ada foto baru dan tidak ada permintaan hapus, biarkan path foto lama
            if (!$request->hasFile('foto') && !$request->boolean('hapus_foto')) {
                 $data['foto'] = $oldFotoPath;
            }
            
            // Konversi tanggal lahir
            $data['tanggal_lahir'] = $request->tanggal_lahir ? Carbon::parse($request->tanggal_lahir) : null;
            
            // Tangani field 'aktif'
            $data['aktif'] = $request->has('aktif') ? $request->boolean('aktif') : $siswa->aktif;
            
            // Masukkan ID Orang Tua yang sudah diproses
            $data['id_orangtua'] = $orangtua_id;


            $siswa->update($data);

            DB::commit();
            return redirect()->route('siswa.index')->with('success', 'Data Siswa ' . $siswa->nama . ' berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memperbarui data siswa: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Hapus siswa.
     */
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

    /**
     * Generate QR Code untuk absensi.
     */
    public function generateQR($nis)
    {
        $siswa = Siswa::findOrFail($nis);

        $fileName = $nis . '.png';
        $filePath = 'qr/' . $fileName;

         // Generate QR code dengan konten NIS
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

    /**
     * Download QR Code.
     */
    public function downloadQR($nis)
    {
        $siswa = Siswa::findOrFail($nis);
        // Path lengkap ke file di public disk
        $filePath = storage_path('app/public/qr/' . $siswa->qr_code);

        if (!$siswa->qr_code || !file_exists($filePath)) {
            return back()->with('error', 'QR Code belum dibuat atau file tidak ditemukan.');
        }

        return response()->download($filePath, $siswa->nis . '_qrcode.png');
    }

    // Fungsi untuk menampilkan kartu siswa minimalis untuk dicetak
public function cetakId(Siswa $siswa)
{
    // Pastikan relasi kelas dimuat
    $siswa->load('kelas'); 

    // Menggunakan view minimalis yang baru dibuat
    return view('siswa.cetak_id_siswa', compact('siswa'));

    // Jika Anda ingin mengunduh sebagai PDF, Anda perlu menggunakan library seperti DomPDF:
    
    $pdf = PDF::loadView('siswa.cetak_id_siswa', compact('siswa'));
    return $pdf->download('kartu-siswa-' . $siswa->nis . '.pdf');
    
}
public function downloadIdCardMassal(Request $request)
    {
        // 1. Validasi input NIS
        $nisList = $request->input('nis_list');

        if (empty($nisList)) {
            return redirect()->back()->with('error', 'Tidak ada siswa yang dipilih untuk unduh ID Card massal.');
        }

        // 2. Ambil data siswa yang dipilih
        // Pastikan Anda mengambil relasi QR code jika QR code disimpan sebagai relasi/file
        $siswas = Siswa::whereIn('nis', $nisList)
                       ->whereNotNull('qr_code') // Hanya yang sudah punya QR Code
                       ->get();

        if ($siswas->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada siswa yang valid (dengan QR Code) ditemukan.');
        }

        // 3. Render ke view cetak (Contoh menggunakan Dompdf)
        
        // Buat ID Card menjadi kelompok 6 per halaman
        $chunks = $siswas->chunk(6); 
        
        // Buat view khusus untuk cetak ID Card
        $pdf = PDF::loadView('siswa.id_card_massal_cetak', [
            'chunks' => $chunks, 
            'siswas' => $siswas
        ]);

        // Atur ukuran dan orientasi kertas jika diperlukan (misal A4 Landscape)
        // $pdf->setPaper('a4', 'landscape');
        
        // 4. Download file PDF
        return $pdf->download('id_card_massal_' . now()->format('Ymd_His') . '.pdf');
    }
}