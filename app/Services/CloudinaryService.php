<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class CloudinaryService
{
    protected ?string $cloudName;

    protected ?string $apiKey;

    protected ?string $apiSecret;

    protected ?string $uploadPreset;

    public function __construct()
    {
        $cloudinaryUrl = config('services.cloudinary.url');

        if ($cloudinaryUrl && str_starts_with($cloudinaryUrl, 'cloudinary://')) {
            $parsed = parse_url($cloudinaryUrl);
            $this->apiKey = $parsed['user'] ?? null;
            $this->apiSecret = $parsed['pass'] ?? null;
            $this->cloudName = $parsed['host'] ?? null;
        } else {
            $this->cloudName = config('services.cloudinary.cloud_name');
            $this->apiKey = config('services.cloudinary.api_key');
            $this->apiSecret = config('services.cloudinary.api_secret');
        }

        $this->uploadPreset = config('services.cloudinary.upload_preset');
    }

    /**
     * Cek apakah konfigurasi Cloudinary sudah lengkap diisi.
     */
    public function isConfigured(): bool
    {
        if (empty($this->cloudName)) {
            return false;
        }

        // Membutuhkan api_key + api_secret untuk signed upload ATAU upload_preset untuk unsigned
        return (! empty($this->apiKey) && ! empty($this->apiSecret)) || ! empty($this->uploadPreset);
    }

    /**
     * Unggah file gambar ke Cloudinary (atau simpan ke disk public lokal jika belum dikonfigurasi).
     *
     * @param  string|UploadedFile  $file  Path file atau objek UploadedFile.
     * @param  string  $folder  Folder penyimpanan di Cloudinary.
     * @return array{url: string, public_id: string, storage_type: string}
     */
    public function upload(string|UploadedFile $file, string $folder = 'pertanian/kegiatan_hst'): array
    {
        $realPath = $file instanceof UploadedFile ? $file->getRealPath() : $file;
        $originalFilename = $file instanceof UploadedFile ? $file->getClientOriginalName() : basename($realPath);
        $ext = pathinfo($originalFilename, PATHINFO_EXTENSION) ?: 'webp';

        // Jika kredensial Cloudinary belum diisi, simpan ke storage disk public lokal
        if (! $this->isConfigured()) {
            return $this->storeToLocalStorage($realPath, $ext);
        }

        try {
            $apiUrl = "https://api.cloudinary.com/v1_1/{$this->cloudName}/image/upload";
            $timestamp = time();

            $postData = [
                'timestamp' => (string) $timestamp,
                'folder' => $folder,
            ];

            if (! empty($this->apiKey) && ! empty($this->apiSecret)) {
                // Parameter untuk signature harus diurutkan secara alfabetis (a-z)
                $signatureString = "folder={$folder}&timestamp={$timestamp}".$this->apiSecret;
                $signature = sha1($signatureString);

                $postData['api_key'] = $this->apiKey;
                $postData['signature'] = $signature;
            } elseif (! empty($this->uploadPreset)) {
                $postData['upload_preset'] = $this->uploadPreset;
            }

            $response = Http::timeout(60)
                ->attach('file', (string) file_get_contents($realPath), basename($realPath))
                ->post($apiUrl, $postData);

            if ($response->successful()) {
                $json = $response->json();
                $secureUrl = $json['secure_url'] ?? $json['url'] ?? null;
                $publicId = $json['public_id'] ?? null;

                if ($secureUrl && $publicId) {
                    return [
                        'url' => $secureUrl,
                        'public_id' => $publicId,
                        'storage_type' => 'cloudinary',
                    ];
                }
            }

            Log::warning('Gagal mengunggah gambar ke Cloudinary API, beralih ke penyimpanan lokal.', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        } catch (Throwable $e) {
            Log::error('Exception saat upload ke Cloudinary: '.$e->getMessage());
        }

        // Fallback ke penyimpanan lokal jika Cloudinary gagal
        return $this->storeToLocalStorage($realPath, $ext);
    }

    /**
     * Hapus file fisik gambar (baik dari Cloudinary maupun dari storage disk lokal).
     *
     * @param  string  $urlOrPublicId  URL lengkap atau public_id file foto.
     */
    public function deleteImage(string $urlOrPublicId): bool
    {
        $target = trim($urlOrPublicId);
        if (empty($target)) {
            return false;
        }

        // Cek jika gambar berada di Cloudinary
        if (str_contains($target, 'cloudinary.com') || (! str_starts_with($target, '/storage/') && ! str_starts_with($target, 'kegiatan_fotos/'))) {
            return $this->deleteFromCloudinary($target);
        }

        // Jika file di storage lokal
        return $this->deleteFromLocalStorage($target);
    }

    /**
     * Hapus file gambar dari server Cloudinary secara permanen.
     */
    protected function deleteFromCloudinary(string $urlOrPublicId): bool
    {
        if (! $this->isConfigured() || empty($this->apiKey) || empty($this->apiSecret)) {
            Log::info("Cloudinary API credentials not present to delete: {$urlOrPublicId}");

            return false;
        }

        $publicId = $this->extractPublicId($urlOrPublicId);
        if (empty($publicId)) {
            return false;
        }

        try {
            $apiUrl = "https://api.cloudinary.com/v1_1/{$this->cloudName}/image/destroy";
            $timestamp = time();

            // Signature untuk destroy: "public_id={public_id}&timestamp={timestamp}{api_secret}"
            $signatureString = "public_id={$publicId}&timestamp={$timestamp}".$this->apiSecret;
            $signature = sha1($signatureString);

            $response = Http::timeout(30)->post($apiUrl, [
                'public_id' => $publicId,
                'api_key' => $this->apiKey,
                'timestamp' => (string) $timestamp,
                'signature' => $signature,
            ]);

            if ($response->successful()) {
                $result = $response->json('result');

                return $result === 'ok' || $result === 'not found';
            }

            Log::warning("Cloudinary destroy API error for {$publicId}: ".$response->body());
        } catch (Throwable $e) {
            Log::error("Cloudinary destroy exception for {$publicId}: ".$e->getMessage());
        }

        return false;
    }

    /**
     * Ekstrak public_id dari Cloudinary URL.
     * Contoh: https://res.cloudinary.com/demo/image/upload/v1570979139/pertanian/kegiatan_hst/sample.webp
     * Hasil: pertanian/kegiatan_hst/sample
     */
    public function extractPublicId(string $url): string
    {
        if (! str_contains($url, 'cloudinary.com')) {
            return $url;
        }

        $parsed = parse_url($url, PHP_URL_PATH);
        if (! $parsed) {
            return $url;
        }

        // Ambil setelah '/image/upload/'
        $pos = strpos($parsed, '/image/upload/');
        if ($pos !== false) {
            $afterUpload = substr($parsed, $pos + strlen('/image/upload/'));
            // Hapus awalan versi 'v123456789/' jika ada
            $afterUpload = preg_replace('#^v\d+/#', '', $afterUpload);

            // Hapus ekstensi file (.webp, .jpg, .png, dll)
            return preg_replace('/\.[^.]+$/', '', $afterUpload);
        }

        return basename($parsed);
    }

    /**
     * Simpan file gambar ke storage disk public lokal (fallback).
     *
     * @return array{url: string, public_id: string, storage_type: string}
     */
    protected function storeToLocalStorage(string $sourcePath, string $ext): array
    {
        $filename = uniqid('kegiatan_').'_'.time().'.'.$ext;
        $subDirectory = 'kegiatan_fotos';
        $destinationRelativePath = "{$subDirectory}/{$filename}";

        $fileContents = (string) file_get_contents($sourcePath);
        Storage::disk('public')->put($destinationRelativePath, $fileContents);

        return [
            'url' => '/storage/'.$destinationRelativePath,
            'public_id' => $destinationRelativePath,
            'storage_type' => 'local',
        ];
    }

    /**
     * Hapus file gambar dari storage disk public lokal.
     */
    protected function deleteFromLocalStorage(string $urlOrPath): bool
    {
        $normalized = preg_replace('#^https?://[^/]+#', '', $urlOrPath);
        $normalized = preg_replace('#^/storage/#', '', $normalized);
        $normalized = ltrim($normalized, '/');

        if (Storage::disk('public')->exists($normalized)) {
            return Storage::disk('public')->delete($normalized);
        }

        return false;
    }
}
