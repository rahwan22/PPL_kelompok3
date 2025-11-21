<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\Siswa;
use App\Models\Notifikasi;
use Carbon\Carbon;
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

    public function store(Request $request)
    {
        // Cek apakah request berasal dari QR Scan (JSON) atau Form Manual (HTML Form)
        $isQrScan = $request->header('Content-Type') === 'application/json';
        
        // Inisialisasi variabel
        $nis = null;
        $sumber = null;
        $status = null;
        $lokasi = null;
        $waktu_absensi = null;
        $tanggal_hari_ini = null;

        // --- 1. Validasi Data ---
        if ($isQrScan) {
            $validatedData = $request->validate([
                'nis' => 'required|string|max:20|exists:siswa,nis',
                // Lokasi scan bisa dikirim dari perangkat scanner/browser
                'lokasi' => 'nullable|string|max:100', 
            ]);
            
            $nis = $validatedData['nis'];
            $sumber = 'qr';
            $waktu_absensi = Carbon::now();
            $tanggal_hari_ini = $waktu_absensi->toDateString();
            $lokasi = $validatedData['lokasi'] ?? 'Perangkat QR';
            
        } else {
            // Logika untuk Form Absensi Manual
            $validatedData = $request->validate([
                'nis'     => 'required|string|max:20|exists:siswa,nis',
                'tanggal' => 'required|date',
                'jam'     => 'nullable|date_format:H:i',
                'status'  => 'required|in:hadir,terlambat,izin,sakit,alpa',
                'lokasi'  => 'nullable|string|max:100',
                'sumber'  => 'required|in:manual',
            ]);
            
            $nis = $validatedData['nis'];
            $sumber = 'manual';
            
            // *** PERBAIKAN UTAMA: Mengganti $datetime dengan $validatedData ***
            // Ini memastikan jam yang diinput user (H:i) digunakan
            $waktu_absensi = Carbon::parse($validatedData['tanggal'] . ' ' . ($validatedData['jam'] ?? '00:00:00'));
            
            $tanggal_hari_ini = $validatedData['tanggal'];
            $status = strtolower($validatedData['status']);
            $lokasi = $validatedData['lokasi'];
        }

        // --- 2. Cek Duplikasi Absensi (Hanya untuk Hadir/Terlambat) ---
        if ($status !== 'izin' && $status !== 'sakit' && $status !== 'alpa') {
            $existingAbsensi = Absensi::where('nis', $nis)
                ->whereDate('tanggal', $tanggal_hari_ini)
                ->whereIn('status', ['hadir', 'terlambat'])
                ->first();
                
            if ($existingAbsensi) {
                // Respon jika duplikasi
                $errorMessage = "❌ Siswa ini sudah absen ({$existingAbsensi->status}) hari ini pada pukul " . Carbon::parse($existingAbsensi->jam)->format('H:i') . ".";
                
                if ($isQrScan) {
                    return response()->json(['success' => false, 'message' => $errorMessage, 'status' => $existingAbsensi->status], 200);
                }
                return back()->with('error', $errorMessage);
            }

            // Tentukan Status untuk QR Scan / Hadir default
            if ($isQrScan) {
                // Ganti dengan jam masuk yang sebenarnya, contoh: 07:00
                $jam_masuk_target = Carbon::createFromTimeString('07:00:00'); 
                $status = $waktu_absensi->greaterThan($jam_masuk_target) ? 'terlambat' : 'hadir';
            }
        }
        
        // --- 3. Simpan Absensi ---
        try {
            $absensi = Absensi::create([
                'nis'     => $nis,
                // *** PERBAIKAN: Memformat Carbon object ke string TIME (H:i:s) ***
                'jam'     => $waktu_absensi->format('H:i:s'), 
                'tanggal' => $tanggal_hari_ini,
                'status'  => $status,
                'lokasi'  => $lokasi,
                'sumber'  => $sumber,
                'id_user' => auth()->check() ? auth()->id() : null, // Catat ID Guru/Admin
            ]);
            
            // --- 4. Kirim Notifikasi (Opsional) ---
            $this->kirimNotifikasiAbsensi($absensi); 

            // --- 5. Respon Sukses ---
            if ($isQrScan) {
                return response()->json([
                    'success' => true,
                    'message' => "✅ Absensi **" . ucwords($status) . "** tercatat untuk NIS: {$nis} pada " . $waktu_absensi->format('H:i') . ".",
                    'status'  => ucwords($status)
                ], 200);
            }
            
            // Respon untuk Form Manual
            return redirect()->route('absensi.index')->with('success', 'Data absensi berhasil ditambahkan.');

        } catch (\Exception $e) {
            \Log::error("Gagal menyimpan absensi untuk NIS {$nis}: " . $e->getMessage());
            
            if ($isQrScan) {
                return response()->json([
                    'success' => false,
                    'message' => "❌ Error Server saat menyimpan data: " . $e->getMessage(),
                ], 500);
            }
            
            return back()->with('error', 'Gagal menyimpan absensi. Silakan coba lagi.');
        }
    }

