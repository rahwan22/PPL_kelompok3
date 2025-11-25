<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas'; 
    protected $primaryKey = 'id_kelas'; 
    protected $guarded = ['id_kelas'];
    protected $fillable = ['nama_kelas', 'tahun_ajaran'];

    

    // public function waliKelas()
    // {
    //     return $this->belongsTo(Guru::class, 'id_guru', 'id_guru');
    // }
    public function waliKelas()
    {
        return $this->hasOne(Guru::class, 'id_kelas_wali', 'id_kelas');
    }


    public function siswa()
    {
        return $this->hasMany(Siswa::class, 'id_kelas', 'id_kelas');
    }
}
