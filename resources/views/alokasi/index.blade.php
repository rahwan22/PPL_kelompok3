@extends('layouts.app')

@section('title', 'Alokasi Pengajaran Guru')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/alokasi.css') }}">
<div class="container mx-auto p-4">
    <h1 class="text-3xl font-bold mb-6 text-gray-800">Daftar Alokasi Pengajaran</h1>

    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded-md" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex justify-between items-center mb-4">
        <a href="{{ route('alokasi.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-300">
            + Tambah Alokasi Baru
        </a>
    </div>

    @php
        $alokasiGrouped = $alokasi->groupBy('id_guru');
    @endphp

    <div class="space-y-4">
        @forelse ($alokasiGrouped as $id_guru => $alokasiPerGuru)
            <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200">
                
                {{-- 1. Header Group (Toggle Button) --}}
                <div class="alokasi-header flex justify-between items-center bg-gray-100 px-6 py-4 cursor-pointer hover:bg-gray-200 transition duration-150" data-target="detail-{{ $id_guru }}">
                    <h2 class="text-xl font-semibold text-indigo-700 select-none">
                        Guru: {{ $alokasiPerGuru->first()->guru->nama ?? 'Guru Tidak Ditemukan' }}
                    </h2>
                    {{-- Icon panah (rotasi dengan JS) --}}
                    <svg class="alokasi-icon w-5 h-5 text-indigo-600 transition-transform duration-300 transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>

                {{-- 2. Detail Konten (Tabel yang disembunyikan secara default) --}}
                <div id="detail-{{ $id_guru }}" class="alokasi-detail hidden overflow-hidden transition-max-height duration-500 ease-in-out">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mata Pelajaran</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas Diajar</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($alokasiPerGuru as $item)
                                <tr>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $item->mataPelajaran->nama_mapel ?? 'Mapel Tidak Ditemukan' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700">{{ $item->kelas->nama_kelas ?? 'Kelas Tidak Ditemukan' }}</td>
                                    <td class="px-6 py-4 text-right text-sm font-medium">
                                        <form action="{{ route('alokasi.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus alokasi ini? Tindakan ini tidak dapat dibatalkan.');">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="text-red-600 hover:text-red-900 transition duration-150">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="bg-white shadow-xl rounded-xl p-6 text-center text-gray-500">
                Belum ada alokasi pengajaran yang dibuat.
            </div>
        @endforelse
    </div>
</div>


<script src="{{ asset('assets/js/alokasi.js') }}" defer></script>
@endsection