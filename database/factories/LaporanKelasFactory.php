<?php

namespace Database\Factories;

use App\Models\LaporanKelas;
use App\Models\Kelas;
use App\Models\Guru;
use Illuminate\Database\Eloquent\Factories\Factory;

class LaporanKelasFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LaporanKelas::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Ambil data ID Kelas yang tersedia
        $kelasIds = Kelas::pluck('id_kelas');
        
        // Ambil data ID Guru yang tersedia
        $guruIds = Guru::pluck('id_guru');
        
        // Tetapkan periode laporan
        $startDate = $this->faker->dateTimeBetween('-1 year', '-6 months')->format('Y-m-d');
        $endDate = $this->faker->dateTimeBetween('-5 months', 'now')->format('Y-m-d');
        
        $totalSiswa = $this->faker->numberBetween(25, 35);
        $totalAbsen = $this->faker->numberBetween(5, 15);
        
        // Hitungan total kehadiran agar konsisten
        $totalHadir = $this->faker->numberBetween(150, 200);
        $totalTerlambat = $this->faker->numberBetween(0, 10);
        $totalIzin = $this->faker->numberBetween(0, 8);
        $totalSakit = $this->faker->numberBetween(0, 5);
        $totalAlpa = $this->faker->numberBetween(0, 3);


        return [
            'id_kelas' => $kelasIds->random(),
            
            // ❌ PENTING: Kolom 'id_user' DIHILANGKAN
            
            'id_guru' => $guruIds->random(), // Penanggung jawab laporan
            
            'periode_awal' => $startDate,
            'periode_akhir' => $endDate,
            
            'total_siswa' => $totalSiswa,
            'total_hadir' => $totalHadir,
            'total_terlambat' => $totalTerlambat,
            'total_izin' => $totalIzin,
            'total_sakit' => $totalSakit,
            'total_alpa' => $totalAlpa,
            
            'catatan' => $this->faker->optional(0.7)->text(200),
            'status' => $this->faker->randomElement(['draft', 'review', 'final']),
            
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}