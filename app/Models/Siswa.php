<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\OrangTua;

class Siswa extends Model
{
    use HasFactory;

    protected $table = 'siswa';
    protected $primaryKey = 'nis';
    public $incrementing = false; // karena NIS bukan auto increment
    protected $keyType = 'string';

    protected $fillable = [
        'nis',
        'nama',
        'jenis_kelamin',
        'tanggal_lahir',
        'alamat',
        'foto',
        'id_kelas',
        'qr_code',
        'id_orangtua',
        'aktif',
    ];

    protected $casts = [
        'aktif' => 'boolean',
        'tanggal_lahir' => 'date',
    ];

    public function generateQr()
    {
        return \QrCode::size(200)->generate($this->nis);
    }


    // 🔹 Relasi: Siswa milik satu kelas
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas');
    }

    // 🔹 Relasi: Siswa memiliki satu orangtua
    public function orangtua()
    {
        return $this->belongsTo(Orangtua::class, 'id_orangtua', 'id_orangtua');
    }

    // 🔹 Relasi: Siswa memiliki banyak absensi
    public function absensi()
    {
        return $this->hasMany(Absensi::class, 'nis', 'nis');
    }

    // 🔹 Relasi: Siswa memiliki banyak notifikasi
    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class, 'nis', 'nis');
    }
}
