<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Backup;
use App\Services\BackupEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BackupEngineTest extends TestCase
{
    use RefreshDatabase;

    public function test_engine_has_name_and_version(): void
    {
        $engine = app(BackupEngine::class);
        $this->assertSame('backup', $engine->name());
        $this->assertSame('1.0.0', $engine->version());
    }

    public function test_prune_removes_old_backups(): void
    {
        $engine = app(BackupEngine::class);
        
        // Create 3 fake completed backups
        Backup::forceCreate([
            'filename' => 'b1.zip',
            'path' => storage_path('b1.zip'),
            'size' => 10,
            'type' => 'full',
            'status' => 'completed',
            'created_at' => now()->subDays(3)
        ]);
        Backup::forceCreate([
            'filename' => 'b2.zip',
            'path' => storage_path('b2.zip'),
            'size' => 10,
            'type' => 'full',
            'status' => 'completed',
            'created_at' => now()->subDays(2)
        ]);
        Backup::forceCreate([
            'filename' => 'b3.zip',
            'path' => storage_path('b3.zip'),
            'size' => 10,
            'type' => 'full',
            'status' => 'completed',
            'created_at' => now()->subDays(1)
        ]);

        $this->assertSame(3, Backup::count());
        
        // Keep only 1
        $deleted = $engine->prune(1);
        
        $this->assertSame(2, $deleted);
        $this->assertSame(1, Backup::count());
        $this->assertSame('b3.zip', Backup::first()->filename);
    }
}