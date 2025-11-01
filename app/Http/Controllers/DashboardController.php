<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Nilai;
use App\Models\Absensi;

class DashboardController extends Controller
{
    // Form login
    public function loginForm()
    {
        return view('auth.login', ['title' => 'Login Sistem Sekolah']);
    }

    // Proses login (Revisi: Role Dihilangkan)
    public function loginPost(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            // Role tidak divalidasi karena diambil dari DB
        ]);

        // 1. Cari user berdasarkan email
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->with('error', 'Email tidak ditemukan.');
        }

        // 2. Cek password
        if (!Hash::check($request->password, $user->password)) {
            return back()->with('error', 'Password salah.');
        }

        // 3. Login menggunakan Laravel Auth
        auth()->login($user);

        // 4. Arahkan ke dashboard sesuai role yang ada di database
        // Logika pengalihan dipindahkan ke fungsi helper redirectToDashboard
        return $this->redirectToDashboard($user->role)
                    ->with('success', 'Selamat datang, ' . ucfirst(str_replace('_', ' ', $user->role)) . '!');
    }


    // Logout
    public function logout(Request $request)
    {
        auth()->logout(); // keluar dari sistem Laravel Auth
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Berhasil logout!');
    }

    // Dashboard umum, redirect sesuai role
    public function index()
    {
        // Pastikan user sudah login
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        $user = auth()->user();
        return $this->redirectToDashboard($user->role);
    }

    // Fungsi helper untuk redirect dashboard
    private function redirectToDashboard($role)
    {
        switch ($role) {
            case 'admin':
                return redirect()->route('dashboard.admin');
            case 'kepala_sekolah':
                return redirect()->route('dashboard.kepsek');
            case 'guru':
                return redirect()->route('dashboard.guru');
            default:
                // Jika role tidak dikenal (ini untuk pencegahan error)
                auth()->logout();
                return redirect()->route('login')->with('error', 'Role pengguna tidak valid. Silakan hubungi admin.');
        }
    }

    // Dashboard admin
    public function admin()
    {
        // Pengecekan Middleware/Logic Role (Pastikan Anda menggunakan middleware di routes/web.php)
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Akses ditolak. Anda bukan Admin.');
        }

        $data = [
            'title' => 'Dashboard Admin',
            'total_siswa' => Siswa::count(),
            'total_guru' => Guru::count(),
            'total_kelas' => Kelas::count(),
            'total_nilai' => Nilai::count(),
            'total_absensi' => Absensi::count(),
        ];

        return view('dashboard.admin', $data);
    }

    // Dashboard kepala sekolah
    public function kepsek()
    {
        // Pengecekan Middleware/Logic Role
        if (auth()->user()->role !== 'kepala_sekolah') {
            abort(403, 'Akses ditolak. Anda bukan Kepala Sekolah.');
        }
        
        $data = [
            'title' => 'Dashboard Kepala Sekolah',
            'total_siswa' => Siswa::count(),
            'total_guru' => Guru::count(),
            'total_kelas' => Kelas::count(),
        ];

        return view('dashboard.kepsek', $data);
    }

    // Dashboard guru
    public function guru()
    {
        // Pengecekan Middleware/Logic Role
        if (auth()->user()->role !== 'guru') {
            abort(403, 'Akses ditolak. Anda bukan Guru.');
        }

        $data = [
            'title' => 'Dashboard Guru',
            'total_siswa' => Siswa::count(),
            'total_nilai' => Nilai::count(),
            'total_absensi' => Absensi::count(),
        ];

        return view('dashboard.guru', $data);
    }
}
