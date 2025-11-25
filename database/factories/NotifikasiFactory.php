<?php

namespace Database\Factories;

use App\Models\Notifikasi;
use App\Models\Orangtua;
use App\Models\Siswa;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotifikasiFactory extends Factory
{
    protected $model = Notifikasi::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $orangtuaIds = Orangtua::pluck('id_orangtua');
        $nis = Siswa::pluck('nis');

        return [
            'id_orangtua' => $this->faker->randomElement($orangtuaIds),
            'nis' => $this->faker->randomElement($nis),
            'jenis' => $this->faker->randomElement(['absensi', 'pengumuman', 'nilai']),
            'pesan' => $this->faker->sentence(),
            'status_kirim' => $this->faker->randomElement(['pending', 'sent', 'failed']),
            'channel' => $this->faker->randomElement(['email', 'wa']),
        ];
    }
}