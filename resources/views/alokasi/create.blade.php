@extends('layouts.app')

@section('title', 'Tambah Alokasi Pengajaran')

@section('content')

<div class="container mx-auto p-4">
    <h1 class="text-3xl font-bold mb-6 text-gray-800">Tambah Alokasi Pengajaran Baru</h1>

    <div class="bg-white shadow-xl rounded-xl p-6 md:p-8">
        <form action="{{ route('alokasi.store') }}" method="POST">
            @csrf

            {{-- Pesan Error Validasi (Biarkan seperti semula) --}}
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Gagal menyimpan!</strong>
                    <span class="block sm:inline">Periksa kembali input Anda.</span>
                    <ul class="mt-2 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- 1. Pilih Guru --}}
            <div class="mb-5">
                <label for="id_guru" class="block text-gray-700 text-sm font-semibold mb-2">Pilih Guru:</label>
                <select name="id_guru" id="id_guru" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('id_guru') border-red-500 @enderror">
                    <option value="">-- Pilih Guru --</option>
                    @foreach ($guru as $guruItem) {{-- Ubah nama variabel agar tidak menimpa variabel $guru di controller --}}
                        <option value="{{ $guruItem->id_guru }}" {{ old('id_guru') == $guruItem->id_guru ? 'selected' : '' }}>
                            {{ $guruItem->nama }}
                        </option>
                    @endforeach
                </select>
                @error('id_guru')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- 2. Pilih Mata Pelajaran --}}
            <div class="mb-5">
                <label for="id_mapel" class="block text-gray-700 text-sm font-semibold mb-2">Pilih Mata Pelajaran:</label>
                <select name="id_mapel" id="id_mapel" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('id_mapel') border-red-500 @enderror">
                    <option value="">-- Pilih Mata Pelajaran --</option>
                    @foreach ($mapels as $mapel)
                        <option value="{{ $mapel->id_mapel }}" {{ old('id_mapel') == $mapel->id_mapel ? 'selected' : '' }}>
                            {{ $mapel->nama_mapel }}
                        </option>
                    @endforeach
                </select>
                @error('id_mapel')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- 3. Pilih Kelas --}}
            <div class="mb-6">
                <label for="id_kelas" class="block text-gray-700 text-sm font-semibold mb-2">Pilih Kelas:</label>
                <select name="id_kelas" id="id_kelas" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('id_kelas') border-red-500 @enderror">
                    <option value="">-- Pilih Kelas --</option>
                    {{-- Opsi kelas akan dimuat ulang oleh JS --}}
                    {{-- Kita tetap bisa memuat semua opsi di sini sebagai fallback/untuk JS nanti --}}
                    @foreach ($kelas as $k)
                        <option 
                            value="{{ $k->id_kelas }}" 
                            data-mapel-id="0" {{-- Tambahkan data-mapel-id dummy --}}
                            class="kelas-option"
                            {{ old('id_kelas') == $k->id_kelas ? 'selected' : '' }}
                        >
                            {{ $k->nama_kelas }}
                        </option>
                    @endforeach
                </select>
                @error('id_kelas')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
                <p id="kelas-warning" class="text-sm text-yellow-600 mt-2 hidden">⚠️ Semua kelas untuk Mata Pelajaran ini sudah dialokasikan.</p>
            </div>

            {{-- Tombol Submit --}}
            <div class="flex items-center justify-between">
                <button type="submit" id="submit-button" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition duration-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Simpan Alokasi
                </button>
                <a href="{{ route('alokasi.index') }}" class="text-gray-500 hover:text-gray-700 font-semibold transition duration-150">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const mapelDropdown = document.getElementById('id_mapel');
        const kelasDropdown = document.getElementById('id_kelas');
        const kelasWarning = document.getElementById('kelas-warning');
        const submitButton = document.getElementById('submit-button');
        
        // Data alokasi yang sudah terpakai dari Controller
        const allocatedCombinations = @json($allocatedCombinations);
        
        // Simpan semua opsi kelas asli
        const allKelasOptions = Array.from(kelasDropdown.options).slice(1); // Abaikan opsi '-- Pilih Kelas --'

        function filterKelasOptions() {
            const selectedMapelId = mapelDropdown.value;
            let currentSelectedKelasId = kelasDropdown.value;
            let availableKelas = 0;

            // Bersihkan dan kembalikan hanya opsi default
            kelasDropdown.innerHTML = '<option value="">-- Pilih Kelas --</option>';

            if (selectedMapelId) {
                // Iterasi melalui semua opsi kelas
                allKelasOptions.forEach(option => {
                    const kelasId = option.value;
                    
                    // Cek apakah kombinasi Mapel dan Kelas sudah teralokasi
                    const isAllocated = allocatedCombinations.some(item => 
                        item.id_mapel == selectedMapelId && item.id_kelas == kelasId
                    );
                    
                    // Jika belum teralokasi, tampilkan opsi kelas tersebut
                    if (!isAllocated) {
                        const newOption = option.cloneNode(true);
                        kelasDropdown.appendChild(newOption);
                        availableKelas++;
                    }
                });
                
                // Coba pertahankan nilai yang dipilih sebelumnya jika masih ada
                if (kelasDropdown.querySelector(`option[value="${currentSelectedKelasId}"]`)) {
                     kelasDropdown.value = currentSelectedKelasId;
                } else {
                    // Reset pilihan kelas jika tidak lagi tersedia
                    kelasDropdown.value = '';
                }

            } else {
                // Jika Mata Pelajaran belum dipilih, kembalikan semua kelas (kecuali yang pertama)
                allKelasOptions.forEach(option => {
                    kelasDropdown.appendChild(option.cloneNode(true));
                });
            }

            // Tampilkan warning jika tidak ada kelas tersedia
            if (selectedMapelId && availableKelas === 0) {
                kelasWarning.classList.remove('hidden');
                submitButton.disabled = true;
                submitButton.classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                kelasWarning.classList.add('hidden');
                submitButton.disabled = false;
                submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        }

        // Jalankan fungsi saat Mapel diubah
        mapelDropdown.addEventListener('change', filterKelasOptions);

        // Jalankan fungsi saat halaman dimuat (untuk old() value)
        filterKelasOptions(); 
    });
</script>

@endsection