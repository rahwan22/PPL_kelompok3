<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nilai extends Model
{
    use HasFactory;

    protected $table = 'nilai';
    protected $primaryKey = 'id_nilai';

    protected $fillable = [
        'nis',
        'id_mapel',
        'nilai_tugas',
        'nilai_uts',
        'nilai_uas',
        'nilai_akhir',
        'semester',
        'catatan',
    ];

    // 🔗 Relasi ke model Siswa
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'nis', 'nis');
    }

    // 🔗 Relasi ke model MataPelajaran
    public function mapel()
    {
        return $this->belongsTo(MataPelajaran::class, 'id_mapel', 'id_mapel');
    }
    
}
