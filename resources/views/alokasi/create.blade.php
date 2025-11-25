@extends('layouts.app')

@section('title', 'Tambah Alokasi Pengajaran')

@section('content')
<div class="container mx-auto p-4 max-w-lg">
    <h1 class="text-3xl font-bold mb-6 text-gray-800">Tambah Alokasi Pengajaran Baru</h1>

    @if (session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-md" role="alert">
            {{ session('error') }}
        </div>
    @endif
    
    @if ($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-md">
            <p class="font-bold">Terjadi Kesalahan Validasi:</p>
            <ul class="list-disc list-inside mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white p-8 shadow-xl rounded-xl">
        <form action="{{ route('alokasi.store') }}" method="POST">
            @csrf

            {{-- 1. PILIH GURU --}}
            <div class="mb-5">
                <label for="id_guru" class="block text-sm font-medium text-gray-700 mb-2">Pilih Guru Pengajar:</label>
                <select name="id_guru" id="id_guru" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" required>
                    <option value="">-- Pilih Guru --</option>
                    @foreach ($gurus as $guru)
                        <option value="{{ $guru->id_guru }}" {{ old('id_guru') == $guru->id_guru ? 'selected' : '' }}>
                            {{ $guru->nama }} ({{ $guru->nip }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- 2. PILIH MATA PELAJARAN --}}
            <div class="mb-5">
                <label for="id_mapel" class="block text-sm font-medium text-gray-700 mb-2">Pilih Mata Pelajaran:</label>
                {{-- Kita akan memuat semua Mapel untuk filter Kelas nanti --}}
                <select name="id_mapel" id="id_mapel" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" required>
                    <option value="">-- Pilih Mata Pelajaran --</option>
                    @foreach ($mapels as $mapel)
                        <option value="{{ $mapel->id_mapel }}" {{ old('id_mapel') == $mapel->id_mapel ? 'selected' : '' }}>
                            {{ $mapel->nama_mapel }} ({{ $mapel->kode_mapel }})
                        </option>
                    @endforeach
                </select>
                <p id="mapel-info" class="text-xs mt-1 text-gray-500 hidden">Memuat Kelas yang tersedia...</p>
            </div>

            {{-- 3. PILIH KELAS (AKAN DIISI VIA AJAX) --}}
            <div class="mb-6">
                <label for="id_kelas" class="block text-sm font-medium text-gray-700 mb-2">Pilih Kelas yang Belum Diajar Mapel Ini:</label>
                <select name="id_kelas" id="id_kelas" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" required disabled>
                    <option value="">-- Pilih Mapel & Guru terlebih dahulu --</option>
                </select>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('alokasi.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-lg transition duration-300">
                    Batal
                </a>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-300">
                    Simpan Alokasi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const guruSelect = document.getElementById('id_guru');
    const mapelSelect = document.getElementById('id_mapel');
    const kelasSelect = document.getElementById('id_kelas');
    const mapelInfo = document.getElementById('mapel-info');

    // Fungsi untuk mengambil dan memuat daftar Kelas
    function loadAvailableClasses() {
        const id_guru = guruSelect.value;
        const id_mapel = mapelSelect.value;
        
        kelasSelect.innerHTML = '<option value="">Memuat...</option>';
        kelasSelect.disabled = true;
        mapelInfo.classList.remove('hidden');

        // Pastikan Guru dan Mapel sudah dipilih
        if (!id_guru || !id_mapel) {
            kelasSelect.innerHTML = '<option value="">Pilih Guru & Mapel terlebih dahulu</option>';
            mapelInfo.classList.add('hidden');
            return;
        }

        // 1. Lakukan permintaan AJAX
        fetch(`/alokasi/available-kelas?guru_id=${id_guru}&mapel_id=${id_mapel}`)
            .then(response => response.json())
            .then(data => {
                let options = '<option value="">-- Pilih Kelas --</option>';
                
                // 2. Isi dropdown Kelas dengan data yang tersedia
                if (data.length > 0) {
                    data.forEach(kelas => {
                        options += `<option value="${kelas.id_kelas}">${kelas.nama_kelas}</option>`;
                    });
                    kelasSelect.disabled = false;
                } else {
                    options = '<option value="">Semua Kelas sudah diajar Mapel ini oleh Guru lain, atau Guru ini sudah mengajar Mapel ini di semua Kelas.</option>';
                    kelasSelect.disabled = true;
                }
                
                kelasSelect.innerHTML = options;
                mapelInfo.classList.add('hidden');
            })
            .catch(error => {
                console.error('Error fetching available classes:', error);
                kelasSelect.innerHTML = '<option value="">Gagal memuat Kelas</option>';
                mapelInfo.classList.add('hidden');
            });
    }

    // Event listeners
    guruSelect.addEventListener('change', loadAvailableClasses);
    mapelSelect.addEventListener('change', loadAvailableClasses);
    
    // Cek pada saat load awal jika ada old input
    if (guruSelect.value && mapelSelect.value) {
        loadAvailableClasses();
    }
});
</script>
@endsection