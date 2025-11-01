<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Siswa;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1️⃣ USERS
        DB::table('users')->insert([
            [
                'nip' => 'ADM001',
                'nama' => 'Admin Utama',
                'email' => 'admin@example.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nip' => 'G001',
                'nama' => 'Budi Santoso',
                'email' => 'budi@example.com',
                'password' => Hash::make('guru123'),
                'role' => 'guru',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nip' => 'G002',
                'nama' => 'Siti Rahma',
                'email' => 'siti@example.com',
                'password' => Hash::make('guru123'),
                'role' => 'guru',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nip' => 'KS001',
                'nama' => 'Pak Kepala',
                'email' => 'kepala@example.com',
                'password' => Hash::make('kepala123'),
                'role' => 'kepala_sekolah',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nip' => 'G003',
                'nama' => 'Rina Dewi',
                'email' => 'rina@example.com',
                'password' => Hash::make('guru123'),
                'role' => 'guru',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 2️⃣ GURU
        DB::table('guru')->insert([
            [
                'nip' => 'G001',
                'nama' => 'Budi Santoso',
                'jenis_kelamin' => 'P',
                'alamat'=>'kajarta',
                'no_hp'=>'000000003',
                'email' => 'budi@example.com',
                'mapel'=>'matematika',
                'id_user' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nip' => 'G002',
                'nama' => 'Siti Rahma',
                'jenis_kelamin' => 'L',
                'alamat'=>'tanegran',
                'no_hp'=>'000000002',
                'email' => 'siti@example.com',
                'mapel'=>'bahas inggris',
                'id_user' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nip' => 'G003',
                'nama' => 'Rina Dewi',
                'jenis_kelamin' => 'L',
                'alamat'=>'belanda',
                'no_hp'=>'000000001',
                'email' => 'rina@example.com',
                'mapel'=>'ipa',
                'id_user' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 3️⃣ KELAS
        DB::table('kelas')->insert([
            [
                'nama_kelas' => 'Kelas 1A',
                'tahun_ajaran'=>'2025/2026',
                'id_wali_kelas' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_kelas' => 'Kelas 2B',
                'tahun_ajaran'=>'2025/2026',
                'id_wali_kelas' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            
            ],
            
            
        ]);

        // 4️⃣ ORANG TUA
        DB::table('orangtua')->insert([
            [
                'nama' => 'Andi Pratama',
                'email' => 'andi.ortu@example.com',
                'no_wa' => '6281234567890',
                'preferensi_notif' => json_encode(['absensi', 'nilai']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Nur Aisyah',
                'email' => 'nur.ortu@example.com',
                'no_wa' => '6282234567890',
                'preferensi_notif' => json_encode(['absensi']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 5️⃣ SISWA AWAL
        DB::table('siswa')->insert([
            [
                'nis' => 'S001',
                'nama' => 'Ali Akbar',
                'tanggal_lahir' => '2015-01-15',
                'id_kelas' => 1,
                'qr_code' => Str::uuid(),
                'id_orangtua' => 1,
                'aktif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nis' => 'S002',
                'nama' => 'Bella Salsabila',
                'tanggal_lahir' => '2015-03-20',
                'id_kelas' => 1,
                'qr_code' => Str::uuid(),
                'id_orangtua' => 2,
                'aktif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 6️⃣ GENERATE OTOMATIS SISWA TAMBAHAN (50 SISWA)
        // \App\Models\Siswa::factory(50)->create();

        // 7️⃣ ABSENSI (contoh scan QR)
        DB::table('absensi')->insert([
            [
                'nis' => 'S001',
                'id_user' => 2,
                'tanggal' => now()->toDateString(),
                'jam' => now()->toTimeString(),
                'status' => 'hadir',
                'sumber' => 'scan',
                'lokasi' => 'Gerbang Utama',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nis' => 'S002',
                'id_user' => 3,
                'tanggal' => now()->toDateString(),
                'jam' => now()->subMinutes(5)->toTimeString(),
                'status' => 'terlambat',
                'sumber' => 'scan',
                'lokasi' => 'Pintu Barat',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
