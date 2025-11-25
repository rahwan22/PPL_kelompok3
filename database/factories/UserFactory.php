<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $role = $this->faker->randomElement(['admin', 'kepala_sekolah', 'guru']);
        // NIP dihasilkan acak untuk non-admin
        $nip = ($role !== 'admin') ? $this->faker->unique()->numerify('##################') : null;

        return [
            'nip' => $nip,
            'nama' => $this->faker->name(),
            // Email acak untuk default
            'email' => $this->faker->unique()->safeEmail(), 
            'password' => Hash::make('password'), // password default: 'password'
            'role' => $role,
            'status_aktif' => $this->faker->boolean(90), // 90% aktif
        ];
    }

    public function admin(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'nip' => null,
                'nama' => 'Admin Sekolah',
                // Kredensial login admin yang pasti
                'email' => 'admin@sekolah.com', 
                'role' => 'admin',
                'status_aktif' => true,
            ];
        });
    }

    public function kepalaSekolah(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'nip' => $this->faker->unique()->numerify('1970##########'),
                'nama' => 'Kepala Sekolah',
                // Kredensial login Kepsek yang pasti
                'email' => 'kepsek@sekolah.com', 
                'role' => 'kepala_sekolah',
                'status_aktif' => true,
            ];
        });
    }
    
    // 1. STATE UNTUK SEEDING MASSAL (EMAIL UNIK)
    // Digunakan untuk membuat banyak user guru secara acak tanpa error.
    public function guru(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'nip' => $this->faker->unique()->numerify('##################'),
                'nama' => 'Guru ' . $this->faker->lastName(), // Nama generik
                'email' => $this->faker->unique()->safeEmail(), // HARUS UNIK
                'role' => 'guru',
                'status_aktif' => true,
            ];
        });
    }
    
    // 2. STATE UNTUK TESTING LOGIN (EMAIL STATIS)
    // Digunakan HANYA SEKALI untuk membuat akun testing yang kredensialnya pasti.
    public function guruTesting(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'nip' => $this->faker->unique()->numerify('##################'),
                'nama' => 'Guru Testing Utama',
                'email' => 'guru@sekolah.com', // Email yang Anda inginkan
                'role' => 'guru',
                'status_aktif' => true,
            ];
        });
    }
}