<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class GuruMapelKelas extends Pivot
{
    use HasFactory;
    
    // Nama tabel pivot
    protected $table = 'guru_mapel_kelas'; 

    // Kolom-kolom yang boleh diisi (fillable)
    protected $fillable = [
        'id_guru',
        'id_mapel',
        'id_kelas',
    ];

    // Menonaktifkan incrementing karena ini adalah tabel pivot
    public $incrementing = true; 

    // Relasi ke Guru
    public function guru()
    {
        return $this->belongsTo(Guru::class, 'id_guru', 'id_guru');
    }

    // Relasi ke Mata Pelajaran
    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class, 'id_mapel', 'id_mapel');
    }

    // Relasi ke Kelas
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id_kelas');
    }
}