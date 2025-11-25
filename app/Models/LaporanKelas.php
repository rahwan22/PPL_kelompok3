<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaporanKelas extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'laporan_kelas';

    // Primary key
    protected $primaryKey = 'id_laporan_kelas';

    // Kolom yang dapat diisi secara massal (mass assignable)
    protected $fillable = [
        'id_kelas',
        'id_user',
        'periode_awal',
        'periode_akhir',
        'total_siswa',
        'total_hadir',
        'total_terlambat',
        'total_izin',
        'total_sakit',
        'total_alpa',
        'catatan',
        'status',
    ];

    /**
     * Relasi ke model Kelas (LaporanKelas belongsTo Kelas)
     */
    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id_kelas');
    }

    /**
     * Relasi ke model User (Siapa yang membuat laporan, biasanya guru atau admin)
     */
    public function user(): BelongsTo
    {
        // Diasumsikan id_user merujuk ke tabel users
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}