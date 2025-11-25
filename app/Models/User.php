<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id_user';
    public $incrementing = true; 
    protected $keyType = 'int';

    protected $fillable = [
        'nip',
        'nama',
        'email',
        'password',
        'role',
        'status_aktif',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function kelasWali()
    {
        return $this->hasOne(\App\Models\Kelas::class, 'id_kelas_wali', 'id_duru');
    }
    public function guru()
    {
        // Asumsi: foreign key di tabel 'guru' adalah 'user_id'
        return $this->hasOne(Guru::class, 'user_id', 'id');
    }

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'status_aktif' => 'boolean', // Penting untuk tinyint(1)
    ];

    public $timestamps = true;
}
