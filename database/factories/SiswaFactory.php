<?php

namespace Database\Factories;

use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Orangtua;
use Illuminate\Database\Eloquent\Factories\Factory;

class SiswaFactory extends Factory
{
    protected $model = Siswa::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        // Mendapatkan ID yang sudah ada untuk relasi.
        $kelasIds = Kelas::pluck('id_kelas');
        $orangtuaIds = Orangtua::pluck('id_orangtua');

        $gender = $this->faker->randomElement(['L', 'P']);

        return [
            'nis' => $this->faker->unique()->numerify('00######'),
            'nama' => $this->faker->name($gender == 'L' ? 'male' : 'female'),
            'jenis_kelamin' => $gender,
            'tanggal_lahir' => $this->faker->date('Y-m-d', '2015-01-01'),
            'alamat' => $this->faker->address(),
            'foto' => $this->faker->imageUrl(640, 480, 'children', true, 'Siswa'),
            'qr_code' => $this->faker->sha256(), // QR code simulasi
            
            'id_kelas' => $this->faker->randomElement($kelasIds),
            'id_orangtua' => $this->faker->randomElement($orangtuaIds),
            
            'aktif' => $this->faker->boolean(95),
        ];
    }
}