<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaporanSiswa extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'laporan_siswa';

    // Primary key
    protected $primaryKey = 'id_laporan_siswa';

    // Kolom yang dapat diisi secara massal (mass assignable)
    protected $fillable = [
        'nis', // Nomor Induk Siswa
        'id_kelas',
        'periode_awal',
        'periode_akhir',
        'hadir',
        'terlambat',
        'izin',
        'sakit',
        'alpa',
        'catatan',
        'status',
    ];

    /**
     * Relasi ke model Siswa (LaporanSiswa belongsTo Siswa)
     * Menggunakan 'nis' sebagai foreign key.
     */
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'nis', 'nis');
    }

    /**
     * Relasi ke model Kelas (Untuk mengetahui kelas saat laporan dibuat)
     */
    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id_kelas');
    }
}