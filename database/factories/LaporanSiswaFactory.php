<?php

namespace Database\Factories;

use App\Models\LaporanSiswa;
use App\Models\Siswa;
use App\Models\Kelas;
use Illuminate\Database\Eloquent\Factories\Factory;

class LaporanSiswaFactory extends Factory
{
    protected $model = LaporanSiswa::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $nis = Siswa::pluck('nis');
        $kelasIds = Kelas::pluck('id_kelas');
        $periodeAwal = $this->faker->dateTimeBetween('-6 months', '-3 months')->format('Y-m-d');
        $periodeAkhir = $this->faker->dateTimeBetween('-3 months', 'now')->format('Y-m-d');
        
        $hadir = $this->faker->numberBetween(35, 40);
        $terlambat = $this->faker->numberBetween(0, 5);
        $izin = $this->faker->numberBetween(0, 3);
        $sakit = $this->faker->numberBetween(0, 2);
        $alpa = $this->faker->numberBetween(0, 1);

        return [
            'nis' => $this->faker->randomElement($nis),
            'id_kelas' => $this->faker->randomElement($kelasIds),
            'periode_awal' => $periodeAwal,
            'periode_akhir' => $periodeAkhir,
            'hadir' => $hadir,
            'terlambat' => $terlambat,
            'izin' => $izin,
            'sakit' => $sakit,
            'alpa' => $alpa,
            'catatan' => $this->faker->optional(0.5)->sentence(),
            'status' => $this->faker->randomElement(['draft', 'final']),
        ];
    }
}