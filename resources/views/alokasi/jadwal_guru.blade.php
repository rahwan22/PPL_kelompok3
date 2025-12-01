@extends('layouts.app')

@section('title', 'Jadwal Mengajar Saya')

@section('content')

<div class="container mx-auto p-4 max-w-4xl">
    <h1 class="text-3xl font-extrabold mb-6 text-indigo-800 border-b-2 border-indigo-200 pb-2">
        Jadwal Mengajar Saya
    </h1>

    {{-- Kartu Info Guru --}}
    <div class="bg-white shadow-xl rounded-xl p-6 mb-8 border-t-4 border-indigo-500">
        <h2 class="text-xl font-semibold text-gray-700">
            Nama Guru
        </h2>
        <p class="text-3xl font-bold text-indigo-600 mt-1">
            {{ $guru->nama ?? 'Nama Guru Tidak Ditemukan' }}
        </p>
        @if (!($guru instanceof \App\Models\Guru) || $guru->id_guru === null)
            <p class="mt-2 text-sm text-red-500 bg-red-50 p-2 rounded-lg">
                ⚠️ Peringatan: Data guru Anda belum disinkronkan sepenuhnya di database (tabel `guru`).
            </p>
        @endif
    </div>

    {{-- Tabel Jadwal --}}
    <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200">
        <div class="bg-indigo-50 px-6 py-3">
            <h3 class="text-lg font-semibold text-indigo-700">Alokasi Mata Pelajaran dan Kelas</h3>
        </div>
        
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No.</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mata Pelajaran</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas Diajar</th>
                    {{-- Kolom Aksi yang hilang sudah dikembalikan di sini --}}
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                {{-- Looping data jadwal --}}
                @forelse ($jadwal as $index => $item)
                    <tr class="hover:bg-indigo-50 transition duration-150 ease-in-out">
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $index + 1 }}</td>
                        
                        {{-- Memastikan relasi ada sebelum mengakses properti --}}
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">
                            {{ $item->mataPelajaran->nama_mapel ?? 'Mapel Tidak Ditemukan' }}
                        </td>
                        
                        <td class="px-6 py-4 text-sm text-gray-700">
                            {{ $item->kelas->nama_kelas ?? 'Kelas Tidak Ditemukan' }}
                        </td>
                        
                        {{-- ** Perubahan Utama di sini: Kolom Aksi ** --}}
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                            {{-- Gunakan $item->id_kelas untuk mendapatkan ID kelas yang sedang diajar --}}
                            {{-- Asumsi Anda memiliki route bernama 'kelas.show' yang menerima ID kelas --}}
                            <a href="{{ route('kelas.show', $item->id_kelas) }}" 
                               class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-semibold rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition ease-in-out duration-150">
                                Absen Sekarang
                            </a>
                        </td>
                
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500 bg-gray-50">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1-1.385 15.655a2 2 0 0 0 1.99 2.345h17.15a2 2 0 0 0 1.99-2.345L21 3H3z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada jadwal</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Anda belum memiliki alokasi pengajaran yang tercatat.
                            </p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection