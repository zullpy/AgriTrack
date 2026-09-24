<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use RuntimeException;

class ImageCompressionService
{
    /**
     * Kompresi dan resize file gambar yang diunggah.
     *
     * @param  UploadedFile|string  $file  Objek UploadedFile atau path fisik gambar.
     * @param  int  $maxWidth  Lebar maksimal dalam piksel (default 1600px).
     * @param  int  $maxHeight  Tinggi maksimal dalam piksel (default 1600px).
     * @param  int  $quality  Kualitas kompresi 1-100 (default 80).
     * @return array{path: string, mime: string, extension: string, size: int, width: int, height: int}
     */
    public function compress(
        UploadedFile|string $file,
        int $maxWidth = 1600,
        int $maxHeight = 1600,
        int $quality = 80
    ): array {
        $sourcePath = $file instanceof UploadedFile ? $file->getRealPath() : $file;

        if (! file_exists($sourcePath)) {
            throw new RuntimeException("File gambar sumber tidak ditemukan: {$sourcePath}");
        }

        $imageInfo = @getimagesize($sourcePath);
        if ($imageInfo === false) {
            throw new RuntimeException('File bukan gambar yang valid.');
        }

        [$origWidth, $origHeight, $imageType] = $imageInfo;

        // Muat resource GD sesuai format
        $sourceImage = match ($imageType) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($sourcePath),
            IMAGETYPE_PNG => @imagecreatefrompng($sourcePath),
            IMAGETYPE_WEBP => @imagecreatefromwebp($sourcePath),
            default => @imagecreatefromstring((string) file_get_contents($sourcePath)),
        };

        if (! $sourceImage) {
            throw new RuntimeException('Gagal memproses gambar dengan GD.');
        }

        // Perbaiki orientasi dari EXIF jika format JPEG dan modul exif tersedia
        if ($imageType === IMAGETYPE_JPEG && function_exists('exif_read_data')) {
            $exif = @exif_read_data($sourcePath);
            if (! empty($exif['Orientation'])) {
                $sourceImage = match ($exif['Orientation']) {
                    3 => imagerotate($sourceImage, 180, 0),
                    6 => imagerotate($sourceImage, -90, 0),
                    8 => imagerotate($sourceImage, 90, 0),
                    default => $sourceImage,
                };
                // Update dimensi jika diputar 90 atau 270 derajat
                if (in_array($exif['Orientation'], [6, 8], true)) {
                    $origWidth = imagesx($sourceImage);
                    $origHeight = imagesy($sourceImage);
                }
            }
        }

        // Hitung dimensi target proporsional
        $ratio = min($maxWidth / max(1, $origWidth), $maxHeight / max(1, $origHeight), 1.0);
        $targetWidth = max(1, (int) round($origWidth * $ratio));
        $targetHeight = max(1, (int) round($origHeight * $ratio));

        // Buat canvas gambar baru
        $targetImage = imagecreatetruecolor($targetWidth, $targetHeight);

        // Pertahankan transparansi untuk PNG/WebP
        imagealphablending($targetImage, false);
        imagesavealpha($targetImage, true);
        $transparent = imagecolorallocatealpha($targetImage, 255, 255, 255, 127);
        imagefilledrectangle($targetImage, 0, 0, $targetWidth, $targetHeight, $transparent);

        // Resample kualitas tinggi
        imagecopyresampled(
            $targetImage,
            $sourceImage,
            0, 0, 0, 0,
            $targetWidth,
            $targetHeight,
            $origWidth,
            $origHeight
        );

        // Simpan ke temporary file dalam format WebP (atau JPEG jika webp tidak didukung)
        $supportsWebp = function_exists('imagewebp');
        $extension = $supportsWebp ? 'webp' : 'jpg';
        $mime = $supportsWebp ? 'image/webp' : 'image/jpeg';

        $tempPath = tempnam(sys_get_temp_dir(), 'hst_img_').'.'.$extension;

        if ($supportsWebp) {
            imagewebp($targetImage, $tempPath, $quality);
        } else {
            imagejpeg($targetImage, $tempPath, $quality);
        }

        // Bebaskan memori GD
        imagedestroy($sourceImage);
        imagedestroy($targetImage);

        return [
            'path' => $tempPath,
            'mime' => $mime,
            'extension' => $extension,
            'size' => (int) @filesize($tempPath),
            'width' => $targetWidth,
            'height' => $targetHeight,
        ];
    }
}
