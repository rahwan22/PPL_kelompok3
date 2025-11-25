<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Guru;
use App\Models\MataPelajaran;
use App\Models\Kelas;
use Illuminate\Support\Facades\DB;

class GuruMapelKelasSeeder extends Seeder
{
    /**
     * Isi tabel pivot guru_mapel_kelas (relasi Many-to-Many).
     */
    public function run(): void
    {
        // Pastikan Kelas, Mapel, dan Guru sudah ada
        $kelas = Kelas::all();
        $mapel = MataPelajaran::all();
        $gurus = Guru::all();

        if ($kelas->isEmpty() || $mapel->isEmpty() || $gurus->isEmpty()) {
            echo "Skipping GuruMapelKelasSeeder: Pastikan data Kelas, Mapel, dan Guru sudah dibuat di DatabaseSeeder.\n";
            return;
        }

        // Hubungkan Guru, Mapel, dan Kelas melalui tabel pivot
        foreach ($gurus as $guru) {
            // Setiap guru akan mengajar 2 mata pelajaran yang dipilih acak
            $selectedMapel = $mapel->random(rand(1, min(3, $mapel->count()))); 

            foreach ($selectedMapel as $m) {
                // Dan setiap Mapel diajarkan di 1 sampai 3 kelas yang berbeda
                $selectedKelas = $kelas->random(rand(1, min(3, $kelas->count()))); 

                foreach ($selectedKelas as $k) {
                    // Masukkan data ke tabel pivot (guru_mapel_kelas)
                    // Kita menggunakan insert biasa karena kita sudah menangani keunikan di skema migrasi
                    DB::table('guru_mapel_kelas')->insertOrIgnore([ // Menggunakan insertOrIgnore untuk menghindari duplikat jika unik
                        'id_guru' => $guru->id_guru,
                        'id_mapel' => $m->id_mapel,
                        'id_kelas' => $k->id_kelas,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}