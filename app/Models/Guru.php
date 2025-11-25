<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    use HasFactory;

    // Pastikan nama tabel, Primary Key, dan tipe key sudah sesuai dengan skema DB Anda
    protected $table = 'guru'; 
    protected $primaryKey = 'id_guru';
    // Asumsi Primary Key bertipe integer/bigint
    public $incrementing = true;
    protected $keyType = 'int'; 

    protected $fillable = [
        'id_user', 
        'id_mapel', 
        'id_kelas_wali', 
        'nama', 
        'nip', 
        'email',
        'jenis_kelamin', 
        'alamat', 
        'no_hp', 
        'foto'
    ];

    // 🔹 Relasi: 1 Guru → Banyak Absensi
    public function absensi()
    {
        return $this->hasMany(Absensi::class, 'id_guru');
    }

    // 🔹 Relasi: 1 Guru bisa menjadi Wali dari 1 Kelas
    public function waliKelas()
    {
        return $this->hasOne(Kelas::class, 'id_guru_wali');
    }
    public function admin()
    {
        return $this->hasOne(Admin::class, 'id_guru');
    }
    public function kelas()
    {
        return $this->hasMany(Kelas::class, 'id_kelas_wali', 'id_guru');
    }

    public function kelasWali()
    {
        // Asumsi: Kelas memiliki kolom foreign key 'id_guru'
        return $this->belongsTo(Kelas::class, 'id_kelas_wali', 'id_kelas');
    }

    public function user()
    {
        // Parameter: Model, foreign key di tabel ini, local key di tabel tujuan (User)
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

}
