<?php

namespace Database\Factories;

use App\Models\MataPelajaran;
use Illuminate\Database\Eloquent\Factories\Factory;

class MataPelajaranFactory extends Factory
{
    protected $model = MataPelajaran::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $subjects = [
            'Matematika' => 'MTK',
            'Bahasa Indonesia' => 'BIN',
            'Ilmu Pengetahuan Alam' => 'IPA',
            'Ilmu Pengetahuan Sosial' => 'IPS',
            'Pendidikan Agama Islam' => 'PAI',
            'Seni Budaya' => 'SBK',
            'Bahasa Inggris' => 'BIG',
        ];
        
        $nama = $this->faker->unique()->randomElement(array_keys($subjects));
        $kode = $subjects[$nama] . $this->faker->unique()->randomNumber(2, true);

        return [
            'nama_mapel' => $nama,
            'kode_mapel' => $kode,
            'tingkat' => $this->faker->numberBetween(1, 6),
        ];
    }
}
