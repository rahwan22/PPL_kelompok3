@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4 md:p-8">
    <div class="bg-white shadow-xl rounded-xl p-6 md:p-10 max-w-lg mx-auto">
        <h2 class="text-3xl font-bold text-gray-800 mb-6 text-center">
            <i class="fas fa-qrcode mr-2"></i> Scan QR Absensi Siswa
        </h2>
        
        <p class="text-gray-600 mb-6 text-center">Arahkan kamera ke QR Code NIS siswa.</p>

        {{-- Area Pemindai QR --}}
        <div id="reader" class="w-full h-auto max-w-xs mx-auto overflow-hidden rounded-lg border-4 border-indigo-500 shadow-lg">
            {{-- Pemindai QR akan dimuat di sini --}}
        </div>
        
        {{-- Area Hasil Scan --}}
        <div id="result" class="mt-8 p-4 bg-gray-100 rounded-lg text-center min-h-[5rem] flex items-center justify-center font-semibold text-lg text-gray-700 transition duration-300">
            Silahkan Scan
        </div>

        {{-- Loading Indicator --}}
        <div id="loading-indicator" class="hidden mt-4 text-center text-indigo-600 font-medium">
            <i class="fas fa-spinner fa-spin mr-2"></i> Memproses data absensi...
        </div>
        
        {{-- Tombol Bantuan (Opsional) --}}
        <div class="text-center mt-6">
            <a href="{{ route('absensi.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 transition duration-150">
                <i class="fas fa-list-alt mr-1"></i> Lihat Daftar Absensi
            </a>
        </div>
    </div>
</div>

{{-- Skrip untuk html5-qrcode dan Logika Absensi --}}
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    // Pastikan variabel token CSRF tersedia
    const csrfToken = "{{ csrf_token() }}"; 

    // Inisialisasi Scanner
    const html5QrcodeScanner = new Html5QrcodeScanner(
        "reader", 
        { 
            fps: 10, 
            qrbox: {width: 250, height: 250},
            // Preferensi kamera belakang (jika ada di HP)
            supportedScanTypes: [Html5QrcodeSupportedFormats.QR_CODE],
            rememberLastUsedCamera: true
        }
    );

    // Dapatkan elemen yang akan diupdate
    const resultElement = document.getElementById('result');
    const loadingIndicator = document.getElementById('loading-indicator');

    // Fungsi yang dipanggil saat QR berhasil discan
    function onScanSuccess(decodedText) {
        // 1. Hentikan (Pause) scanner sementara
        html5QrcodeScanner.pause();
        
        // 2. Tampilkan status loading
        resultElement.classList.remove('bg-red-200', 'text-red-800', 'bg-yellow-200', 'text-yellow-800');
        resultElement.classList.add('bg-indigo-100', 'text-indigo-700');
        resultElement.innerHTML = `<i class="fas fa-sync-alt fa-spin mr-2"></i> Memproses NIS: <b>${decodedText}</b>`;
        loadingIndicator.classList.remove('hidden');

        // 3. Kirim data ke Controller Laravel
        fetch("{{ route('absensi.store') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken // Gunakan token untuk otentikasi POST
            },
            body: JSON.stringify({ nis: decodedText })
        })
        .then(res => res.json())
        .then(data => {
            let className, icon;

            if (data.success) {
                // Kasus Sukses (Hadir/Terlambat)
                className = data.status === 'Terlambat' ? 'bg-yellow-200 text-yellow-800' : 'bg-green-200 text-green-800';
                icon = '<i class="fas fa-check-circle mr-2"></i>';
            } else {
                // Kasus Gagal (Sudah Absen/NIS tidak terdaftar/Error Server)
                className = data.message.includes('sudah absen') ? 'bg-yellow-200 text-yellow-800' : 'bg-red-200 text-red-800';
                icon = '<i class="fas fa-times-circle mr-2"></i>';
            }

            // Update hasil scan dengan status
            resultElement.classList.remove('bg-indigo-100', 'text-indigo-700');
            resultElement.classList.add(className);
            resultElement.innerHTML = `${icon} ${data.message}`;

        })
        .catch(error => {
            // Tangani error koneksi atau server
            resultElement.classList.remove('bg-indigo-100', 'text-indigo-700');
            resultElement.classList.add('bg-red-200', 'text-red-800');
            resultElement.innerHTML = '❌ Kesalahan Jaringan: Gagal terhubung ke server.';
        })
        .finally(() => {
            loadingIndicator.classList.add('hidden');
            
            // 4. Lanjutkan scanner setelah jeda agar guru bisa melihat hasilnya
            setTimeout(() => {
                resultElement.classList.remove('bg-green-200', 'text-green-800', 'bg-yellow-200', 'text-yellow-800', 'bg-red-200', 'text-red-800');
                resultElement.classList.add('bg-gray-100', 'text-gray-700');
                resultElement.innerHTML = 'Siap untuk scan berikutnya...';
                html5QrcodeScanner.resume(); // Lanjutkan scanner
            }, 3000); // Jeda 3 detik
        });
    }

    // Render scanner saat halaman dimuat
    html5QrcodeScanner.render(onScanSuccess);
</script>
@endsection
