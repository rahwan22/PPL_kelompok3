<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1️⃣ USERS (Akun login) - TIDAK BERUBAH
        Schema::create('users', function (Blueprint $table) {
            $table->id('id_user');
            $table->string('nip', 20)->nullable();
            $table->string('nama', 100);
            $table->string('email', 100)->unique();
            $table->string('password', 255);
            $table->enum('role', ['admin', 'kepala_sekolah', 'guru'])->default('guru');
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();
        });

        // 3️⃣ ORANG TUA - TIDAK BERUBAH
        Schema::create('orangtua', function (Blueprint $table) {
            $table->id('id_orangtua');
            $table->string('nama', 100);
            $table->string('email', 100)->nullable();
            $table->string('no_wa', 50)->nullable();
            $table->json('preferensi_notif')->nullable();
            $table->timestamps();
        });

        // 4️⃣ KELAS - TIDAK BERUBAH
        Schema::create('kelas', function (Blueprint $table) {
            $table->id('id_kelas');
            $table->string('nama_kelas', 50); 
            $table->string('tahun_ajaran', 50); 
            $table->timestamps();
        });

        // 6️⃣ MATA PELAJARAN - TIDAK BERUBAH
        Schema::create('mata_pelajaran', function (Blueprint $table) {
            $table->id('id_mapel');
            $table->string('nama_mapel', 100);
            $table->string('kode_mapel', 20)->unique();
            $table->enum('tingkat', ['1', '2', '3', '4', '5', '6'])->nullable();
            $table->timestamps();
        });

        // 2️⃣ GURU (REVISI: id_mapel dihapus, id_kelas_wali tetap)
        Schema::create('guru', function (Blueprint $table) {
            $table->id('id_guru'); 
            
            // Data Guru
            $table->string('nip', 20)->unique()->nullable();
            $table->string('nama', 100)->nullable(false);
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->text('alamat')->nullable();
            $table->string('no_hp', 20)->nullable();
            $table->string('email', 100)->unique()->nullable();
            $table->string('foto', 255)->nullable(); 
            
            // =======================================================
            // FOREIGN KEY
            // =======================================================
            
            // 1. Relasi ke Users (Wajib)
            $table->unsignedBigInteger('id_user')->unique();
            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');

            // ❌ Dihapus: $table->unsignedBigInteger('id_mapel');

            // 2. Relasi Wali Kelas (Opsional, Unique)
            $table->unsignedBigInteger('id_kelas_wali')->nullable()->unique();
            $table->foreign('id_kelas_wali')->references('id_kelas')->on('kelas')->onDelete('set null'); // Mengubah ke set null

            $table->timestamps();
        });

        // 🆕 TABEL PIVOT: guru_mapel_kelas (Menghubungkan Guru - Mapel - Kelas)
        // Ini adalah tabel kunci untuk logic Anda: Guru mengajar Mapel di Kelas tertentu.
        Schema::create('guru_mapel_kelas', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('id_guru')->constrained('guru', 'id_guru')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('id_mapel')->constrained('mata_pelajaran', 'id_mapel')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('id_kelas')->constrained('kelas', 'id_kelas')->cascadeOnUpdate()->cascadeOnDelete();

            // Kombinasi ini memastikan seorang guru tidak mengajar Mapel yang sama di Kelas yang sama dua kali.
            $table->unique(['id_guru', 'id_mapel', 'id_kelas'], 'unique_pengajaran'); 

            $table->timestamps();
        });
        // -------------------------------------------------------------
        
        // 5️⃣ SISWA - TIDAK BERUBAH
        Schema::create('siswa', function (Blueprint $table) {
            $table->string('nis', 20)->primary();
            $table->string('nama', 100);
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->date('tanggal_lahir')->nullable();
            $table->text('alamat')->nullable();
            $table->string('foto', 255)->nullable();
            $table->string('qr_code', 255)->nullable();
            $table->foreignId('id_kelas')
                ->constrained('kelas', 'id_kelas')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('id_orangtua')
                ->nullable()
                ->constrained('orangtua', 'id_orangtua')
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        // 7️⃣ NILAI / RAPOR (REVISI: Tambahkan id_guru untuk mengetahui siapa yang menginput nilai)
        Schema::create('nilai', function (Blueprint $table) {
            $table->id('id_nilai');
            $table->string('nis', 20);
            $table->foreign('nis')->references('nis')->on('siswa')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('id_mapel')->constrained('mata_pelajaran', 'id_mapel')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('id_kelas')->nullable()->constrained('kelas', 'id_kelas')->cascadeOnUpdate()->nullOnDelete();
            
            // 🆕 Tambahkan FK Guru yang menginput nilai
            $table->foreignId('id_guru')->nullable()->constrained('guru', 'id_guru')->cascadeOnUpdate()->nullOnDelete();

            $table->float('nilai_tugas')->default(0);
            $table->float('nilai_uts')->default(0);
            $table->float('nilai_uas')->default(0);
            $table->float('nilai_akhir')->default(0);
            $table->text('catatan')->nullable();
            $table->string('semester', 10)->nullable();
            $table->timestamps();
        });

        // 8️⃣ ABSENSI (REVISI: Menggunakan id_kelas, BUKAN id_user, untuk menentukan kelas yang diabsen)
        Schema::create('absensi', function (Blueprint $table) {
            $table->id('id_absensi');
            $table->string('nis', 20);
            $table->foreign('nis')->references('nis')->on('siswa')->cascadeOnUpdate()->cascadeOnDelete();
            
            // 🔄 Gunakan id_kelas (diambil dari $siswa->id_kelas saat absensi)
            $table->foreignId('id_kelas')->nullable()->constrained('kelas', 'id_kelas')->cascadeOnUpdate()->nullOnDelete(); 
            
            // 🔄 Gunakan id_guru (siapa yang mencatat absensi)
            $table->foreignId('id_guru')->nullable()->constrained('guru', 'id_guru')->cascadeOnUpdate()->nullOnDelete(); 

            $table->date('tanggal');
            $table->time('jam')->nullable();
            $table->enum('status', ['hadir', 'terlambat', 'izin', 'sakit', 'alpa'])->default('hadir');
            $table->enum('sumber', ['scan', 'manual'])->default('scan');
            $table->string('lokasi', 100)->nullable();
            $table->timestamps();
        });

        // 9️⃣ NOTIFIKASI - TIDAK BERUBAH
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->id('id_notif');
            $table->foreignId('id_orangtua')->constrained('orangtua', 'id_orangtua')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('nis', 20);
            $table->foreign('nis')->references('nis')->on('siswa')->cascadeOnUpdate()->cascadeOnDelete();
            $table->enum('jenis', ['absensi', 'pengumuman', 'nilai']);
            $table->text('pesan');
            $table->enum('status_kirim', ['pending', 'sent', 'failed'])->default('pending');
            $table->enum('channel', ['email', 'wa'])->default('wa');
            $table->timestamps();
        });

        // 🔟 PENGUMUMAN (REVISI: Menggunakan id_guru, bukan id_user, sebagai pembuat)
        Schema::create('pengumuman', function (Blueprint $table) {
            $table->id('id_pengumuman');
            $table->string('judul', 200);
            $table->text('isi');
            $table->foreignId('id_guru')->nullable()->constrained('guru', 'id_guru')->cascadeOnUpdate()->nullOnDelete();
            $table->date('tanggal');
            $table->enum('tujuan', ['semua', 'guru', 'orangtua', 'siswa'])->default('semua');
            $table->timestamps();
        });

        // 11️⃣ LAPORAN KELAS (REVISI: Menggunakan id_guru, bukan id_user)
        Schema::create('laporan_kelas', function (Blueprint $table) {
            $table->id('id_laporan_kelas');
            $table->foreignId('id_kelas')->constrained('kelas', 'id_kelas')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('id_guru')->nullable()->constrained('guru', 'id_guru')->cascadeOnUpdate()->nullOnDelete();
            $table->date('periode_awal');
            $table->date('periode_akhir');
            $table->integer('total_siswa')->default(0);
            $table->integer('total_hadir')->default(0);
            $table->integer('total_terlambat')->default(0);
            $table->integer('total_izin')->default(0);
            $table->integer('total_sakit')->default(0);
            $table->integer('total_alpa')->default(0);
            $table->text('catatan')->nullable();
            $table->enum('status', ['draft', 'review', 'final'])->default('draft');
            $table->timestamps();
        });

        // 12️⃣ LAPORAN SISWA (REVISI: Menggunakan id_guru, bukan id_user)
        Schema::create('laporan_siswa', function (Blueprint $table) {
            $table->id('id_laporan_siswa');
            $table->string('nis', 20);
            $table->foreign('nis')->references('nis')->on('siswa')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('id_kelas')->nullable()->constrained('kelas', 'id_kelas')->cascadeOnUpdate()->nullOnDelete();
            // 🆕 Tambahkan FK Guru yang membuat laporan
            $table->foreignId('id_guru')->nullable()->constrained('guru', 'id_guru')->cascadeOnUpdate()->nullOnDelete(); 
            $table->date('periode_awal');
            $table->date('periode_akhir');
            $table->integer('hadir')->default(0);
            $table->integer('terlambat')->default(0);
            $table->integer('izin')->default(0);
            $table->integer('sakit')->default(0);
            $table->integer('alpa')->default(0);
            $table->text('catatan')->nullable();
            $table->enum('status', ['draft', 'review', 'final'])->default('draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        // Urutan drop harus diperhatikan karena adanya Foreign Key
        Schema::dropIfExists('laporan_siswa');
        Schema::dropIfExists('laporan_kelas');
        Schema::dropIfExists('pengumuman');
        Schema::dropIfExists('notifikasi');
        Schema::dropIfExists('absensi');
        Schema::dropIfExists('nilai');
        
        // 🆕 Drop tabel pivot dulu
        Schema::dropIfExists('guru_mapel_kelas'); 
        
        Schema::dropIfExists('guru'); 
        Schema::dropIfExists('mata_pelajaran');
        Schema::dropIfExists('siswa');
        Schema::dropIfExists('kelas');
        Schema::dropIfExists('orangtua');
        Schema::dropIfExists('users');
    }
};