<?php

namespace Database\Factories;

use App\Models\Nilai;
use App\Models\Siswa;
use App\Models\MataPelajaran;
use App\Models\Kelas;
use Illuminate\Database\Eloquent\Factories\Factory;

class NilaiFactory extends Factory
{
    protected $model = Nilai::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $nis = Siswa::pluck('nis');
        $mapelIds = MataPelajaran::pluck('id_mapel');
        $kelasIds = Kelas::pluck('id_kelas');
        
        $tugas = $this->faker->numberBetween(70, 100);
        $uts = $this->faker->numberBetween(65, 95);
        $uas = $this->faker->numberBetween(60, 90);
        
        // Hitung nilai akhir (contoh bobot)
        $akhir = round(($tugas * 0.3) + ($uts * 0.3) + ($uas * 0.4));

        return [
            'nis' => $this->faker->randomElement($nis),
            'id_mapel' => $this->faker->randomElement($mapelIds),
            'id_kelas' => $this->faker->randomElement($kelasIds),
            'nilai_tugas' => $tugas,
            'nilai_uts' => $uts,
            'nilai_uas' => $uas,
            'nilai_akhir' => $akhir,
            'catatan' => $this->faker->optional(0.3)->sentence(),
            'semester' => $this->faker->randomElement(['Ganjil', 'Genap']),
        ];
    }
}