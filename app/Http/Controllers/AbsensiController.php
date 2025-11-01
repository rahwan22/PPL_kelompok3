<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\Siswa;
use Carbon\Carbon; // *** DITAMBAHKAN: Untuk memanajemen waktu/tanggal ***
use Illuminate\Validation\ValidationException; // Untuk penanganan error validasi yang lebih baik

class AbsensiController extends Controller
{
    /**
     * Menampilkan semua absensi
     */
    public function index()
    {
        // Pastikan relasi 'siswa' sudah didefinisikan di model Absensi
        $absensi = Absensi::with('siswa')->orderBy('tanggal', 'desc')->get();
        return view('absensi.index', compact('absensi'));
    }

    /**
     * Form tambah absensi (Manual)
     */
    public function create()
    {
        $siswa = Siswa::where('aktif', true)->get();
        return view('absensi.create', compact('siswa'));
    }

    /**
     * Metode BARU untuk menampilkan view scan QR bagi guru
     */
    public function scanForm()
    {
        // Mengarahkan ke view yang berisi scanner QR
        return view('absensi.scan'); 
    }

    /**
     * Simpan data absensi baru (digunakan untuk Form Manual dan Scan QR)
     */
    public function store(Request $request)
    {
        // 1. Cek apakah permintaan datang dari AJAX/JSON (biasanya dari QR scan)
        if ($request->wantsJson()) {
            return $this->handleQrScanStore($request);
        }

        // --- 2. Logika untuk Form Manual (Jika request bukan JSON) ---

        $request->validate([
            // Pastikan field 'nis' ada di table Absensi atau Anda mapping ke 'siswa_id'
            'nis' => 'required|exists:siswa,nis',
            'tanggal' => 'required|date',
            'status' => 'required|in:hadir,terlambat,izin,sakit,alpa', // Menggunakan huruf kecil sesuai ENUM di migration
            'sumber' => 'required|in:scan,manual',
            'lokasi' => 'nullable|string|max:100', // *** PERUBAHAN: Validasi Lokasi Manual ***
        ]);

        // Catat absensi manual
        Absensi::create([
            'nis' => $request->nis, 
            'tanggal' => $request->tanggal,
            'status' => strtolower($request->status), // Pastikan status disimpan dalam huruf kecil
            'sumber' => 'manual', // Diatur manual karena ini adalah input manual
            'jam' => $request->jam ?? Carbon::now()->format('H:i:s'),
            'id_user' => auth()->check() ? auth()->id() : null, // Catat ID Guru/Admin yang menginput
            'lokasi' => $request->lokasi, // *** PERUBAHAN: Menyimpan Lokasi Manual ***
        ]);

        return redirect()->route('absensi.index')->with('success', 'Data absensi berhasil ditambahkan secara manual!');
    }

    /**
     * Form edit absensi
     */
    public function edit($id)
    {
        $absensi = Absensi::findOrFail($id);
        $siswa = Siswa::all();
        return view('absensi.edit', compact('absensi', 'siswa'));
    }

    /**
     * Update data absensi
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:hadir,terlambat,izin,sakit,alpa',
            'sumber' => 'required|in:scan,manual',
            'tanggal' => 'required|date',
            'lokasi' => 'nullable|string|max:100', // *** PERUBAHAN: Validasi Lokasi Update ***
        ]);

        $absensi = Absensi::findOrFail($id);
        $absensi->update([
            'tanggal' => $request->tanggal,
            'status' => strtolower($request->status), // Pastikan status disimpan dalam huruf kecil
            'sumber' => $request->sumber,
            'jam' => $request->jam ?? $absensi->jam,
            'id_user' => auth()->check() ? auth()->id() : null, // Catat ID Guru/Admin yang mengupdate
            'lokasi' => $request->lokasi, // *** PERUBAHAN: Menyimpan Lokasi Update ***
        ]);

        return redirect()->route('absensi.index')->with('success', 'Data absensi berhasil diperbarui!');
    }

    /**
     * Hapus data
     */
    public function destroy($id)
    {
        $absensi = Absensi::findOrFail($id);
        $absensi->delete();

        return redirect()->route('absensi.index')->with('success', 'Data absensi berhasil dihapus!');
    }
    
