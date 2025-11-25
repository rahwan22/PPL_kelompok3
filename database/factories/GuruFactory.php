<?php

namespace Database\Factories;

use App\Models\Guru;
use App\Models\User;
use App\Models\Kelas;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class GuruFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Guru::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // 1. Buat user baru dengan role 'guru'
        $user = User::factory()->create([
            'role' => 'guru',
            'nip' => $this->faker->unique()->numerify('19##########'), // Contoh NIP 14 digit
            'email' => $this->faker->unique()->safeEmail(),
            'nama' => $this->faker->name('male' | 'female'), // Ambil nama dari Faker
        ]);

        // 2. Ambil ID Kelas yang belum memiliki wali kelas (untuk id_kelas_wali)
        // Kita hanya mengambil ID, bukan objek, untuk menghindari circular dependency
        $availableKelasIds = Kelas::whereDoesntHave('waliKelas')->pluck('id_kelas')->toArray();
        
        // Pilih ID Kelas secara acak, bisa null
        $idKelasWali = $this->faker->randomElement(array_merge([null], $availableKelasIds));

        // Jika kelas dipilih, pastikan kelas tersebut ditandai sebagai sudah memiliki wali di scope factory ini
        if ($idKelasWali !== null) {
             // (Opsional) Implementasi yang lebih kompleks dibutuhkan di seeder jika ingin memblokir di database
             // Untuk factory, kita hanya perlu memastikan id_kelas_wali unik saat seeding masal
        }

        return [
            'id_user' => $user->id_user,
            'nip' => $user->nip, // Menggunakan NIP dari User yang sudah dibuat
            'nama' => $user->nama, // Menggunakan Nama dari User yang sudah dibuat
            
            // Relasi ke Mata Pelajaran tidak ada di tabel guru lagi,
            // tetapi akan diurus oleh tabel pivot di Seeder.

            // Relasi Wali Kelas
            'id_kelas_wali' => $idKelasWali, 
            
            // Data Guru lainnya
            'jenis_kelamin' => $this->faker->randomElement(['L', 'P']),
            'alamat' => $this->faker->address(),
            'no_hp' => $this->faker->phoneNumber(),
            'email' => $user->email,
            'foto' => $this->faker->imageUrl(), // Jika Anda ingin foto default
        ];
    }
}