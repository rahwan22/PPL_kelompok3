@extends('layouts.app')

@section('content')

<div class="container-fluid py-4">

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">📜 Detail Nilai Akademik</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-1"><strong>Nama Siswa:</strong> {{ $siswa->nama }}</p>
                    <p class="mb-1"><strong>NIS:</strong> {{ $siswa->nis }}</p>
                </div>
                <div class="col-md-6 text-md-right">
                    <a href="{{ route('siswa.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Kembali ke Data Siswa
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card shadow">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0">Daftar Nilai yang Diinput Guru</h5>
        </div>
        <div class="card-body p-0">
            @if ($data_nilai->isEmpty())
                <div class="alert alert-warning m-4" role="alert">
                    <i class="fas fa-exclamation-triangle"></i> Belum ada data nilai yang diinput untuk siswa ini.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>#</th>
                                <th>Semester</th>
                                <th>Mata Pelajaran</th>
                                <th>Kelas</th>
                                <th class="text-center">Tugas</th>
                                <th class="text-center">UTS</th>
                                <th class="text-center">UAS</th>
                                <th class="text-center">Nilai Akhir</th>
                                <th class="text-center">Predikat</th> {{-- ⭐ Kolom Baru: Predikat --}}
                                <th>Catatan Guru</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data_nilai as $index => $nilai)
                                @php
                                    // Tentukan Predikat berdasarkan Nilai Akhir
                                    $predikat = '';
                                    $badge_class = 'secondary';
                                    if ($nilai->nilai_akhir >= 90) { $predikat = 'A'; $badge_class = 'success'; }
                                    elseif ($nilai->nilai_akhir >= 80) { $predikat = 'B'; $badge_class = 'primary'; }
                                    elseif ($nilai->nilai_akhir >= 70) { $predikat = 'C'; $badge_class = 'warning'; }
                                    else { $predikat = 'D'; $badge_class = 'danger'; }
                                @endphp

                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $nilai->semester }}</td>
                                    <td><strong>{{ $nilai->mapel->nama_mapel }}</strong></td>
                                    <td><strong>{{ $nilai->kelas?->nama_kelas ?? '—' }}</strong></td>
                                    <td class="text-center">{{ $nilai->nilai_tugas }}</td>
                                    <td class="text-center">{{ $nilai->nilai_uts }}</td>
                                    <td class="text-center">{{ $nilai->nilai_uas }}</td>
                                    <td class="text-center"><strong>{{ number_format($nilai->nilai_akhir, 2) }}</strong></td>
                                    
                                    {{-- Kolom Predikat --}}
                                    <td class="text-center">
                                        <span class="badge text-bg-dark">{{ $predikat }}</span>
                                    </td>
                                    
                                    <td>{{ $nilai->catatan ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection