<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Media;
use App\Services\MediaEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaEngineTest extends TestCase
{
    use RefreshDatabase;

    public function test_engine_has_name_and_version(): void
    {
        $engine = app(MediaEngine::class);
        $this->assertSame('media', $engine->name());
        $this->assertSame('1.0.0', $engine->version());
    }

    public function test_upload_persists_file(): void
    {
        Storage::fake('public');

        $engine = app(MediaEngine::class);
        $file = UploadedFile::fake()->image('avatar.jpg');

        $media = $engine->upload($file, 'avatars');

        $this->assertInstanceOf(Media::class, $media);
        $this->assertSame('avatars', $media->collection);
        $this->assertSame('avatar.jpg', $media->original_name);
        
        Storage::disk('public')->assertExists($media->path);
    }

    public function test_validate_checks_size_and_mime(): void
    {
        $engine = app(MediaEngine::class);
        
        // fake size is minimal
        $validFile = UploadedFile::fake()->image('photo.jpg');
        $this->assertTrue($engine->validate($validFile, ['jpg']));
        
        // invalid extension
        $invalidFile = UploadedFile::fake()->create('document.pdf');
        $this->assertFalse($engine->validate($invalidFile, ['jpg', 'png']));
    }

    public function test_delete_removes_file_and_record(): void
    {
        Storage::fake('public');

        $engine = app(MediaEngine::class);
        $file = UploadedFile::fake()->image('photo.jpg');
        $media = $engine->upload($file);

        $path = $media->path;
        Storage::disk('public')->assertExists($path);
        
        $engine->delete($media);

        Storage::disk('public')->assertMissing($path);
        $this->assertSoftDeleted('media', ['id' => $media->id]);
    }

    public function test_garbage_collect_removes_orphans(): void
    {
        Storage::fake('public');

        $engine = app(MediaEngine::class);
        
        // orphan
        $engine->upload(UploadedFile::fake()->image('orphan.jpg'));
        
        // not orphan
        $engine->upload(
            UploadedFile::fake()->image('used.jpg'), 
            'default',
            'App\Models\User',
            1
        );

        $this->assertSame(2, Media::count());
        
        $deletedCount = $engine->garbageCollect();
        
        $this->assertSame(1, $deletedCount);
        $this->assertSame(1, Media::count());
    }
}