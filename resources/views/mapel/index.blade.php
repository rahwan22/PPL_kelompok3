@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto p-6 bg-white shadow-xl rounded-xl mt-10">
    <div class="flex justify-between items-center mb-6 border-b pb-4">
        <h1 class="text-3xl font-extrabold text-gray-800">Daftar Mata Pelajaran</h1>
        <a href="{{ route('mapel.create') }}" class="px-4 py-2 text-sm font-semibold rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 shadow-md transition duration-150">
            + Tambah Mapel
        </a>
    </div>

    @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 border border-green-400 rounded-lg font-medium">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-4 p-4 bg-red-100 text-red-700 border border-red-400 rounded-lg font-medium">
            {{ session('error') }}
        </div>
    @endif

    <div class="overflow-x-auto shadow-md rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-indigo-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">No.</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Kode</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Nama Mata Pelajaran</th>
                    <!-- <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tingkat</th> -->
                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($mapels as $mapel)
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-indigo-600">{{ $mapel->kode_mapel }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $mapel->nama_mapel }}</td>
                        <!-- <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $mapel->tingkat ?? '-' }}</td> -->
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <div class="flex justify-center space-x-2">
                                <a href="{{ route('mapel.show', $mapel->id_mapel) }}" class="text-blue-600 hover:text-blue-900 transition duration-150 p-2 rounded-lg bg-blue-50">
                                    Lihat
                                </a>
                                <a href="{{ route('mapel.edit', $mapel->id_mapel) }}" class="text-yellow-600 hover:text-yellow-900 transition duration-150 p-2 rounded-lg bg-yellow-50">
                                    Edit
                                </a>
                                <form action="{{ route('mapel.destroy', $mapel->id_mapel) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus mata pelajaran ini? Semua data terkait (nilai, dll.) mungkin ikut terhapus.');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 transition duration-150 p-2 rounded-lg bg-red-50">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">Tidak ada data Mata Pelajaran.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection