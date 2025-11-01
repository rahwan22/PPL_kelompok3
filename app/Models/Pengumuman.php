<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengumuman extends Model
{
    use HasFactory;

    protected $table = 'pengumuman';
    protected $primaryKey = 'id_pengumuman';

    protected $fillable = [
        'judul',
        'isi',
        'id_user',
        'tanggal',
        'tujuan',
    ];

    public function pembuat()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
