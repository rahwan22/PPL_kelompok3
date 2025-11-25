<?php

namespace Database\Factories;

use App\Models\Absensi;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Kelas;
use Illuminate\Database\Eloquent\Factories\Factory;

class AbsensiFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Absensi::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Ambil NIS dan data Siswa terkait secara acak
        $siswaNis = Siswa::pluck('nis')->random();
        $siswa = Siswa::where('nis', $siswaNis)->first();

        // Ambil ID Guru secara acak
        $guruId = Guru::pluck('id_guru')->random();

        return [
            'nis' => $siswaNis,
            
            // ⚠️ PENTING: Mengganti 'id_user' menjadi 'id_guru'
            'id_guru' => $guruId, 
            
            // Mengambil ID Kelas dari data siswa saat ini
            'id_kelas' => $siswa->id_kelas ?? Kelas::pluck('id_kelas')->random(), 
            
            'tanggal' => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'jam' => $this->faker->time(),
            'status' => $this->faker->randomElement(['hadir', 'terlambat', 'izin', 'sakit', 'alpa']),
            'sumber' => $this->faker->randomElement(['scan', 'manual']),
            'lokasi' => $this->faker->city(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}