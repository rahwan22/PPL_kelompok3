@extends('layouts.app')

@section('title', 'Jadwal Mengajar Saya')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-3xl font-bold mb-2 text-gray-800">Jadwal Mengajar</h1>
    <p class="text-xl text-indigo-600 mb-6">Guru: {{ $guru->nama }}</p>

    <div class="bg-white shadow-xl rounded-xl overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-indigo-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider">No.</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider">Mata Pelajaran</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider">Kelas</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($jadwal as $index => $item)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $index + 1 }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item->mataPelajaran->nama_mapel ?? 'Mapel Tidak Ditemukan' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $item->kelas->nama_kelas ?? 'Kelas Tidak Ditemukan' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-6 py-4 text-center text-gray-500">Anda belum memiliki alokasi mengajar. Silakan hubungi admin/kepala sekolah.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection