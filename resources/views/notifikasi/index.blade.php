@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4">Daftar Notifikasi</h3>
    <!-- <a href="{{ route('notifikasi.create') }}" class="btn btn-primary mb-3">+ Tambah Notifikasi Manual</a> -->

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
    @if($notifikasi->isEmpty())
        <div class="alert alert-info">Belum ada data notifikasi yang tercatat.</div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Tanggal/Waktu</th>
                    <th>Siswa (NIS)</th>
                    <th>Orang Tua</th>
                    <th>Pesan</th>
                    <th>Status Kirim</th> <!-- Menggantikan 'Status' lama -->
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($notifikasi as $n)
                    <tr>
                        <td>{{ $n->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $n->siswa->nama ?? 'Siswa Tidak Ditemukan' }} ({{ $n->nis }})</td>
                        <td>{{ $n->orangtua->nama ?? 'Orang Tua Tidak Ditemukan' }} (ID: {{ $n->id_orangtua }})</td>
                        <td>
                            <!-- Membatasi panjang pesan agar tabel tidak melebar -->
                            {{ Str::limit($n->pesan, 80) }}
                        </td>
                        
                        <!-- Kolom Status Kirim (Menggunakan status_kirim dari Controller) -->
                        <td>
                            @php
                                $statusKirim = strtolower($n->status_kirim ?? 'pending');
                                $badgeClass = match ($statusKirim) {
                                    'terkirim' => 'bg-success',
                                    'gagal' => 'bg-danger',
                                    default => 'bg-warning', // pending atau status lain
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }}">
                                {{ ucfirst($statusKirim) }}
                            </span>
                        </td>
                        
                        <!-- Kolom Aksi (Tombol) -->
                        
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection