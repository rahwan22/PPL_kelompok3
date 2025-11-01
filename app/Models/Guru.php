<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    use HasFactory;

    protected $table = 'guru';
    // protected $primaryKey = 'id_guru';
    // protected $fillable = ['nip', 'nama', 'role', 'password_hash'];
    protected $primaryKey = 'id_guru';
    protected $fillable = ['nip', 'nama', 'jenis_kelamin', 'alamat', 'no_hp', 'email', 'mapel'];


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
        return $this->hasMany(Kelas::class, 'id_wali_kelas', 'id_guru');
    }

}


