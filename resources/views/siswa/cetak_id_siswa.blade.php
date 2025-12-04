<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu ID Siswa - {{ $siswa->nama }}</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('assets/css/cetak_id.css')}}">
    <script src="{{ asset('assets/js/cetak_id.js') }}" defer></script>

</head>
<body>

    <div class="card-container">
        <div class="header-strip">KARTU PELAJAR</div>
        <!-- Kolom Kiri: Foto & QR (2/5 lebar) -->
        <div class="w-2/5 flex flex-col items-center pt-8 p-2 bg-indigo-50/50">
            <!-- Foto -->
            <div class="w-20 h-20 rounded-lg overflow-hidden border-2 border-white shadow-md mb-2">
                @if ($siswa->foto)
                    <img src="{{ asset('storage/' . $siswa->foto) }}" alt="Foto Siswa" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full bg-gray-300 flex items-center justify-center text-xs text-gray-600 font-semibold">
                        FOTO
                    </div>
                @endif
            </div>
            
            <!-- QR Code -->
            @if ($siswa->qr_code)
                <img src="{{ asset('storage/qr/' . $siswa->qr_code) }}" alt="QR Code" class="w-16 h-16 p-0.5 border bg-white rounded-sm">
            @endif
        </div>

        <!-- Kolom Kanan: Detail Utama (3/5 lebar) -->
        <div class="w-3/5 pt-8 p-3 flex flex-col justify-start">
            <p class="text-xs text-indigo-600 font-bold mb-1 uppercase">Nama Siswa</p>
            <h2 class="text-xs font-extrabold text-gray-900 leading-tight mb-3 uppercase">{{ $siswa->nama }}</h2>
            
            <p class="text-xs text-gray-500 mb-1">NIS: <span class="font-semibold">{{ $siswa->nis }}</span></p>

            <div class="mt-auto pt-2">
                <p class="text-ms font-semibold text-indigo-700">KELAS:</p>
                <p class="text-xl font-black text-indigo-800 leading-none">{{ $siswa->kelas->nama_kelas ?? 'N/A' }}</p>
            </div>
        </div>
    </div>
    
    <button onclick="window.print()" class="no-print absolute bottom-4 right-4 bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-full shadow-lg transition duration-300 flex items-center">
        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2v-2m-10 0h4m-4-2h4m6 4H7a2 2 0 01-2-2V5a2 2 0 012-2h10a2 2 0 012 2v10a2 2 0 01-2 2z"></path></svg>
        Cetak Kartu
    </button>
    
</body>
</html>