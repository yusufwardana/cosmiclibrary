<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaEngine extends BaseService
{
    public function name(): string
    {
        return 'media';
    }

    public function version(): string
    {
        return '1.0.0';
    }

    /**
     * Upload a file to the media library.
     *
     * @param  UploadedFile  $file
     * @param  string|null   $collection  e.g. 'covers', 'avatars', 'documents'
     * @param  string|null   $modelType  polymorphic model class
     * @param  int|null      $modelId
     * @return Media
     */
    public function upload(
        UploadedFile $file,
        ?string $collection = 'default',
        ?string $modelType = null,
        ?int $modelId = null,
    ): Media {
        $disk = config('media.disk', 'public');
        $folder = trim(config('media.folder', 'uploads'), '/');
        $hashName = Str::random(40).'.'.$file->getClientOriginalExtension();
        $path = $file->storeAs($folder, $hashName, ['disk' => $disk]);

        return Media::create([
            'model_type'    => $modelType,
            'model_id'      => $modelId,
            'collection'    => $collection,
            'filename'      => $hashName,
            'original_name' => $file->getClientOriginalName(),
            'mime_type'     => $file->getMimeType(),
            'disk'          => $disk,
            'path'          => $path,
            'size'          => $file->getSize(),
            'metadata'      => null,
            'uploaded_by'   => auth()->id(),
        ]);
    }

    /**
     * Validate an uploaded file against allowed types and max size.
     */
    public function validate(UploadedFile $file, array $allowedMimes = ['jpg','jpeg','png','gif','pdf','doc','docx'], int $maxMb = 5): bool
    {
        $maxBytes = $maxMb * 1024 * 1024;

        if ($file->getSize() > $maxBytes) {
            return false;
        }

        $ext = strtolower($file->getClientOriginalExtension());

        return in_array($ext, $allowedMimes);
    }

    /**
     * Delete a media record and its file from disk.
     */
    public function delete(Media $media): bool
    {
        Storage::disk($media->disk)->delete($media->path);

        return (bool) $media->delete();
    }

    /**
     * Garbage collect orphaned media files (no model reference).
     */
    public function garbageCollect(): int
    {
        $orphans = Media::whereNull('model_type')->whereNull('model_id')->get();
        $count = 0;

        foreach ($orphans as $media) {
            $this->delete($media);
            $count++;
        }

        return $count;
    }
}