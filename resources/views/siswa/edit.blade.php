@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6 bg-white shadow-2xl rounded-xl mt-10">
    <h1 class="text-3xl font-extrabold text-indigo-800 mb-8 border-b-4 border-indigo-100 pb-3">
        Edit Data Siswa: {{ $siswa->nama }}
    </h1>

    {{-- Error/Validation Messages --}}
    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 text-red-700 border border-red-300 rounded-lg shadow-sm">
            <p class="font-bold">Terjadi Kesalahan Validasi:</p>
            <ul class="list-disc ml-5 space-y-1 mt-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form menggunakan method PUT/PATCH untuk update --}}
    <form action="{{ route('siswa.update', $siswa->nis) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT') 
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <h3 class="md:col-span-2 text-2xl font-semibold text-indigo-700 mt-4 mb-2 border-b pb-2">Data Siswa</h3>
            
            {{-- NIS (Dibuat readonly atau disabled agar tidak diubah) --}}
            <div>
                <label for="nis" class="block text-sm font-medium text-gray-700 mb-1">NIS</label>
                <input type="text" name="nis" id="nis" value="{{ $siswa->nis }}" readonly disabled
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-inner bg-gray-100 cursor-not-allowed">
                <p class="text-xs text-gray-500 mt-1">NIS tidak dapat diubah setelah data dibuat.</p>
            </div>

            {{-- Nama Siswa --}}
            <div>
                <label for="nama" class="block text-sm font-medium text-gray-700 mb-1">Nama Siswa <span class="text-red-500">*</span></label>
                <input type="text" name="nama" id="nama" value="{{ old('nama', $siswa->nama) }}" required
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('nama') border-red-500 @enderror">
                @error('nama')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Jenis Kelamin --}}
            <div class="mb-4">
                <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin <span class="text-red-500">*</span></label>
                <select name="jenis_kelamin" id="jenis_kelamin" required
                    class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('jenis_kelamin') border-red-500 @enderror">
                    {{-- Gunakan $siswa->jenis_kelamin sebagai nilai default --}}
                    <option value="L" {{ old('jenis_kelamin', $siswa->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="P" {{ old('jenis_kelamin', $siswa->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                </select>
                @error('jenis_kelamin')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tanggal Lahir --}}
            <div>
                <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir </label>
                <input type="date" name="tanggal_lahir" id="tanggal_lahir" value="{{ old('tanggal_lahir', $siswa->tanggal_lahir) }}"
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('tanggal_lahir') border-red-500 @enderror">
                @error('tanggal_lahir')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Alamat --}}
            <div class="md:col-span-2">
                <label for="alamat" class="block text-sm font-medium text-gray-700 mb-1">Alamat <span class="text-red-500">*</span></label>
                <textarea name="alamat" id="alamat" rows="2" required
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('alamat') border-red-500 @enderror">{{ old('alamat', $siswa->alamat) }}</textarea>
                @error('alamat')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>


            {{-- Kelas --}}
            <div class="md:col-span-2">
                <label for="id_kelas" class="block text-sm font-medium text-gray-700 mb-1">Kelas <span class="text-red-500">*</span></label>
                <select name="id_kelas" id="id_kelas" required
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('id_kelas') border-red-500 @enderror">
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($kelas as $k)
                    <option value="{{ $k->id_kelas }}" {{ old('id_kelas', $siswa->id_kelas) == $k->id_kelas ? 'selected' : '' }}>
                        {{ $k->nama_kelas }} (Tahun: {{ $k->tahun_ajaran }})
                    </option>
                    @endforeach
                </select>
                @error('id_kelas')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Foto -->
            <div class="md:col-span-2">
                <label for="foto" class="block text-sm font-medium text-gray-700 mb-1">Ganti Foto Profil (Maks 2MB, Opsional)</label>
                <input type="file" name="foto" id="foto" 
                        class="mt-1 block w-full text-sm text-gray-500
                              file:mr-4 file:py-2 file:px-4
                              file:rounded-full file:border-0
                              file:text-sm file:font-semibold
                              file:bg-indigo-50 file:text-indigo-700
                              hover:file:bg-indigo-100 cursor-pointer @error('foto') border-red-500 @enderror">
                <p class="mt-1 text-xs text-gray-500">Foto saat ini: 
                    @if($siswa->foto)
                        <span class="text-indigo-600 font-semibold">Tersedia</span>. Upload baru untuk mengganti.
                    @else
                        Tidak ada foto.
                    @endif
                </p>
                @error('foto')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            {{-- Status Keaktifan --}}
            <div class="md:col-span-2">
                <label for="aktif" class="block text-sm font-medium text-gray-700 mb-1">Status Keaktifan</label>
                <select name="aktif" id="aktif" class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option value="1" {{ old('aktif', $siswa->aktif) ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ !old('aktif', $siswa->aktif) ? 'selected' : '' }}>Tidak Aktif (Lulus/Pindah)</option>
                </select>
            </div>
        </div>

           


        <div class="mt-8 pt-4 border-t flex justify-end">
            <a href="{{ route('siswa.index') }}" class="px-6 py-2 text-sm font-semibold rounded-lg text-gray-600 border border-gray-300 hover:bg-gray-100 mr-3 transition duration-150 ease-in-out">Batal</a>
            <button type="submit" class="px-8 py-2 text-sm font-bold rounded-lg text-white bg-green-600 hover:bg-green-700 shadow-md hover:shadow-lg transform hover:scale-[1.01] transition duration-200 ease-in-out">
                Update Data Siswa
            </button>
        </div>
    </form>
</div>

{{-- JAVASCRIPT: Logic untuk memastikan hanya satu opsi Orang Tua yang aktif --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const newParentFields = document.querySelectorAll('.parent-new');
        const existingParentSelect = document.getElementById('id_orangtua');
        const newParentInputs = Array.from(newParentFields);
        
        // Fungsi untuk mengembalikan status field ke normal (tidak disabled, tidak abu-abu)
        function enableFields(inputs) {
            inputs.forEach(input => {
                input.disabled = false;
                input.classList.remove('bg-gray-100', 'cursor-not-allowed');
            });
        }

        // Fungsi untuk menonaktifkan field (disabled dan abu-abu)
        function disableFields(inputs) {
            inputs.forEach(input => {
                input.disabled = true;
                input.classList.add('bg-gray-100', 'cursor-not-allowed');
            });
        }

        // Function to check if any new parent field is filled
        function isNewParentFilled() {
            return newParentInputs.some(input => input.value.trim() !== '');
        }

        // --- Logic for New Parent Input (Nama/No WA) ---
        newParentInputs.forEach(input => {
            input.addEventListener('input', function() {
                if (isNewParentFilled()) {
                    // Jika ada field baru yang diisi, nonaktifkan dropdown
                    existingParentSelect.value = '';
                    disableFields([existingParentSelect]);
                } else {
                    // Jika field baru dikosongkan, aktifkan kembali dropdown
                    enableFields([existingParentSelect]);
                }
            });
        });

        // --- Logic for Existing Parent Select ---
        existingParentSelect.addEventListener('change', function() {
            if (this.value) {
                // Jika parent yang sudah ada dipilih, kosongkan dan nonaktifkan field baru
                newParentInputs.forEach(input => {
                    input.value = '';
                });
                disableFields(newParentInputs);
            } else if (!isNewParentFilled()) {
                // Jika pilihan dibatalkan (kembali ke "-- Pilih --") DAN field baru kosong, aktifkan kembali field baru
                enableFields(newParentInputs);
            }
        });

        // Initial check on page load (Penting untuk Old Values dan Data Siswa Lama)
        function initialCheck() {
            if (existingParentSelect.value && !isNewParentFilled()) {
                // Ada hubungan lama/baru dipilih via dropdown, dan field baru kosong
                disableFields(newParentInputs);
            } else if (isNewParentFilled()) {
                // Field baru terisi (dari old() value)
                existingParentSelect.value = ''; 
                disableFields([existingParentSelect]);
            }
            // Jika keduanya kosong, biarkan semua aktif (pengguna bisa memilih untuk tidak mengubah parent)
        }

        initialCheck();
    });
</script>
@endsection