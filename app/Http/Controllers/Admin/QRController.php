<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\Siswa;

class QRController extends Controller
{
    public function generate($nis)
    {
        $siswa = Siswa::findOrFail($nis);

        // Format data dalam QR (Anda bebas ubah)
        $dataQR = "SISWA|" . $siswa->nis . "|" . $siswa->nama;

        // Simpan teks QR ke database
        $siswa->qr_code = $dataQR;
        $siswa->save();

        // Generate QR image
        $qrImage = QrCode::format('png')->size(300)->generate($dataQR);

        // Nama file
        $fileName = $siswa->nis . '_qrcode.png';
        $filePath = public_path('qrcodes/' . $fileName);

        // Simpan ke public/qrcodes
        if (!file_exists(public_path('qrcodes'))) {
            mkdir(public_path('qrcodes'), 0777, true);
        }

        file_put_contents($filePath, $qrImage);

        return back()->with('success', "QR Code berhasil dibuat untuk siswa: $siswa->nama");
    }
}
