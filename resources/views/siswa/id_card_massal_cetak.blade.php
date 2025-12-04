<!DOCTYPE html>
<html>
<head>
    <title>ID Card Siswa Massal</title>

    <link rel="stylesheet" href="{{ asset('asset/css/siswa_idcadr.css') }}">
</head>
<body>
    @foreach ($chunks as $siswasOnPage)
        <div class="page-break">
            @foreach ($siswasOnPage as $siswa)
                <div class="id-card-wrapper">
                    <div class="id-card-content">
                        <h5>KARTU PELAJAR</h5>
                        <hr style="margin: 2px 0;">
                        <p>Nama: {{ $siswa->nama }}</p>
                        <p>NIS: {{ $siswa->nis }}</p>
                        <p>Kelas: {{ $siswa->kelas->nama_kelas ?? 'N/A' }}</p>
                        {{-- Tampilkan QR Code (Anda harus sesuaikan dengan cara Anda menyimpan QR code) --}}
                        {{-- Jika QR code adalah base64: --}}
                        @if ($siswa->qr_code)
                            <img src="data:image/png;base64,{{ $siswa->qr_code }}" class="qr-code-img" alt="QR Code">
                        @else
                            <p>QR Code Belum Tersedia</p>
                        @endif
                        {{-- Tambahkan detail lain sesuai desain ID Card Anda --}}
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach
</body>
</html>