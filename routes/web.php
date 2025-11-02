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
});


// ===================== 3. ADMIN ACCESS (ROLE: ADMIN) =====================
Route::middleware(['auth', 'role:admin'])->group(function () {
    
    Route::get('dashboard/admin', [DashboardController::class, 'admin'])->name('dashboard.admin');

    // Resource CRUD untuk admin (termasuk AbsensiController@index dan @store manual)
    Route::resources([
        'siswa' => SiswaController::class,
        'guru' => GuruController::class,
        'mapel' => MataPelajaranController::class,
        'kelas' => KelasController::class,
        // 'nilai' => NilaiController::class,
        // 'absensi' => AbsensiController::class,
        
    ]);
    
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
});


// ===================== 5. GURU ACCESS (ROLE: GURU) =====================
Route::middleware(['auth', 'role:guru'])->group(function () {
    
    Route::get('dashboard/guru', [DashboardController::class, 'guru'])->name('dashboard.guru');

    Route::get('/absensi', [AbsensiController::class, 'index'])->name('absensi.index');
    Route::get('absensi/create', [AbsensiController::class, 'create'])->name('absensi.create');
    Route::get('absensi/{absensi}/edit', [AbsensiController::class, 'edit'])->name('absensi.edit');
    Route::put('absensi/{absensi}', [AbsensiController::class, 'update'])->name('absensi.update');
    Route::delete('absensi/{absensi}', [AbsensiController::class, 'destroy'])->name('absensi.destroy');
    Route::post('absensi/store', [AbsensiController::class, 'store'])->name('absensi.store');

    // --- Rute Absensi QR (PENTING) ---
    Route::get('/scan', [AbsensiController::class, 'scanForm'])->name('absensi.scan');
    
    // Route::get('/kelas', [KelasController::class, 'index'])->name('kelas.index');
    Route::get('guru/mapel', [MataPelajaranController::class, 'index'])->name('guru.mapel');
    

    // --- Rute Nilai ---
    Route::get('/nilai', [NilaiController::class, 'index'])->name('nilai.index');
    Route::get('nilai/create', [NilaiController::class, 'create'])->name('nilai.create');
    Route::post('/nilai/store', [NilaiController::class, 'store'])->name('nilai.store');


    
});
