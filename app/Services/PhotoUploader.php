<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Stores an uploaded/captured photo on the "public" disk and returns the
 * stored path (not the full URL — callers pass that through
 * Storage::url() when displaying it, same as $customer->photo_url).
 * Requires `php artisan storage:link` to have been run.
 */
class PhotoUploader
{
    public function store(UploadedFile $file, string $folder): string
    {
        return $file->store($folder, 'public');
    }

    public function replace(?string $existingPath, ?UploadedFile $file, string $folder): ?string
    {
        if (! $file) {
            return $existingPath;
        }

        if ($existingPath) {
            Storage::disk('public')->delete($existingPath);
        }

        return $this->store($file, $folder);
    }
}
