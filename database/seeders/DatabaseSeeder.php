<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Orangtua;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Nilai;
use App\Models\Absensi;
use App\Models\Notifikasi;
use App\Models\Pengumuman;
use App\Models\LaporanKelas;
use App\Models\LaporanSiswa;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Jalankan proses seeding untuk aplikasi.
     * Pastikan urutan seeding mengikuti hierarki Foreign Key.
     */
    public function run(): void
    {
        // =================================================================
        // LEVEL 1: Data Dasar (Tidak memiliki Foreign Key ke tabel lain)
        // =================================================================

        // 1. Users: Buat user khusus (admin, kepsek) dan user guru
        User::factory()->admin()->create();
        User::factory()->kepalaSekolah()->create();
        // Buat 6 user dengan role 'guru' yang akan dihubungkan ke tabel Guru
        User::factory(6)->guru()->create();
        // Buat 5 user umum (opsional)
        User::factory(1)->create();
        
        // 2. Data Independen lainnya
        Orangtua::factory(10)->create();
        Kelas::factory(6)->create(); // Misal 6 kelas
        MataPelajaran::factory(7)->create(); // 7 mata pelajaran

        // =================================================================
        // LEVEL 2: Data Berelasi (Bergantung pada data Level 1)
        // =================================================================

        $guru_users = User::where('role', 'guru')->pluck('id_user');
        $kelas = Kelas::pluck('id_kelas');
        
        // 3. Guru: Sinkronisasi akun User role 'guru' dengan profil Guru
        Guru::factory(6)->make()->each(function ($guru, $index) use ($guru_users, $kelas) {
            // Hubungkan User ID yang sudah dibuat sebelumnya
            $guru->id_user = $guru_users->get($index);
            
            // ❌ BARIS INI DIHAPUS karena id_mapel sudah tidak ada di tabel 'guru'
            // $guru->id_mapel = $mapel->random();

            // Assign Wali Kelas secara unik (maksimal 6 kelas, 6 guru)
            if ($index < $kelas->count()) {
                // Ambil ID kelas secara unik sesuai index
                $guru->id_kelas_wali = $kelas->get($index);
            } else {
                 $guru->id_kelas_wali = null;
            }
            $guru->save();
        });

        // 4. Siswa: Bergantung pada Kelas dan Orangtua
        Siswa::factory(6)->create(); // 25 Siswa

        // -----------------------------------------------------------------
        // 🆕 PANGGIL SEEDER KHUSUS UNTUK TABEL PIVOT
        // -----------------------------------------------------------------
        $this->call(GuruMapelKelasSeeder::class);
        // -----------------------------------------------------------------
        

        // =================================================================
        // LEVEL 3: Data Transaksional & Laporan (Bergantung pada Siswa, Guru, dll.)
        // =================================================================
        
        // Data pendukung untuk transaksional (Ambil ID setelah Guru dan Siswa dibuat)
        $guru_ids = Guru::pluck('id_guru');
        $mapel_ids = MataPelajaran::pluck('id_mapel');
        $siswa_nis = Siswa::pluck('nis');
        $kelas_ids = Kelas::pluck('id_kelas');

        // // 5. Data Transaksional
        // Nilai::factory(50)->make()->each(function ($nilai) use ($siswa_nis, $mapel_ids, $kelas_ids, $guru_ids) {
        //     $nilai->nis = $siswa_nis->random();
        //     $nilai->id_mapel = $mapel_ids->random();
        //     $nilai->id_kelas = $kelas_ids->random();
        //     $nilai->id_guru = $guru_ids->random();
        //     $nilai->save();
        // });
        
        // Absensi::factory(100)->make()->each(function ($absensi) use ($siswa_nis, $kelas_ids, $guru_ids) {
        //     $absensi->nis = $siswa_nis->random();
        //     $absensi->id_kelas = $kelas_ids->random();
        //     $absensi->id_guru = $guru_ids->random();
        //     $absensi->save();
        // });
        
        // Notifikasi::factory(20)->create(); 
        
        // Pengumuman::factory(10)->make()->each(function ($pengumuman) use ($guru_ids) {
        //     $pengumuman->id_guru = $guru_ids->random();
        //     $pengumuman->save();
        // });

        // 6. Data Laporan
        // LaporanKelas::factory(6)->make()->each(function ($laporan) use ($kelas_ids, $guru_ids) {
        //     $laporan->id_kelas = $kelas_ids->random();
        //     $laporan->id_guru = $guru_ids->random();
        //     $laporan->save();
        // });
        
        // LaporanSiswa::factory(25)->make()->each(function ($laporan) use ($siswa_nis, $kelas_ids, $guru_ids) {
        //     $laporan->nis = $siswa_nis->random();
        //     $laporan->id_kelas = $kelas_ids->random();
        //     $laporan->id_guru = $guru_ids->random();
        //     $laporan->save();
        // });
    }
}