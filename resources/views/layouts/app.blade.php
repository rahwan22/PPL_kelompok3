<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard Sistem Sekolah SD' }}</title>
    {{-- Memuat Tailwind CSS (jika Anda tidak menggunakan CLI, gunakan CDN) --}}
    <script src="https://cdn.tailwindcss.com"></script>
    
    {{-- Memuat Font Awesome (untuk ikon) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" xintegrity="sha512-SnH5WK+bZxgPHs44uWIX+LLMDJd7wW2YJ0z0VvWq/nJj/n8/I/8+OqQ4P4R2P1kR4P2P2O3P1P1P1Q==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(to bottom, #a8e6cf, #ffffff);
        }
        .navbar {
            background-color: #370db5ff;
        }
        .navbar a {
            color: #fff !important;
        }
        .sidebar {
            background-color: #350d92ff;
            min-height: 100vh;
            padding-top: 20px;
        }
        .sidebar .nav-link {
            color: #fff;
            margin: 5px 0;
        }
        .sidebar .nav-link:hover {
            background-color: #2a40bdff;
            border-radius: 5px;
        }
        .content {
            padding: 20px;
        }
    </style>
    <script>
function confirmDelete(nama, id) {
    // first ask: apakah ingin hapus guru?
    if (!confirm('Yakin hapus data guru '+nama+'? Tekan OK untuk lanjut.')) {
        return;
    }
    // second ask: apakah akun user juga ikut dihapus?
    var delUser = confirm('Apakah akun login (user) guru ini juga ingin dihapus? OK = Ya, Cancel = Tidak');
    document.getElementById('delete_user_' + id).value = delUser ? '1' : '0';
    document.getElementById('delete-form-' + id).submit();
}
</script>

</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand" href="">Sistem Manajemen Sekolah SD</a>
        
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
            <li class="nav-item me-3 text-white">
                👋 Hai, {{ Auth::user()->nama ?? 'Pengguna' }}
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="{{ route('logout') }}"
                   onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                    Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
                    @csrf
                </form>
            </li>
        </ul>
        
    </div>
</nav>

    <div class="container-fluid">
        <div class="row">

            <!-- Sidebar -->
            <div class="col-md-2 sidebar">
                <ul class="nav flex-column">
                    @php
                        $role = auth()->user()->role ?? '';
                    @endphp

                    @if($role == 'admin')
                    <h2>Dashboard Admin</h2>
                        <li class="nav-item"><a class="nav-link" href="{{ route('dashboard.admin') }}">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('guru.index') }}">Semua Guru</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('siswa.index') }}">Semua Siswa</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('kelas.index') }}">Semua Kelas</a></li>
                        <!-- <li class="nav-item"><a class="nav-link" href="{{ route('absensi.index') }}"> Lihat Absensi</a></li> -->
                        <!-- // ... di bagian dashboard admin, guru, dan kepala sekolah -->
                        <li class="nav-item"><a class="nav-link" href="{{ route('mapel.index') }}">Semua Mapel</a></li>
                        <!-- <li class="nav-item"><a class="nav-link" href="{{ route('nilai.index') }}">Lihat Nilai</a></li> -->
                        

                    @elseif($role == 'kepala_sekolah')
                    <h2>Dashboard Kepala Sekolah</h2>
                        <li class="nav-item"><a class="nav-link" href="{{ route('dashboard.kepsek') }}">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('laporan.guru') }}">Laporan Guru</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('laporan.siswa') }}">Laporan Siswa</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('laporan.nilai') }}">Laporan Nilai</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('laporan.mapel') }}">Semua Mapel</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('laporan.absensi') }}">Laporan Absensi</a></li>

                    @elseif($role == 'guru')
                    <h2 ca;>DASHBOARD GURU</h2>
                        <li class="nav-item"><a class="nav-link" href="{{ route('dashboard.guru') }}">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('absensi.scan') }}">Absensi Siswa (QR)</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('absensi.index') }}">LIhat Absensi</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('kelas.index') }}">Lihat Kelas</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('guru.mapel') }}">Lihat Mapel</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('nilai.index') }}">Input Nilai</a></li>
                    @endif
                </ul>
            </div>

            <!-- Content -->
            <div class="col-md-10 content">
                @yield('content')
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
