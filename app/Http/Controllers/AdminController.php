<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Siswa;

class AdminController extends Controller
{
    public function index()
    {
        $totalGuru = Guru::count();
        $totalSiswa = Siswa::count();

        return view('admin.dashboard', [
            'title' => 'Dashboard Admin',
            'totalGuru' => $totalGuru,
            'totalSiswa' => $totalSiswa
        ]);
    }
}
