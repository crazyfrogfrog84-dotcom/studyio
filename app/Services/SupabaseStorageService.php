<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class SupabaseStorageService
{
    public function upload(UploadedFile $file, string $folder): string
    {
        $key = $this->key();
        $bucket = (string) config('services.supabase.storage_bucket');
        $path = trim($folder, '/').'/'.now()->format('Y/m').'/'.Str::uuid().'.'.$file->guessExtension();
        $encodedPath = implode('/', array_map('rawurlencode', explode('/', $path)));
        $response = $this->request($key)
            ->withBody((string) file_get_contents($file->getRealPath()), $file->getMimeType() ?: 'application/octet-stream')
            ->post($this->baseUrl()."/storage/v1/object/{$bucket}/{$encodedPath}");

        $response->throw();

        return $path;
    }

    public function signedUrl(?string $path): ?string
    {
        if (! $path || ! config('services.supabase.url') || ! config('services.supabase.service_role_key')) {
            return null;
        }

        $bucket = (string) config('services.supabase.storage_bucket');
        $encodedPath = implode('/', array_map('rawurlencode', explode('/', $path)));
        $response = $this->request($this->key())->post(
            $this->baseUrl()."/storage/v1/object/sign/{$bucket}/{$encodedPath}",
            ['expiresIn' => 86400],
        );
        $response->throw();
        $signedPath = $response->json('signedURL');

        return $signedPath ? rtrim($this->baseUrl(), '/').'/storage/v1/'.ltrim($signedPath, '/') : null;
    }

    public function delete(array $paths): void
    {
        $paths = array_values(array_filter($paths));
        if ($paths === []) {
            return;
        }

        $response = $this->request($this->key())->delete(
            $this->baseUrl().'/storage/v1/object/'.config('services.supabase.storage_bucket'),
            ['prefixes' => $paths],
        );

        $response->throw();
    }

    private function request(string $key): PendingRequest
    {
        return Http::acceptJson()->withHeaders([
            'apikey' => $key,
            'Authorization' => 'Bearer '.$key,
            'x-upsert' => 'true',
        ])->timeout(30);
    }

    private function baseUrl(): string
    {
        return rtrim((string) config('services.supabase.url'), '/');
    }

    private function key(): string
    {
        $key = (string) config('services.supabase.service_role_key');

        if ($key === '' || $this->baseUrl() === '') {
            throw new RuntimeException('Supabase Storage is not configured.');
        }

        return $key;
    }
}
