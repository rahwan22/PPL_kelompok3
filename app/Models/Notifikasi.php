<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    use HasFactory;

    protected $table = 'notifikasi';
    protected $primaryKey = 'id_notif';
    protected $fillable = [
        'id_orangtua',
        'nis',
        'jenis',
        'pesan',
        'status_kirim',
        'channel',
    ];

    // 🔹 Relasi: Notifikasi milik satu orangtua
    public function orangtua()
    {
        return $this->belongsTo(Orangtua::class, 'id_orangtua');
    }

    // 🔹 Relasi: Notifikasi terkait dengan satu siswa
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'nis', 'nis');
    }
}
