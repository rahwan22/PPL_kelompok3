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
    <style>
        /* ====== RESET & UMUM ====== */
        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #e9f0ff, #ffffff);
            color: #333;
            overflow-x: hidden;
        }

        h2 {
            color: #fff;
            font-size: 1.2rem;
            text-align: center;
            margin-bottom: 1.5rem;
            letter-spacing: 0.5px;
        }

        /* ====== NAVBAR ====== */
        .navbar {
            background: linear-gradient(90deg, #3a0ca3, #4361ee);
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.15);
            z-index: 10;
            position: sticky;
            top: 0;
        }

        .navbar-brand {
            font-weight: 700;
            color: #fff !important;
        }

        .navbar .nav-link {
            color: #fff !important;
            font-weight: 500;
            transition: 0.3s;
        }

        .navbar .nav-link:hover {
            color: #ffd60a !important;
        }

        /* ====== SIDEBAR ====== */
        .sidebar {
            background: linear-gradient(180deg, #3a0ca3, #5f0f40);
            min-height: 100vh;
            padding: 25px 15px;
            color: #fff;
        }

        .sidebar .nav-link {
            color: #f0f0f0;
            display: flex;
            align-items: center;
            padding: 10px 15px;
            border-radius: 8px;
            transition: 0.3s;
            font-weight: 500;
            font-size: 0.95rem;
        }

        .sidebar .nav-link i {
            margin-right: 10px;
            font-size: 1.1rem;
        }

        .sidebar .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.15);
            color: #fff;
            transform: translateX(5px);
        }

        .sidebar h2 {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        /* ====== CONTENT ====== */
        .content {
            background-color: #ffffff;
            border-radius: 12px;
            padding: 30px;
            margin-top: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            animation: fadeIn 0.4s ease-in-out;
            min-height: calc(100vh - 80px);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ====== RESPONSIVE ====== */
        @media (max-width: 992px) {
            .sidebar {
                min-height: auto;
                text-align: center;
                padding: 15px 10px;
            }

            .sidebar .nav-link {
                justify-content: center;
                font-size: 0.9rem;
            }

            .sidebar .nav-link i {
                margin: 0 0 5px 0;
                display: block;
            }

            .content {
                margin: 10px;
                padding: 20px;
            }
        }

        @media (max-width: 768px) {
            .row {
                flex-direction: column;
            }

            .sidebar {
                min-height: auto;
            }

            .content {
                width: 100%;
                margin: 0;
            }
        }

        /* ====== TABLE STYLE ====== */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #3a0ca3;
            color: #fff;
        }

        th,
        td {
            padding: 12px 15px;
            text-align: left;
        }

        tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        tbody tr:hover {
            background-color: #edf2fb;
        }

        /* ====== BUTTON ====== */
        .btn-custom {
            background: linear-gradient(90deg, #4361ee, #7209b7);
            color: #fff;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            transition: 0.3s;
            font-weight: 500;
        }

        .btn-custom:hover {
            background: linear-gradient(90deg, #4cc9f0, #7209b7);
            transform: translateY(-2px);
        }
    </style>

    <script>
        function confirmDelete(nama, id) {
            // first ask: apakah ingin hapus guru?
            if (!confirm('Yakin hapus data guru ' + nama + '? Tekan OK untuk lanjut.')) {
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
                                        class="bi bi-people"></i> Semua Siswa</a></li>

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
                                        class="bi bi-person-badge"></i> Laporan Guru</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('laporan.siswa') }}"><i
                                        class="bi bi-people"></i> Laporan Siswa</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('laporan.mapel') }}"><i
                                        class="bi bi-journal-bookmark"></i> Semua Mapel</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('laporan.absensi') }}"><i
                                        class="bi bi-card-checklist"></i> Laporan Absensi</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('laporan.kelas') }}"><i
                                        class="bi bi-people"></i> Lihat Kelas</a></li>

                    @elseif($role == 'guru')
                        <h2 ca;>DASHBOARD GURU</h2>
                        <li class="nav-item"><a class="nav-link" href="{{ route('dashboard.guru') }}"><i
                                        class="bi bi-speedometer2"></i> Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('absensi.scan') }}"><i
                                        class="bi bi-qr-code-scan"></i> Absensi Siswa (QR)</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('absensi.index') }}"><i
                                        class="bi bi-card-checklist"></i> LIhat Absensi</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('nilai.index') }}"><i
                                        class="bi bi-pencil-square"></i> Input Nilai</a></li>

                        <li class="nav-item"><a class="nav-link" href="{{ route('lihat.kelas') }}"><i
                                        class="bi bi-people"></i> Lihat Kelas</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('lihat.mapel') }}"><i
                                        class="bi bi-journal-bookmark"></i> Lihat Mapel</a></li>

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