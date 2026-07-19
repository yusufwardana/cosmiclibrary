<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Book;
use App\Models\BookItem;
use App\Models\BorrowRecord;
use App\Models\Category;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class SearchApiTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_searches_books_via_api(): void
    {
        $category = Category::factory()->create();
        Book::factory()->count(3)->for($category)->create(['title' => 'Laravel Advanced']);
        Book::factory()->create(['title' => 'React Patterns']);

        $response = $this->getJson('/api/search/books?q=Laravel');

        $response->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonPath('data.0.title', 'Laravel Advanced');
    }

    #[Test]
    public function it_searches_members_via_api(): void
    {
        $user = User::factory()->create(['name' => 'Budi Santoso']);
        Member::factory()->create(['user_id' => $user->id, 'member_number' => 'M001']);

        $response = $this->getJson('/api/search/members?q=Budi');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.member_number', 'M001');
    }

    #[Test]
    public function it_searches_borrow_records_via_api(): void
    {
        $member = Member::factory()->create();
        $book = Book::factory()->create();
        $item = BookItem::factory()->for($book)->create();
        BorrowRecord::factory()->for($member)->for($item, 'bookItem')->create();

        $response = $this->getJson('/api/search/borrow-records?member_id='.$member->id);

        $response->assertOk()->assertJsonCount(1, 'data');
    }
}