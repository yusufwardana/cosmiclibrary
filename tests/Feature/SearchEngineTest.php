<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Book;
use App\Models\BookItem;
use App\Models\BorrowRecord;
use App\Models\Category;
use App\Models\Member;
use App\Models\User;
use App\Services\SearchEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchEngineTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_books_returns_paginator(): void
    {
        $engine = new SearchEngine();
        Book::factory()->count(2)->create(['title' => 'Alpha']);
        Book::factory()->create(['title' => 'Beta']);

        $result = $engine->searchBooks('Alpha');
        $this->assertSame(2, $result->total());
        $this->assertSame('Alpha', $result->items()[0]->title);
    }

    public function test_search_books_applies_status_filter(): void
    {
        $engine = new SearchEngine();
        $book1 = Book::factory()->create(['title' => 'Test', 'available_copies' => 5]);
        $book2 = Book::factory()->create(['title' => 'Test', 'available_copies' => 0]);

        $available = $engine->searchBooks('Test', ['status' => 'available']);
        $this->assertSame(1, $available->total());
        $this->assertSame($book1->id, $available->items()[0]->id);

        $unavailable = $engine->searchBooks('Test', ['status' => 'unavailable']);
        $this->assertSame(1, $unavailable->total());
        $this->assertSame($book2->id, $unavailable->items()[0]->id);
    }

    public function test_search_members_returns_paginator(): void
    {
        $engine = new SearchEngine();
        $user1 = User::factory()->create(['name' => 'John']);
        $user2 = User::factory()->create(['name' => 'Jane']);
        Member::factory()->create(['user_id' => $user1->id, 'member_number' => 'M001']);
        Member::factory()->create(['user_id' => $user2->id, 'member_number' => 'M002']);

        $result = $engine->searchMembers('John');
        $this->assertSame(1, $result->total());
        $this->assertSame('M001', $result->items()[0]->member_number);
    }

    public function test_search_members_applies_filters(): void
    {
        $engine = new SearchEngine();
        $user = User::factory()->create();
        Member::factory()->create(['user_id' => $user->id, 'class_name' => '10A', 'type' => 'student', 'status' => 'active']);
        Member::factory()->create(['user_id' => $user->id, 'class_name' => '10B', 'type' => 'student', 'status' => 'suspended']);

        $result = $engine->searchMembers('', ['class_name' => '10A', 'status' => 'active']);
        $this->assertSame(1, $result->total());
        $this->assertSame('10A', $result->items()[0]->class_name);
    }

    public function test_search_borrow_records_returns_paginator(): void
    {
        $engine = new SearchEngine();
        $user = User::factory()->create(['name' => 'Borrower']);
        $member = Member::factory()->create(['user_id' => $user->id]);
        $book = Book::factory()->create(['title' => 'Book One']);
        $item = BookItem::factory()->for($book)->create();

        BorrowRecord::factory()->for($member)->for($item, 'bookItem')->create(['status' => 'borrowed']);
        BorrowRecord::factory()->for(Member::factory()->create())->for($item, 'bookItem')->create(['status' => 'returned']);

        $result = $engine->searchBorrowRecords('Borrower', ['status' => 'borrowed']);
        $this->assertSame(1, $result->total());
        $this->assertSame('borrowed', $result->items()[0]->status);
    }
}