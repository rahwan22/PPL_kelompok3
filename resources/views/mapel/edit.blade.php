@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6 bg-white shadow-xl rounded-xl mt-10">
    <h1 class="text-3xl font-bold text-gray-800 mb-6 border-b pb-2">Edit Mata Pelajaran: {{ $mapel->nama_mapel }}</h1>

    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-100 text-red-700 border border-red-400 rounded-lg">
            <p class="font-bold">Terjadi Kesalahan Validasi:</p>
            <ul class="list-disc ml-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('mapel.update', $mapel->id_mapel) }}" method="POST">
        @csrf
        @method('PUT') 
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            {{-- Nama Mata Pelajaran --}}
            <div>
                <label for="nama_mapel" class="block text-sm font-medium text-gray-700 mb-1">Nama Mata Pelajaran</label>
                <input type="text" name="nama_mapel" id="nama_mapel" value="{{ old('nama_mapel', $mapel->nama_mapel) }}" required
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('nama_mapel') border-red-500 @enderror">
                @error('nama_mapel')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            {{-- Kode Mata Pelajaran --}}
            <div>
                <label for="kode_mapel" class="block text-sm font-medium text-gray-700 mb-1">Kode Mata Pelajaran</label>
                <input type="text" name="kode_mapel" id="kode_mapel" value="{{ old('kode_mapel', $mapel->kode_mapel) }}" required
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('kode_mapel') border-red-500 @enderror">
                @error('kode_mapel')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tingkat --}}
            <div class="col-span-1">
                <label for="tingkat" class="block text-sm font-medium text-gray-700 mb-1">Tingkat (Kelas)</label>
                <select name="tingkat" id="tingkat"
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('tingkat') border-red-500 @enderror">
                    <option value="" {{ old('tingkat', $mapel->tingkat) == null ? 'selected' : '' }}>--- Semua Tingkat ---</option>
                    @foreach ($tingkat as $t)
                        <option value="{{ $t }}" {{ old('tingkat', $mapel->tingkat) == $t ? 'selected' : '' }}>
                            Kelas {{ $t }}
                        </option>
                    @endforeach
                </select>
                @error('tingkat')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            {{-- Kolom Kosong untuk mengisi grid --}}
            <div class="col-span-1"></div> 

        </div>

        <div class="mt-8 pt-4 border-t flex justify-end">
            <a href="{{ route('mapel.index') }}" class="px-6 py-2 text-sm font-semibold rounded-lg text-gray-600 border border-gray-300 hover:bg-gray-50 mr-3">Batal</a>
            <button type="submit" class="px-6 py-2 text-sm font-semibold rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 shadow-md transition duration-150 ease-in-out">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection