@extends('layouts.app')

@section('content')
<div class="container mt-4">

    <h3 class="text-center mb-4">Laporan Absensi Siswa SD</h3>
    <p class="text-center text-muted">Silakan pilih kelas:</p>

    <div class="row justify-content-center">
        <div class="col-md-7">

            <div class="list-group shadow-sm">

                {{-- Kelas 1 --}}
                <a href="{{ route('laporan.guru.kelas1') }}" class="list-group-item list-group-item-action d-flex justify-content-between">
                    <strong>Kelas 1</strong>
                    <span class="badge badge-info">32 Siswa</span>
                </a>

                {{-- Kelas 2 --}}
                <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between">
                    <strong>Kelas 2</strong>
                    <span class="badge badge-info">30 Siswa</span>
                </a>

                {{-- Kelas 3 --}}
                <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between">
                    <strong>Kelas 3</strong>
                    <span class="badge badge-info">29 Siswa</span>
                </a>

                {{-- Kelas 4 --}}
                <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between">
                    <strong>Kelas 4</strong>
                    <span class="badge badge-info">34 Siswa</span>
                </a>

                {{-- Kelas 5 --}}
                <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between">
                    <strong>Kelas 5</strong>
                    <span class="badge badge-info">31 Siswa</span>
                </a>

                {{-- Kelas 6 --}}
                <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between">
                    <strong>Kelas 6</strong>
                    <span class="badge badge-info">33 Siswa</span>
                </a>

            </div>
        </div>
    </div>

</div>
@endsection
