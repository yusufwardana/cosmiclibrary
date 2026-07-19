<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    public function test_guest_cannot_view_categories(): void
    {
        $this->get(route('categories.index'))
            ->assertRedirect(route('auth.login'));
    }

    public function test_authenticated_user_can_view_categories(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('categories.index'))
            ->assertOk();
    }

    public function test_can_create_category(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('categories.store'), [
                'name' => 'Fiksi',
                'description' => 'Buku fiksi',
            ])
            ->assertRedirect(route('categories.index'));

        $this->assertDatabaseHas('categories', ['name' => 'Fiksi']);
    }

    public function test_can_update_category(): void
    {
        $user = User::factory()->create();
        /** @var Category $category */
        $category = Category::factory()->create(['name' => 'Lama']);

        $this->actingAs($user)
            ->put(route('categories.update', $category), [
                'name' => 'Baru',
                'description' => $category->description,
            ])
            ->assertRedirect(route('categories.index'));

        $category->refresh();
        $this->assertSame('Baru', $category->name);
    }

    public function test_cannot_delete_category_with_books(): void
    {
        $user = User::factory()->create();
        /** @var Category $category */
        $category = Category::factory()->create();
        Book::factory()->create(['category_id' => $category->id]);

        $this->actingAs($user)
            ->delete(route('categories.destroy', $category))
            ->assertRedirect(route('categories.index'));

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_can_delete_category_without_books(): void
    {
        $user = User::factory()->create();
        /** @var Category $category */
        $category = Category::factory()->create();

        $this->actingAs($user)
            ->delete(route('categories.destroy', $category))
            ->assertRedirect(route('categories.index'));

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }
}
