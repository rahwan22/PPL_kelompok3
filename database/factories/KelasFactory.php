<?php

namespace Database\Factories;

use App\Models\Kelas;
use Illuminate\Database\Eloquent\Factories\Factory;

class KelasFactory extends Factory
{
    protected $model = Kelas::class;

    // Static property untuk menyimpan daftar kelas yang sudah dibuat
    // Ini membantu mencegah duplikasi jika factory dipanggil berkali-kali dalam satu run
    private static $generatedClassNames = [];

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        // Untuk memastikan setiap nama kelas unik dalam satu seeding run
        // kita akan mencoba beberapa kali untuk mendapatkan kombinasi unik.
        $maxAttempts = 100; // Batasi percobaan agar tidak infinite loop
        $attempt = 0;
        $uniqueNameFound = false;
        $namaKelas = '';
        
        $tahun_mulai = (date('Y') - $this->faker->numberBetween(0, 2)); // Biar tahun ajaran lebih variatif
        $tahun_selesai = $tahun_mulai + 1;
        $tahun_ajaran = $tahun_mulai . '/' . $tahun_selesai;

        while (!$uniqueNameFound && $attempt < $maxAttempts) {
            $tingkat = $this->faker->numberBetween(1, 6); 
            // $huruf = $this->faker->randomElement(['A', 'B', 'C', 'D']);
            // $nomorRombel = $this->faker->numberBetween(1, 3); // Tambahkan nomor rombel untuk variasi lebih
            
            // Menggunakan Angka Romawi untuk tingkat agar lebih bervariasi dan jelas
            $tingkatRomawi = match ($tingkat) {
                1 => 'I',
                2 => 'II',
                3 => 'III',
                4 => 'IV',
                5 => 'V',
                6 => 'VI',
                default => $tingkat, // Fallback
            };

            // Contoh: "Kelas I-A1", "Kelas II-B2"
            $generatedNamaKelas = 'Kelas ' . $tingkatRomawi ;
            
            // Kita juga harus mempertimbangkan tahun ajaran untuk keunikan global jika diperlukan
            // Namun, dalam kasus ini, nama_kelas saja sudah harus cukup unik untuk factory
            $fullUniqueIdentifier = $generatedNamaKelas . ' (' . $tahun_ajaran . ')';

            if (!in_array($fullUniqueIdentifier, self::$generatedClassNames)) {
                $namaKelas = $generatedNamaKelas;
                self::$generatedClassNames[] = $fullUniqueIdentifier; // Simpan identifier unik
                $uniqueNameFound = true;
            }
            $attempt++;
        }

        // Jika tidak bisa menemukan nama unik setelah banyak percobaan, 
        // fallback ke sesuatu yang unik secara paksa
        if (!$uniqueNameFound) {
            $namaKelas = 'Kelas-ERROR-' . uniqid();
        }

        return [
            'nama_kelas' => $namaKelas,
            'tahun_ajaran' => $tahun_ajaran,
        ];
    }
}