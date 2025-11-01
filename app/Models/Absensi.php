<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;

    protected $table = 'absensi';
    protected $primaryKey = 'id_absensi';
    protected $fillable = [
        'nis',
        'id_guru',
        'tanggal',
        'jam',
        'lokasi',
        'status',
        'sumber',
        'device_id',
        'synced',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'synced' => 'boolean',
    ];

    // 🔹 Relasi: Absensi milik satu siswa
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'nis', 'nis');
    }

    // 🔹 Relasi: Absensi dicatat oleh satu guru
    public function guru()
    {
        return $this->belongsTo(Guru::class, 'id_guru');
    }
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'id_admin');
    }

}
