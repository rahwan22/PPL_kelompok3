<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu ID Siswa - {{ $siswa->nama }}</title>
    <!-- Tailwind CSS (Digunakan untuk styling awal) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Gaya untuk tampilan Kartu ID */
        body {
            background-color: #f3f4f6;
            font-family: 'Inter', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .card-container {
            /* Dimensi Kartu ID standar (CR80) sekitar 3.375" x 2.125" */
            width: 320px; /* Lebar Kartu */
            height: 200px; /* Tinggi Kartu */
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border: 2px solid #4f46e5; /* Aksen Indigo */
            position: relative;
            display: flex;
            padding: 0;
        }
        .header-strip {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 20px;
            background-color: #4f46e5;
            color: white;
            font-size: 10px;
            font-weight: 700;
            text-align: center;
            line-height: 20px;
            clip-path: polygon(0 0, 100% 0, 95% 100%, 5% 100%); /* Bentuk strip melengkung sedikit */
        }

        /* PRINT STYLES */
        @media print {
            body { 
                margin: 0; 
                padding: 0;
                background-color: white !important;
                display: block; /* Matikan flex untuk cetak */
            }
            .no-print {
                display: none !important;
            }
            .card-container {
                /* Atur ulang margin dan shadow untuk hasil cetak yang bersih */
                margin: 10mm; /* Jarak dari tepi kertas */
                box-shadow: none; 
                border: 1px solid #ccc; /* Border tipis untuk menunjukkan batas kartu */
                /* Pastikan ukuran tetap sama untuk konsistensi */
                width: 320px; 
                height: 200px;
                page-break-after: always; /* Cetak kartu per halaman (jika ada loop) */
            }
        }
    </style>
</head>
<body>

    <div class="card-container">
        <!-- Strip Header Institusi (Logo atau Nama Sekolah) -->
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
            <h2 class="text-xl font-extrabold text-gray-900 leading-tight mb-3 uppercase">{{ $siswa->nama }}</h2>
            
            <p class="text-xs text-gray-500 mb-1">NIS: <span class="font-semibold">{{ $siswa->nis }}</span></p>

            <div class="mt-auto pt-2">
                <p class="text-sm font-semibold text-indigo-700">KELAS:</p>
                <p class="text-3xl font-black text-indigo-800 leading-none">{{ $siswa->kelas->nama_kelas ?? 'N/A' }}</p>
            </div>
        </div>
    </div>
    
    <button onclick="window.print()" class="no-print absolute bottom-4 right-4 bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-full shadow-lg transition duration-300 flex items-center">
        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2v-2m-10 0h4m-4-2h4m6 4H7a2 2 0 01-2-2V5a2 2 0 012-2h10a2 2 0 012 2v10a2 2 0 01-2 2z"></path></svg>
        Cetak Kartu
    </button>

    <script>
        // Pemicu cetak otomatis setelah halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            // Memberi jeda singkat agar semua konten (terutama gambar) dimuat sebelum mencetak
            setTimeout(() => {
                window.print();
            }, 800);
        });
    </script>
</body>
</html>