    // =================================================================
    // FUNGSI UNTUK MENANGANI SCAN QR SISWA OLEH GURU 
    // =================================================================

    /**
     * Metode internal untuk menangani permintaan absensi dari Scan QR.
     * Mengembalikan Respons JSON.
     */
    protected function handleQrScanStore(Request $request)
    {
        // 1. Validasi Input (Hanya butuh NIS dari scan + Lokasi)
        try {
            $request->validate([
                'nis' => 'required|string|exists:siswa,nis',
                'lokasi' => 'nullable|string|max:100', // *** PERUBAHAN: Validasi Lokasi Scan ***
            ]);
        } catch (ValidationException $e) { // Menggunakan ValidationException yang diimpor
             // Jika NIS tidak ditemukan
             return response()->json([
                 'success' => false,
                 'message' => '❌ NIS tidak terdaftar di sistem.',
             ], 404);
        }

        $nis = $request->input('nis');
        $lokasi = $request->input('lokasi'); // *** PERUBAHAN: Ambil Lokasi ***
        $today = Carbon::today();

        // 2. Cari Siswa
        $siswa = Siswa::where('nis', $nis)->first(); 

        // 3. Cek apakah siswa sudah absensi hari ini (Hadir, Terlambat)
        // Pastikan status yang dicari sesuai dengan ENUM (huruf kecil)
        $sudahAbsen = Absensi::where('nis', $nis)
                             ->whereDate('tanggal', $today)
                             ->whereIn('status', ['hadir', 'terlambat']) 
                             ->first();

        if ($sudahAbsen) {
             // Jika sudah absen, kembalikan JSON
             return response()->json([
                 'success' => false,
                 'message' => '⚠️ ' . $siswa->nama . ' sudah absen hari ini pada ' . $sudahAbsen->jam . ' dengan status ' . ucfirst($sudahAbsen->status) . '.',
             ], 409); // 409 Conflict
        }
        
        // 4. Tentukan Status Awal (Hadir atau Terlambat)
        // Atur batas waktu masuk (Contoh: Jam 07:15:00)
        $batasWaktuMasuk = Carbon::createFromTime(7, 15, 0); 
        $currentDateTime = Carbon::now();
        
        $statusAbsensi = 'hadir'; // Status awal: hadir (huruf kecil)
        if ($currentDateTime->greaterThan($batasWaktuMasuk)) {
             $statusAbsensi = 'terlambat'; // Status: terlambat (huruf kecil)
        }

        // 5. Catat Absensi
        try {
            Absensi::create([
                'nis' => $nis,
                'tanggal' => $today,
                'status' => $statusAbsensi,
                'sumber' => 'scan',
                'jam' => $currentDateTime->format('H:i:s'),
                'id_user' => auth()->check() ? auth()->id() : null, // Catat ID Guru/Admin yang mengawasi scan
                'lokasi' => $lokasi, // *** PERUBAHAN: Menyimpan Lokasi Scan ***
            ]);

            // Kembalikan JSON dengan pesan sukses
            return response()->json([
                'success' => true,
                'message' => '✅ Absensi ' . $siswa->nama . ' berhasil dicatat. Status: ' . ucfirst($statusAbsensi) . '.',
                'nama' => $siswa->nama,
                'status' => $statusAbsensi,
                'lokasi' => $lokasi, // Mengembalikan lokasi dalam respons
            ], 200);

        } catch (\Exception $e) {
            \Log::error('QR Scan Absensi Error: ' . $e->getMessage() . ' for NIS: ' . $nis);

            return response()->json([
                'success' => false,
                'message' => '❌ Terjadi kesalahan server (500) saat mencatat absensi. Coba lagi.',
            ], 500);
        }
    }
}