/**
 * Fungsi pembantu untuk notifikasi
 */
protected function kirimNotifikasiAbsensi($absensi)
{
    try {
        $siswa = Siswa::with('orangtua')->where('nis', $absensi->nis)->first();

        if ($siswa && $siswa->orangtua) {
            $orangtua = $siswa->orangtua;
            $status_kehadiran = ucwords($absensi->status);
            $tanggal_absensi  = Carbon::parse($absensi->tanggal)->isoFormat('dddd, D MMMM Y');
            
            // *** PERBAIKAN: Menggunakan $absensi->jam yang sudah tersimpan di DB ***
            // Parsing string waktu ('H:i:s') dari DB ke Carbon untuk diformat ('H:i')
            $waktu_tercatat = Carbon::parse($absensi->jam)->format('H:i');

            $pesan = "Halo Bapak/Ibu {$orangtua->nama}. Siswa atas nama **{$siswa->nama}** telah tercatat dengan status kehadiran **{$status_kehadiran}** pada {$tanggal_absensi} pukul {$waktu_tercatat}.";

            Notifikasi::create([
                'id_orangtua'  => $orangtua->id_orangtua,
                'nis'          => $siswa->nis,
                'jenis'        => 'absensi',
                'pesan'        => $pesan,
                'status_kirim' => 'pending', 
                'channel'      => 'wa',
            ]);
        }
    } catch (\Exception $e) {
        \Log::error("Gagal menyimpan notifikasi absensi otomatis: " . $e->getMessage());
    }
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
            'jam'     => 'nullable|date_format:H:i:s', // Menerima H:i:s
            'lokasi' => 'nullable|string|max:100', 
        ]);

        $absensi = Absensi::findOrFail($id);
        
        // Ambil waktu dari request, jika ada, pastikan formatnya benar
        $jamUpdate = $request->has('jam') ? Carbon::parse($request->jam)->format('H:i:s') : $absensi->jam;
        
        $absensi->update([
            'tanggal' => $request->tanggal,
            'status' => strtolower($request->status),
            'sumber' => $request->sumber,
            'jam' => $jamUpdate, // Gunakan jam yang sudah diformat atau jam lama
            'id_user' => auth()->check() ? auth()->id() : null, 
            'lokasi' => $request->lokasi,
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


    protected function handleQrScanStore(Request $request)
    {
        // 1. Validasi Input (Hanya butuh NIS dari scan + Lokasi)
        try {
            $request->validate([
                'nis' => 'required|string|exists:siswa,nis',
                'lokasi' => 'nullable|string|max:100',
            ]);
        } catch (ValidationException $e) { 
             // Jika NIS tidak ditemukan
             return response()->json([
                 'success' => false,
                 'message' => '❌ NIS tidak terdaftar di sistem.',
             ], 404);
        }

        $nis = $request->input('nis');
        $lokasi = $request->input('lokasi') ?? 'Perangkat Scan';
        $today = Carbon::today();
        $currentDateTime = Carbon::now();

        // 2. Cari Siswa
        $siswa = Siswa::where('nis', $nis)->first(); 

        // 3. Cek apakah siswa sudah absensi hari ini (Hadir, Terlambat)
        $sudahAbsen = Absensi::where('nis', $nis)
                             ->whereDate('tanggal', $today)
                             ->whereIn('status', ['hadir', 'terlambat']) 
                             ->first();

        if ($sudahAbsen) {
             // Jika sudah absen, kembalikan JSON
             return response()->json([
                 'success' => false,
                 'message' => '⚠️ ' . $siswa->nama . ' sudah absen hari ini pada ' . Carbon::parse($sudahAbsen->jam)->format('H:i') . ' dengan status ' . ucfirst($sudahAbsen->status) . '.',
             ], 409); // 409 Conflict
        }
        
        // 4. Tentukan Status Awal (Hadir atau Terlambat)
        // Atur batas waktu masuk (Contoh: Jam 07:15:00)
        $batasWaktuMasuk = Carbon::createFromTime(7, 15, 0); 
        
        $statusAbsensi = 'hadir'; 
        if ($currentDateTime->greaterThan($batasWaktuMasuk)) {
             $statusAbsensi = 'terlambat'; 
        }

        // 5. Catat Absensi
        try {
            Absensi::create([
                'nis' => $nis,
                'tanggal' => $today,
                'status' => $statusAbsensi,
                'sumber' => 'scan',
                // *** PERBAIKAN: Memformat waktu_absensi ke string TIME (H:i:s) ***
                'jam' => $currentDateTime->format('H:i:s'), 
                'id_user' => auth()->check() ? auth()->id() : null,
                'lokasi' => $lokasi, 
            ]);

            // Kembalikan JSON dengan pesan sukses
            return response()->json([
                'success' => true,
                'message' => '✅ Absensi ' . $siswa->nama . ' berhasil dicatat. Status: ' . ucfirst($statusAbsensi) . '.',
                'nama' => $siswa->nama,
                'status' => $statusAbsensi,
                'lokasi' => $lokasi, 
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