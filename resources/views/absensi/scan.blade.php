@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4 md:p-8">
    <div class="bg-white shadow-xl rounded-xl p-6 md:p-10 max-w-lg mx-auto">
        <h2 class="text-3xl font-bold text-gray-800 mb-6 text-center">
            <i class="fas fa-qrcode mr-2"></i> Scan QR Absensi Siswa
        </h2>

        <p class="text-gray-600 mb-6 text-center">Arahkan kamera ke QR Code NIS siswa.</p>

        {{-- Scanner --}}
        <div id="reader" class="w-full max-w-xs mx-auto overflow-hidden rounded-lg border-4 border-indigo-500 shadow-lg"></div>

        {{-- Status Scan --}}
        <div id="result" 
             class="mt-8 p-4 bg-gray-100 rounded-lg text-center min-h-[5rem]
                    flex items-center justify-center font-semibold text-lg text-gray-700">
            Menunggu Scan...
        </div>

        {{-- Loading --}}
        <div id="loading-indicator" class="hidden mt-4 text-center text-indigo-600 font-medium">
            <i class="fas fa-spinner fa-spin mr-2"></i> Memproses data absensi...
        </div>

        {{-- Link --}}
        <div class="text-center mt-6">
            <a href="{{ route('absensi.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                <i class="fas fa-list-alt mr-1"></i> Lihat Daftar Absensi
            </a>
        </div>
    </div>
</div>

{{-- Suara Beep --}}
<audio id="beep-sound">
    <source src="https://actions.google.com/sounds/v1/cartoon/clang_and_wobble.ogg">
</audio>

{{-- Library --}}
<script src="https://unpkg.com/html5-qrcode"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const csrfToken = "{{ csrf_token() }}";
    const beep = document.getElementById("beep-sound");
    const resultBox = document.getElementById("result");
    const loading = document.getElementById("loading-indicator");

    function updateBox(className, icon, message) {
        resultBox.className = `mt-8 p-4 rounded-lg text-center min-h-[5rem] 
                               flex items-center justify-center font-semibold text-lg ${className}`;
        resultBox.innerHTML = `${icon} ${message}`;
    }

    function onScanSuccess(decodedText) {
        beep.play();

        updateBox("bg-indigo-100 text-indigo-700", 
                  '<i class="fas fa-sync-alt fa-spin mr-2"></i>', 
                  `Memproses NIS: <b>${decodedText}</b>`);

        loading.classList.remove("hidden");

        // Kirim ke Controller (SAVE DATA)
        fetch("{{ route('absensi.store') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken
            },
            body: JSON.stringify({
                nis: decodedText,
                lokasi: "Scan QR"
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                updateBox(
                    data.status === "Terlambat" ? "bg-yellow-200 text-yellow-800" : "bg-green-200 text-green-800",
                    '<i class="fas fa-check-circle mr-2"></i>',
                    data.message
                );
            } else {
                updateBox(
                    data.message.includes("sudah absen") ? "bg-yellow-200 text-yellow-800" : "bg-red-200 text-red-800",
                    '<i class="fas fa-times-circle mr-2"></i>',
                    data.message
                );
            }
        })
        .catch(() => {
            updateBox("bg-red-200 text-red-800", "❌", "Kesalahan jaringan! Gagal terhubung.");
        })
        .finally(() => {
            loading.classList.add("hidden");

            // Reset dan mulai scan lagi
            setTimeout(() => {
                updateBox("bg-gray-100 text-gray-700", "", "Siap untuk scan berikutnya...");
            }, 3000);
        });
    }

    // Inisialisasi Scanner
    let scanner = new Html5QrcodeScanner("reader", { fps: 10, qrbox: 250 });
    scanner.render(onScanSuccess);
});
</script>

@endsection

