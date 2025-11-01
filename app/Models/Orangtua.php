<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orangtua extends Model
{
    use HasFactory;

    protected $table = 'orangtua';
    protected $primaryKey = 'id_orangtua';

    protected $fillable = [
        'nama',
        'email',
        'no_wa',
        'preferensi_notif',
    ];

    protected $casts = [
        'preferensi_notif' => 'array',
    ];

    public function siswa()
    {
        return $this->hasMany(Siswa::class, 'id_orangtua', 'id_orangtua');
    }
}
