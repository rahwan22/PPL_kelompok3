@extends('layouts.app')

{{-- Tambahkan CDN Chart.js. Idealnya ini diletakkan di layouts/app.blade.php jika Anda menggunakan @stack('scripts'). --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

@section('content')
<p><center>Selamat datang, Anda memiliki akses penuh ke semua data</center></p>

<!-- Bagian Kartu Ringkasan (Existing) -->
<div class="row mt-4">
    <div class="col-md-4">
        <div class="card p-3 text-white bg-success mb-3">
            <h5>Jumlah Guru</h5>
            <p>{{ \App\Models\Guru::count() }}</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 text-white bg-success mb-3">
            <h5 >Jumlah Siswa</h5>
            <p>{{ \App\Models\Siswa::count() }}</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 text-white bg-success mb-3">
            <h5>Jumlah Kelas</h5>
            <p>{{ \App\Models\Kelas::count() }}</p>
        </div>
    </div>
</div>

<!-- Bagian Grafik Jumlah Siswa Tahunan (New) -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card p-4">
            <h5 class="mb-3">Grafik Jumlah Siswa per Tahun Ajaran</h5>
            <canvas id="studentChart"></canvas>
        </div>
    </div>
</div>

<script>
    // --- Data Siswa Tahunan (Data Mockup/Contoh) ---
    // PENTING: Dalam aplikasi Laravel yang sebenarnya, Anda harus mendapatkan data ini dari Controller
    // dan meneruskannya ke Blade menggunakan syntax PHP:
    
    
    // Data contoh untuk demo:
    const annualData = {!! json_encode($siswaTahunan) !!};

    const labels = annualData.map(item => `Tahun ${item.year}`);
    const counts = annualData.map(item => item.count);

    const ctx = document.getElementById('studentChart');

    if (ctx) {
        new Chart(ctx, {
            type: 'bar', // Menggunakan Bar Chart untuk perbandingan tahunan
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Siswa',
                    data: counts,
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.7)', // Warna untuk tahun 2022
                        'rgba(153, 102, 255, 0.7)', // Warna untuk tahun 2023
                        'rgba(255, 159, 64, 0.7)' // Warna untuk tahun 2024
                    ],
                    borderColor: [
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true, // Pastikan grafik responsif
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Jumlah Siswa'
                        },
                        ticks: {
                            // Memastikan nilai pada sumbu Y adalah bilangan bulat
                            callback: function(value) { if (value % 1 === 0) { return value; } }
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Tahun Ajaran'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    },
                    title: {
                        display: false,
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += context.raw;
                                return label + ' Siswa';
                            }
                        }
                    }
                }
            }
        });
    }
</script>

@endsection