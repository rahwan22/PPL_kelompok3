<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard Sistem Sekolah SD' }}</title>
    {{-- Memuat Tailwind CSS (jika Anda tidak menggunakan CLI, gunakan CDN) --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Memuat Font Awesome (untuk ikon) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        xin-tegrity="sha512-SnH5WK+bZxgPHs44uWIX+LLMDJd7wW2YJ0z0VvWq/nJj/n8/I/8+OqQ4P4R2P1kR4P2P2O3P1P1P1Q=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  
    <link rel="stylesheet" href="{{ asset('assets/css/layout.css') }}">
    <script src="{{ asset('assets/js/layout.js') }}" defer></script>

    
</head>

<body>

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

            <div class="col-md-2 sidebar">
                <ul class="nav flex-column">
                    @php
                        $role = auth()->user()->role ?? '';
                    @endphp

                    @if ($role == 'admin')
                        <h2>Dashboard Admin</h2>
                        <li class="nav-item"><a class="nav-link" href="{{ route('dashboard.admin') }}"><i
                                        class="bi bi-speedometer2"></i> Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('guru.index') }}"><i
                                        class="bi bi-person-badge"></i> Semua Guru</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('siswa.index') }}"><i
                                        class="bi bi-people"></i>DATA Siswa/kelas </a></li>

                        <li class="nav-item"><a class="nav-link" href="{{ route('kelas.index') }}"><i
                                        class="bi bi-building"></i> Semua Kelas</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('mapel.index') }}"><i
                                        class="bi bi-journal-bookmark"></i> Semua Mapel</a></li>

                        
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('alokasi.index') }}">
                                <i class="bi bi-calendar-check"></i> Alokasi Mengajar
                            </a>
                        </li>
                        
                        @elseif($role == 'kepala_sekolah')
                        <h2>Dashboard Kepala Sekolah</h2>
                        <li class="nav-item"><a class="nav-link" href="{{ route('dashboard.kepsek') }}"><i
                                        class="bi bi-speedometer2"></i> Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('laporan.guru') }}"><i
                                        class="bi bi-person-badge"></i> Semua Guru</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('laporan.siswa') }}"><i
                                        class="bi bi-people"></i> Semua Siswa</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('laporan.mapel') }}"><i
                                        class="bi bi-journal-bookmark"></i> Semua Mapel</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('laporan.absensi') }}"><i
                                        class="bi bi-card-checklist"></i> Semua Absensi</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('laporan.kelas') }}"><i
                                        class="bi bi-people"></i> Lihat Kelas</a></li>

                    @elseif($role == 'guru')
                        <h2 ca;>DASHBOARD GURU</h2>
                        <li class="nav-item"><a class="nav-link" href="{{ route('dashboard.guru') }}"><i
                                        class="bi bi-speedometer2"></i> Dashboard</a></li>
                        <!--  -->
                        <li class="nav-item"><a class="nav-link" href="{{ route('absensi.index') }}"><i
                                        class="bi bi-card-checklist"></i> LIhat Absensi</a></li>

                        <li class="nav-item"><a class="nav-link" href="{{ route('jadwal.guru.saya') }}"><i
                                        class="bi bi-people"></i> Alokasi Kelas</a></li>

                        <li class="nav-item"><a class="nav-link" href="{{ route('nilai.index') }}"><i
                                        class="bi bi-pencil-square"></i> Input Nilai Lapor</a></li>

                        <li class="nav-item"><a class="nav-link" href="{{ route('notifikasi.index') }}"><i
                                        class="bi bi-pencil-square"></i> Notifikasi</a></li>


                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('password.edit') }}">
                                <i class="bi bi-lock"></i> Ubah Sandi
                            </a>
                        </li>
                    @endif
                </ul>
            </div>

            <div class="col-md-10 content">
                @yield('content')
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>