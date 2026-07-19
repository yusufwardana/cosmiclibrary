<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Permission;
use App\Services\PermissionEngine;
use Tests\TestCase;

class PermissionEngineTest extends TestCase
{
    public function test_register_and_all(): void
    {
        $engine = app(PermissionEngine::class);
        $engine->register('book', ['create' => 'Membuat Buku', 'edit' => 'Mengedit Buku']);

        $all = $engine->all();

        $this->assertTrue($all->has('book.create'));
        $this->assertTrue($all->has('book.edit'));
        $this->assertSame('Membuat Buku', $all->get('book.create'));
    }

    public function test_register_does_not_duplicate(): void
    {
        Permission::factory()->create(['slug' => 'book.delete', 'name' => 'Menghapus Buku']);

        $engine = app(PermissionEngine::class);
        $engine->register('book', ['delete' => 'Menghapus Buku']);

        $this->assertCount(1, Permission::where('slug', 'book.delete')->get());
    }

    public function test_all_from_db(): void
    {
        Permission::factory()->create(['slug' => 'book.export', 'name' => 'Ekspor Buku']);

        $engine = app(PermissionEngine::class);
        $fromDb = $engine->allFromDb();

        $this->assertTrue($fromDb->contains(fn (Permission $p) => $p->slug === 'book.export'));
    }

    public function test_has_checks_in_memory_first(): void
    {
        $engine = app(PermissionEngine::class);
        $engine->register('book', ['view' => 'Melihat Buku']);

        $this->assertTrue($engine->has('book.view'));
    }

    public function test_has_falls_back_to_db(): void
    {
        Permission::factory()->create(['slug' => 'book.audit', 'name' => 'Audit Buku']);

        $engine = app(PermissionEngine::class);
        $this->assertTrue($engine->has('book.audit'));
    }

    public function test_has_returns_false_if_not_found(): void
    {
        $engine = app(PermissionEngine::class);
        $this->assertFalse($engine->has('nonexistent.perm'));
    }
}
