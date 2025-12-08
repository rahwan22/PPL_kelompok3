<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\NilaiController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\MataPelajaranController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\AlokasiMengajarController;




// Rute Halaman Utama
Route::get('/u', function () {
    return view('welcome');
});

// ===================== 1. AUTHENTICATION (LOGIN & REGISTER) =====================

Route::get('/', [DashboardController::class, 'loginForm'])->name('login');
Route::post('login', [DashboardController::class, 'loginPost'])->name('login.post');
Route::post('logout', [DashboardController::class, 'logout'])->name('logout');



// ===================== 2. DASHBOARD UMUM (WAJIB LOGIN) =====================
Route::middleware(['auth'])->group(function () {
    // Rute dashboard utama yang biasanya mengarahkan berdasarkan peran (role)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Route untuk menampilkan form Ubah Sandi
    Route::get('/ubah-sandi', [DashboardController::class, 'editPassword'])->name('password.edit');
    Route::post('/ubah-sandi', [DashboardController::class, 'updatePassword'])->name('password.update');
});

//route yang bisa di akses lebih dari satu user

Route::resource('kelas', KelasController::class);
Route::resource('siswa', SiswaController::class);
Route::resource('guru', GuruController::class);
Route::get('admin/siswa/{nis}/nilai', [NilaiController::class, 'nilaiSiswaByAdmin'])->name('admin.nilai.show_by_siswa');
Route::get('/kelas/{id_kelas}/mapel', [MataPelajaranController::class, 'indexByKelas'])->name('kelas.mapel.list');


// ===================== 3. ADMIN ACCESS (ROLE: ADMIN) =====================
Route::middleware(['auth', 'role:admin'])->group(function () {
    
    Route::get('dashboard/admin', [DashboardController::class, 'admin'])->name('dashboard.admin');

    // Resource CRUD untuk admin (termasuk AbsensiController@index dan @store manual)
    Route::resources([
        // 'siswa' => SiswaController::class,
        // 'guru' => GuruController::class,
        'mapel' => MataPelajaranController::class,
        // 'kelas' => KelasController::class,
        // 'nilai' => NilaiController::class,
        // 'absensi' => AbsensiController::class,
        
    ]);
    



    // 🆕 RUTE BARU: PENGELOLAAN ALOKASI MENGAJAR (CRUD)
    Route::get('/alokasi/available-kelas', [AlokasiMengajarController::class, 'getAvailableKelas']);
    Route::prefix('alokasi')->group(function () {
        Route::get('/', [AlokasiMengajarController::class, 'index'])->name('alokasi.index');
        Route::get('/create', [AlokasiMengajarController::class, 'create'])->name('alokasi.create');
        Route::post('/', [AlokasiMengajarController::class, 'store'])->name('alokasi.store');
        // Asumsi hanya perlu delete untuk tabel pivot
        Route::delete('/{alokasi}', [AlokasiMengajarController::class, 'destroy'])->name('alokasi.destroy');
    });

    Route::get('/siswa/{siswa}/cetak-id', [SiswaController::class, 'cetakId'])->name('siswa.cetak.id');
    Route::get('/download-idcard-massal', [SiswaController::class, 'downloadIdCardMassal'])->name('admin.downloadIdCardMassal');
    
    // Rute khusus QR untuk Admin (mencetak QR Siswa)
    Route::get('/admin/siswa/{nis}/generate-qr', [SiswaController::class, 'generateQR'])->name('admin.siswa.generateQR');
    Route::get('/admin/siswa/{nis}/download-qr', [SiswaController::class, 'downloadQR'])->name('admin.siswa.downloadQR');
});


// ===================== 4. KEPALA SEKOLAH ACCESS (ROLE: KEPALA_SEKOLAH) =====================
Route::middleware(['auth', 'role:kepala_sekolah'])->group(function () {
    
    Route::get('dashboard/kepsek', [DashboardController::class, 'kepsek'])->name('dashboard.kepsek');

    // Laporan untuk kepala sekolah (menggunakan Controller index untuk menampilkan daftar)
    Route::get('laporan/guru', [GuruController::class, 'index'])->name('laporan.guru');
    Route::get('laporan/siswa', [SiswaController::class, 'index'])->name('laporan.siswa');
    Route::get('laporan/nilai', [NilaiController::class, 'index'])->name('laporan.nilai');
    Route::get('laporan/absensi', [AbsensiController::class, 'index'])->name('laporan.absensi');
    Route::get('laporan/mapel', [MataPelajaranController::class, 'index'])->name('laporan.mapel');
    Route::get('laporan/kelas', [KelasController::class, 'index'])->name('laporan.kelas');
    
    
});


// ===================== 5. GURU ACCESS (ROLE: GURU) =====================
Route::middleware(['auth', 'role:guru'])->group(function () {
   
    
    Route::get('dashboard/guru', [DashboardController::class, 'guru'])->name('dashboard.guru');
    Route::resource('absensi', AbsensiController::class);

    // Route untuk menampilkan jadwal mengajar khusus guru
    Route::get('/jadwal-mengajar-saya', [AlokasiMengajarController::class, 'jadwalGuru'])->name('jadwal.guru.saya');
   
    Route::get('/scan', [AbsensiController::class, 'scanForm'])->name('absensi.scan');
    
    // Route::get('semua/kelas', [KelasController::class, 'index'])->name('lihat.kelas');
    Route::get('semua/mapel', [MataPelajaranController::class, 'index'])->name('lihat.mapel');

    // --- Rute Nilai --
    Route::resource('nilai', NilaiController::class);
    
    
    Route::resource('notifikasi', NotifikasiController::class);


    
});


Route::get('/absensi/get-siswa/{id_kelas}', [AbsensiController::class, 'getSiswaByKelas'])->name('absensi.getSiswa');