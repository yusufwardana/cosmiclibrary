<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Book;
use App\Models\BookItem;
use App\Models\BorrowRecord;
use App\Models\Category;
use App\Models\Fine;
use App\Models\Member;
use App\Models\User;
use App\Services\AnalyticsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_summary_returns_correct_counts(): void
    {
        $category = Category::factory()->create();
        Book::factory()->count(3)->create(['category_id' => $category->id]);
        Member::factory()->count(2)->create();

        $service = new AnalyticsService;
        $summary = $service->summary();

        $this->assertSame(3, $summary['books']);
        $this->assertSame(2, $summary['members']);
        $this->assertSame(0, $summary['active_borrows']);
        $this->assertSame(0, $summary['overdue']);
        $this->assertSame(0, $summary['fines_total']);
    }

    public function test_borrows_by_month_returns_twelve_entries(): void
    {
        $service = new AnalyticsService;
        $result = $service->borrowsByMonth(12);

        $this->assertCount(12, $result);
    }

    public function test_popular_books_returns_array(): void
    {
        $service = new AnalyticsService;
        $result = $service->popularBooks();

        $this->assertIsArray($result);
    }

    public function test_borrows_by_category_returns_array(): void
    {
        $service = new AnalyticsService;
        $result = $service->borrowsByCategory();

        $this->assertIsArray($result);
    }

    public function test_fines_by_month_returns_twelve_entries(): void
    {
        $service = new AnalyticsService;
        $result = $service->finesByMonth(12);

        $this->assertCount(12, $result);
    }

    public function test_active_members_returns_array(): void
    {
        $service = new AnalyticsService;
        $result = $service->activeMembers();

        $this->assertIsArray($result);
    }

    public function test_analytics_page_loads(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('analytics.index'));

        $response->assertStatus(200);
        $response->assertViewIs('analytics.index');
        $response->assertViewHas('summary');
        $response->assertViewHas('borrowsByMonth');
        $response->assertViewHas('popularBooks');
        $response->assertViewHas('borrowsByCategory');
        $response->assertViewHas('finesByMonth');
        $response->assertViewHas('activeMembers');
    }

    public function test_summary_counts_borrows_with_data(): void
    {
        $category = Category::factory()->create();
        $book = Book::factory()->create(['category_id' => $category->id]);
        $item = BookItem::factory()->create(['book_id' => $book->id]);
        $member = Member::factory()->create();
        $user = User::factory()->create();

        BorrowRecord::factory()->create([
            'member_id' => $member->id,
            'book_item_id' => $item->id,
            'librarian_out_id' => $user->id,
            'status' => 'borrowed',
        ]);

        $service = new AnalyticsService;
        $summary = $service->summary();

        $this->assertSame(1, $summary['active_borrows']);
    }
}