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
use Carbon\Carbon;


use Illuminate\Support\Facades\Auth;

use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    // Form login
    public function loginForm()
    {
        return view('auth.login', ['title' => 'Login Sistem Sekolah']);
    }

    // Proses login (Revisi: Menambahkan Login via NIP/Email)
    public function loginPost(Request $request)
    {
        $request->validate([
            // Input 'email' akan digunakan sebagai penampung untuk Email atau NIP
            'email' => 'required', // Hapus validasi 'email' format, karena bisa jadi NIP
            'password' => 'required',
        ]);

        $loginCredential = $request->email;

        // 1. Coba cari user berdasarkan email
        $user = User::where('email', $loginCredential)->first();

        // 2. Jika tidak ditemukan, coba cari user sebagai Guru berdasarkan NIP
        if (!$user) {
            // Asumsi: Jika NIP digunakan, field 'email' di form login berisi NIP
            $user = User::where('role', 'guru')
                        ->where('nip', $loginCredential)
                        ->first();
        }

        // 3. Cek apakah user ditemukan (baik dari email maupun NIP)
        if (!$user) {
            // Pesan error umum untuk keamanan
            return back()->with('error', 'Kombinasi Email/NIP dan Password salah.');
        }

        // 4. Cek password
        if (!Hash::check($request->password, $user->password)) {
            return back()->with('error', 'Kombinasi Email/NIP dan Password salah.');
        }

        // 5. Login menggunakan Laravel Auth
        auth()->login($user);

        // 6. Arahkan ke dashboard sesuai role
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

        $startYear = 2010;
        $endYear = Carbon::now()->year;
        $annualData = [];

        for ($year = $startYear; $year <= $endYear; $year++) {
            // Anggap Anda menggunakan kolom 'created_at' atau 'tahun_masuk'
            $count = DB::table('siswa')
                        ->whereYear('created_at', $year) // Atau kolom tanggal pendaftaran lain
                        ->count();
                        
            $annualData[] = [
                'year' => $year,
                'count' => $count
            ];
        }
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
            'siswaTahunan' => $annualData,
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

      
    public function editPassword()
    {
        $user = Auth::user();
        return view('profile.edit-password', compact('user'));
    }

    /**
     * Memperbarui sandi pengguna.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'], // Memeriksa sandi lama
            'password' => ['required', 'confirmed', Password::defaults()], // Sandi baru dan konfirmasi
        ], [
            'current_password.current_password' => 'Sandi lama salah.',
            'password.required' => 'Sandi baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi sandi tidak cocok.',
        ]);

        // Ambil user yang sedang login
        $user = Auth::user();

        // Update sandi
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Redirect dengan pesan sukses
        return redirect()->route('dashboard.guru')->with('success', 'Sandi berhasil diubah!');
    }
}