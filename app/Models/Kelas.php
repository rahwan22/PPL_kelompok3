<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_kelas';
    protected $fillable = ['nama_kelas', 'tahun_ajaran', 'id_wali_kelas'];

    // public function waliKelas()
    // {
    //     return $this->belongsTo(Guru::class, 'id_guru', 'id_guru');
    // }
    public function waliKelas()
    {
        return $this->belongsTo(Guru::class, 'id_wali_kelas', 'id_guru');
    }


    public function siswa()
    {
        return $this->hasMany(Siswa::class, 'id_kelas', 'id_kelas');
    }
}
