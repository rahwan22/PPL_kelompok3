<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Image Backend
    |--------------------------------------------------------------------------
    | Tentukan backend rendering gambar yang akan digunakan. 
    | Ubah menjadi 'gd' karena imagick menyebabkan error RuntimeException.
    */

    // 'image_backend' => 'gd',
    
    // Anda bisa menambahkan konfigurasi default lainnya di sini jika diperlukan,
    // tetapi untuk mengatasi error Imagick, baris di atas sudah cukup.
    'default' => 'gd',
        
        'drivers' => [
            'gd' => \BaconQrCode\Renderer\Image\GdImageBackEnd::class,
            // Anda bisa hapus 'imagick' dari sini jika Anda mau, tapi biasanya dibiarkan saja.
        ],
];