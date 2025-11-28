<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\Siswa;
use App\Models\Notifikasi;
use App\Models\Kelas; // Pastikan model Kelas diimport
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AbsensiController extends Controller
{
    /**
     * Menampilkan semua absensi
     */
    public function index()
    {
        $absensi = Absensi::with('siswa')->orderBy('tanggal', 'desc')->get();
        return view('absensi.index', compact('absensi'));
    }

    /**
     * Form tambah absensi manual
     */
    public function create()
    {
        $siswa = Siswa::where('aktif', true)->get();
        return view('absensi.create', compact('siswa'));
    }

    /**
     * Tampilan Scan QR
     */
    public function scanForm()
    {
        return view('absensi.scan');
    }

    /**
     * Store Absensi (SCAN QR & MANUAL digabung)
     */
    public function store(Request $request)
    {
        $isQr = $request->header('Content-Type') === 'application/json';
        $nis = '';
        $tanggal = '';
        $lokasi = '';
        $waktu = Carbon::now();

        // ===============================
        // 1. VALIDASI INPUT
        // ===============================
        if ($isQr) {
            try {
                $validated = $request->validate([
                    'nis' => 'required|exists:siswa,nis',
                    'lokasi' => 'nullable|string|max:100'
                ]);
            } catch (ValidationException $e) {
                // Tangani error validasi untuk permintaan QR (JSON)
                $msg = "❌ NIS tidak valid atau tidak ditemukan.";
                return response()->json(['success' => false, 'message' => $msg], 422);
            }

            $nis = $validated['nis'];
            $lokasi = $validated['lokasi'] ?? 'QR Device';
            $tanggal = Carbon::today()->toDateString();
            $waktu = Carbon::now();
        } else {
            $validated = $request->validate([
                'nis' => 'required|exists:siswa,nis',
                'tanggal' => 'required|date',
                'jam' => 'nullable|date_format:H:i',
                'status' => 'required|in:hadir,terlambat,izin,sakit,alpa',
                'lokasi' => 'nullable|string|max:100',
                'sumber' => 'required|in:manual',
            ]);

            $nis = $validated['nis'];
            $tanggal = $validated['tanggal'];
            $lokasi = $validated['lokasi'];
            $waktu = Carbon::parse($tanggal . ' ' . ($validated['jam'] ?? '07:00'));
        }
        
        // ===============================
        // 1.5. Ambil Siswa dan Kelas (Diperlukan untuk Logika QR)
        // ===============================
        $siswa = Siswa::with('kelas')->where('nis', $nis)->first();

        // Cek jika ini adalah absensi QR dan data siswa/kelas tidak ditemukan
        if ($isQr && (!$siswa || !$siswa->kelas)) {
             $msg = "❌ Data siswa atau kelas tidak ditemukan. Mohon cek data NIS dan penugasan kelas.";
             return response()->json(['success' => false, 'message' => $msg], 404);
        }

        // ===============================
        // 2. CEK DUPLIKASI ABSENSI
        // ===============================
        $existing = Absensi::where('nis', $nis)
            ->whereDate('tanggal', $tanggal)
            ->whereIn('status', ['hadir', 'terlambat'])
            ->first();

        if ($existing) {
            $msg = "❌ Sudah absen ({$existing->status}) pada " . Carbon::parse($existing->jam)->format('H:i');

            return $isQr
                ? response()->json(['success' => false, 'message' => $msg])
                : back()->with('error', $msg);
        }

        // ===============================
        // 3. TENTUKAN STATUS
        // ===============================
        if ($isQr) {
            // Logika Pembatasan Jam Masuk BERDASARKAN KELAS
            
            // Mengambil jam_masuk dari relasi Kelas. Jika tidak ada, default ke '07:30:00'.
            $jamMasukString = $siswa->kelas->jam_masuk ?? '07:30:00'; 
            
            // Konversi jam masuk kelas ke objek Carbon
            $jamMasuk = Carbon::createFromTimeString($jamMasukString);
            
            // Bandingkan waktu scan dengan jam masuk kelas
            // Jika waktu scan > jam masuk kelas, status = 'terlambat'
            $status = $waktu->greaterThan($jamMasuk) ? 'terlambat' : 'hadir';
            $sumber = 'scan';
            
            // Logging untuk debugging
            Log::info("Absensi QR untuk NIS: {$nis}, Kelas: {$siswa->kelas->nama_kelas}, Jam Masuk Kelas: {$jamMasukString}, Waktu Scan: {$waktu->format('H:i')}, Status: {$status}");
        } else {
            // Logika untuk input manual (status sudah divalidasi di langkah 1)
            $status = strtolower($validated['status']);
            $sumber = 'manual';
        }

        // ===============================
        // 4. SIMPAN ABSENSI
        // ===============================
        try {
            $absensi = Absensi::create([
                'nis' => $nis,
                'tanggal' => $tanggal,
                'jam' => $waktu->format('H:i'),
                'status' => $status,
                'sumber' => $sumber,
                'lokasi' => $lokasi,
                'id_user' => Auth::check() ? Auth::id() : null,

            ]);

            // Kirim notifikasi WA
            $this->kirimNotifikasiAbsensi($absensi);

            // === Respon SCAN QR ===
            if ($isQr) {
                $statusPesan = ($status == 'terlambat') 
                    ? "‼️ {$status}! Anda masuk pada {$waktu->format('H:i')} (Batas: {$jamMasukString})." 
                    : "✅ Absensi {$status} dicatat pada {$waktu->format('H:i')}.";

                return response()->json([
                    'success' => true,
                    'message' => $statusPesan,
                    'status' => $status
                ]);
            }

            return redirect()->route('absensi.index')->with('success', 'Absensi berhasil ditambahkan');
        } catch (\Exception $e) {
            Log::error("Gagal menyimpan absensi: " . $e->getMessage()); // Tambahkan log error
            return $isQr
                ? response()->json(['success' => false, 'message' => "❌ Error server: Absensi gagal dicatat."])
                : back()->with('error', 'Gagal menyimpan absensi.');
        }
    }

    /**
     * Fungsi Kirim Notifikasi Absensi
     */
    protected function kirimNotifikasiAbsensi($absensi)
    {
        try {
            $siswa = Siswa::with('orangtua')->where('nis', $absensi->nis)->first();

            if (!$siswa || !$siswa->orangtua) return;

            $waJam = Carbon::parse($absensi->jam)->format('H:i');
            $waTanggal = Carbon::parse($absensi->tanggal)->isoFormat('dddd, D MMMM Y');

            $pesan = "Halo, siswa *{$siswa->nama}* tercatat *{$absensi->status}* pada {$waTanggal} pukul {$waJam}.";

            Notifikasi::create([
                'id_orangtua' => $siswa->orangtua->id_orangtua,
                'nis' => $siswa->nis,
                'jenis' => 'absensi',
                'pesan' => $pesan,
                'status_kirim' => 'pending',
                'channel' => 'wa',
            ]);
        } catch (\Exception $e) {
            Log::error("Gagal kirim notifikasi absensi: " . $e->getMessage());
        }
    }

    /**
     * EDIT, UPDATE, DELETE tetap sama
     */
    public function edit($id)
    {
        $absensi = Absensi::findOrFail($id);
        $siswa = Siswa::all();
        return view('absensi.edit', compact('absensi', 'siswa'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:hadir,terlambat,izin,sakit,alpa',
            'tanggal' => 'required|date',
            // Memperluas format jam untuk menerima H:i atau H:i:s, sesuai kebutuhan umum
            'jam' => ['nullable', 'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/'],
            'lokasi' => 'nullable|string|max:100',
            'sumber' => 'required|in:scan,manual',
        ]);

        $absensi = Absensi::findOrFail($id);

        // Format jam menjadi H:i sebelum update
        $jam = $request->jam ? Carbon::parse($request->jam)->format('H:i') : $absensi->jam;

        $absensi->update([
            'tanggal' => $request->tanggal,
            'status' => strtolower($request->status),
            'jam' => $jam,
            'lokasi' => $request->lokasi,
            'sumber' => $request->sumber,
        ]);

        return redirect()->route('absensi.index')->with('success', 'Berhasil diperbarui');
    }

    public function destroy($id)
    {
        Absensi::findOrFail($id)->delete();
        return redirect()->route('absensi.index')->with('success', 'Berhasil dihapus.');
    }
}