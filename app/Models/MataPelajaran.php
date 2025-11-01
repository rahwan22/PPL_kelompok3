<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MataPelajaran extends Model
{
    use HasFactory;
    

    protected $table = 'mata_pelajaran';
    protected $primaryKey = 'id_mapel';

    protected $fillable = [
        'nama_mapel',
        'kode_mapel',
        'id_guru',
    ];

    // 🔗 Relasi ke model Guru
    public function guru()
    {
        return $this->belongsTo(Guru::class, 'id_guru', 'id_guru');
    }

    // 🔗 Relasi ke model Nilai
    public function nilai()
    {
        return $this->hasMany(Nilai::class, 'id_mapel', 'id_mapel');
    }
}
