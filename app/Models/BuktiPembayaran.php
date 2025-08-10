<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuktiPembayaran extends Model
{
    protected $table = 'buktiPembayaran';
    protected $primaryKey = 'buktiPembayaranId';

    protected $fillable = [
        'buktiPembayaranPembayaranId',
        'buktiPembayaranFoto',
    ];

    public function pembayaran()
    {
        return $this->belongsTo(Pembayaran::class, 'buktiPembayaranPembayaranId', 'pembayaranId');
    }

    public function tagihan()
    {
        return $this->hasOneThrough(Tagihan::class, Pembayaran::class, 'pembayaranId', 'tagihanId', 'buktiPembayaranPembayaranId', 'pembayaranTagihanId');
    }


    public static function compressImageToBase64($file, $quality = 50, $maxWidth = 500, $maxHeight = 500)
    {
        try {
            // Baca file sebagai string
            $imageData = file_get_contents($file->getPathname());

            // Buat resource gambar dari file
            $image = imagecreatefromstring($imageData);
            if (!$image) {
                return null;
            }

            // Dapatkan ukuran asli gambar
            $width = imagesx($image);
            $height = imagesy($image);

            // Resize jika lebih besar dari batas yang ditentukan
            if ($width > $maxWidth || $height > $maxHeight) {
                $newWidth = $maxWidth;
                $newHeight = ($height / $width) * $newWidth;
                if ($newHeight > $maxHeight) {
                    $newHeight = $maxHeight;
                    $newWidth = ($width / $height) * $newHeight;
                }

                // Buat canvas baru dengan ukuran lebih kecil
                $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
                imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

                // Hancurkan gambar lama dan gunakan gambar baru
                imagedestroy($image);
                $image = $resizedImage;
            }

            // Hilangkan metadata (EXIF)
            if (function_exists('exif_read_data')) {
                @exif_read_data($file->getPathname());
            }

            // Start output buffering untuk menyimpan hasil kompresi
            ob_start();
            imagejpeg($image, null, $quality); // Kompres gambar ke format JPEG
            $compressedImageData = ob_get_contents();
            ob_end_clean();

            // Hancurkan resource gambar untuk menghindari memory leak
            imagedestroy($image);

            // Encode hasil kompresi ke base64
            return base64_encode($compressedImageData);
        } catch (\Exception $e) {
            return null;
        }
    }




}
