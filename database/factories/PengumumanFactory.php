<?php

namespace Database\Factories;

use App\Models\Pengumuman;
use App\Models\Guru;
use Illuminate\Database\Eloquent\Factories\Factory;

class PengumumanFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Pengumuman::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'judul' => $this->faker->sentence(5),
            'isi' => $this->faker->paragraphs(3, true),
            
            // ⚠️ PENTING: Kolom 'id_user' dihilangkan. 
            // Kita hanya menggunakan 'id_guru' untuk pengumuman.
            'id_guru' => Guru::pluck('id_guru')->random(),
            
            'tanggal' => $this->faker->dateTimeBetween('-6 months', '+6 months')->format('Y-m-d'),
            'tujuan' => $this->faker->randomElement(['semua', 'guru', 'orangtua', 'siswa']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}