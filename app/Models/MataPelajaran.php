<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MataPelajaran extends Model
{
    use HasFactory;

    // Definisikan primary key dan nama tabel
    protected $primaryKey = 'id_mapel';
    protected $table = 'mata_pelajaran';
    protected $guarded = ['id_mapel'];
    
    // Tentukan kolom yang dapat diisi secara massal (fillable)
    protected $fillable = [
        'nama_mapel',
        'kode_mapel',
        'tingkat',
    ];

    /**
     * Relasi ke Guru (melalui tabel pivot guru_mapel_kelas)
     * Ini menunjukkan guru mana saja yang mengajar mata pelajaran ini.
     */
    public function guruMengajar()
    {
        return $this->belongsToMany(
            Guru::class, 
            'guru_mapel_kelas', 
            'id_mapel', // Foreign key di tabel pivot yang merujuk ke MataPelajaran
            'id_guru'   // Foreign key di tabel pivot yang merujuk ke Guru
        )->withPivot('id_kelas'); // Sertakan id_kelas jika perlu tahu di kelas mana dia mengajar
    }
}