<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SiswaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nis' => 'NIS' . $this->faker->unique()->numerify('###'),
            'nama' => $this->faker->name(),
            'alamat' => $this->faker->name(),
            'tanggal_lahir' => $this->faker->date('Y-m-d', '2016-01-01'),
            'id_kelas' => $this->faker->numberBetween(1, 2, 3, 4, 5, 6), // kelas 1A atau 2B
            'qr_code' => Str::uuid(),
            'id_orangtua' => $this->faker->numberBetween(1, 2),
            'aktif' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
