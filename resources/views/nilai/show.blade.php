@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Detail Nilai Siswa</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Nama Siswa:</strong> {{ $nilai->siswa->nama ?? 'N/A' }}</p>
                    <p><strong>NIS:</strong> {{ $nilai->nis }}</p>
                    <p><strong>Kelas:</strong> {{ $nilai->kelas->nama_kelas ?? 'N/A' }}</p>
                    <p><strong>Tahun Ajaran Kelas:</strong> {{ $nilai->kelas->tahun_ajaran ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Mata Pelajaran:</strong> {{ $nilai->mapel->nama_mapel ?? 'N/A' }}</p>
                    <p><strong>Kode Mapel:</strong> {{ $nilai->mapel->kode_mapel ?? 'N/A' }}</p>
                    <p><strong>Semester:</strong> {{ $nilai->semester }}</p>
                   
                </div>
            </div>
            <hr>
            <h5>Komponen Nilai</h5>
            <table class="table table-bordered table-sm w-50">
                <tbody>
                    <tr><th>Nilai Tugas</th><td>{{ $nilai->nilai_tugas }}</td></tr>
                    <tr><th>Nilai UTS</th><td>{{ $nilai->nilai_uts }}</td></tr>
                    <tr><th>Nilai UAS</th><td>{{ $nilai->nilai_uas }}</td></tr>
                </tbody>
            </table>
            <p><strong>Hasil Nilai Akhir:</strong> <span class="badge bg-success fs-6">{{ number_format($nilai->nilai_akhir, 2) }}</span></p>
           
           
            <h5 class="mt-4">Catatan Guru</h5>
            <p class="alert alert-info">{{ $nilai->catatan ?? 'Tidak ada catatan.' }}</p>

            <div class="d-flex justify-content-end">
                <a href="{{ route('nilai.edit', $nilai->id_nilai) }}" class="btn btn-warning me-2"><i class="bi bi-pencil-square"></i> Edit</a>
                <a href="{{ route('nilai.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
            </div>
        </div>
    </div>
</div>
@endsection