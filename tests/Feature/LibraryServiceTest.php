<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Book;
use App\Models\BookItem;
use App\Models\BorrowRecord;
use App\Models\Category;
use App\Models\Fine;
use App\Models\Member;
use App\Models\Reservation;
use App\Models\User;
use App\Services\LibraryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class LibraryServiceTest extends TestCase
{
    use RefreshDatabase;

    private LibraryService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(LibraryService::class);
    }

    public function test_create_book_persists_and_logs(): void
    {
        /** @var Category $category */
        $category = Category::factory()->create();
        $book = $this->service->createBook([
            'category_id' => $category->id,
            'title' => 'Judul Buku',
            'isbn' => '9781234567890',
            'author' => 'Penulis',
            'publisher' => 'Penerbit',
            'publish_year' => 2020,
            'language' => 'id',
            'total_copies' => 1,
            'available_copies' => 1,
        ]);

        $this->assertDatabaseHas('books', ['id' => $book->id, 'title' => 'Judul Buku']);
    }

    public function test_add_book_item_syncs_copy_count(): void
    {
        $book = Book::factory()->create(['total_copies' => 0, 'available_copies' => 0]);

        $this->service->addBookItem($book, [
            'barcode' => '1234567890123',
            'call_number' => '100.100',
            'status' => 'available',
            'condition' => 'good',
        ]);

        $this->assertSame(1, (int) $book->fresh()->total_copies);
        $this->assertSame(1, (int) $book->fresh()->available_copies);
    }

    public function test_borrow_book_updates_item_status(): void
    {
        $book = Book::factory()->create();
        $item = BookItem::factory()->create(['book_id' => $book->id, 'status' => 'available']);
        $member = Member::factory()->create();
        $librarian = User::factory()->create();

        $record = $this->service->borrowBook($member->id, $item->id, $librarian->id);

        $this->assertSame('borrowed', $record->status);
        $this->assertSame('borrowed', $item->fresh()->status);
    }

    public function test_borrow_unavailable_item_aborts(): void
    {
        $item = BookItem::factory()->create(['status' => 'borrowed']);
        $member = Member::factory()->create();
        $librarian = User::factory()->create();

        $this->expectException(\Throwable::class);
        $this->service->borrowBook($member->id, $item->id, $librarian->id);
    }

    public function test_return_overdue_book_creates_fine(): void
    {
        $member = Member::factory()->create();
        $librarian = User::factory()->create();
        $item = BookItem::factory()->create(['status' => 'borrowed']);
        $record = BorrowRecord::factory()->create([
            'book_item_id' => $item->id,
            'member_id' => $member->id,
            'librarian_out_id' => $librarian->id,
            'borrow_date' => Carbon::today()->subDays(10),
            'due_date' => Carbon::today()->subDays(3),
            'return_date' => null,
            'status' => 'borrowed',
        ]);

        $returned = $this->service->returnBook($record, $librarian->id);

        $this->assertSame('returned', $returned->status);
        $this->assertDatabaseHas('fines', ['borrow_record_id' => $record->id]);
    }

    public function test_extend_loan_increments_count(): void
    {
        $item = BookItem::factory()->create(['status' => 'borrowed']);
        $record = BorrowRecord::factory()->create([
            'book_item_id' => $item->id,
            'status' => 'borrowed',
            'extend_count' => 0,
        ]);

        $extended = $this->service->extendLoan($record);

        $this->assertSame(1, (int) $extended->extend_count);
    }

    public function test_extend_loan_max_limit_aborts(): void
    {
        $item = BookItem::factory()->create(['status' => 'borrowed']);
        $record = BorrowRecord::factory()->create([
            'book_item_id' => $item->id,
            'status' => 'borrowed',
            'extend_count' => 99,
        ]);

        $this->expectException(\Throwable::class);
        $this->service->extendLoan($record);
    }

    public function test_reserve_book_creates_pending(): void
    {
        $member = Member::factory()->create();
        $book = Book::factory()->create();

        $res = $this->service->reserveBook($member->id, $book->id);

        $this->assertSame('pending', $res->status);
        $this->assertDatabaseHas('reservations', ['id' => $res->id]);
    }

    public function test_cancel_reservation_sets_cancelled(): void
    {
        $res = Reservation::factory()->create(['status' => 'pending']);

        $this->service->cancelReservation($res);

        $this->assertSame('cancelled', $res->fresh()->status);
    }

    public function test_pay_fine_marks_paid(): void
    {
        $fine = Fine::factory()->create(['fine_amount' => 5000, 'paid_amount' => 0, 'status' => 'unpaid']);

        $this->service->payFine($fine, 5000);

        $this->assertSame('paid', $fine->fresh()->status);
    }

    public function test_waive_fine_sets_waived(): void
    {
        $fine = Fine::factory()->create(['status' => 'unpaid']);
        $user = User::factory()->create();

        $this->service->waiveFine($fine, $user->id, 'test waiver');

        $this->assertSame('waived', $fine->fresh()->status);
    }

    public function test_overdue_records_returns_only_overdue(): void
    {
        $item = BookItem::factory()->create(['status' => 'borrowed']);
        BorrowRecord::factory()->create([
            'book_item_id' => $item->id,
            'status' => 'borrowed',
            'due_date' => Carbon::today()->subDay(),
        ]);
        BorrowRecord::factory()->create([
            'book_item_id' => BookItem::factory()->create(['status' => 'borrowed'])->id,
            'status' => 'borrowed',
            'due_date' => Carbon::today()->addDay(),
        ]);

        $this->assertCount(1, $this->service->overdueRecords());
    }

    public function test_member_history_returns_ordered(): void
    {
        $member = Member::factory()->create();
        BorrowRecord::factory()->create([
            'member_id' => $member->id,
            'borrow_date' => Carbon::today()->subDays(2),
        ]);
        BorrowRecord::factory()->create([
            'member_id' => $member->id,
            'borrow_date' => Carbon::today()->subDays(1),
        ]);

        $history = $this->service->memberHistory($member->id);

        $this->assertCount(2, $history);
        assert($history->first() instanceof BorrowRecord);
        assert($history->last() instanceof BorrowRecord);
        $this->assertTrue($history->first()->borrow_date->gt($history->last()->borrow_date));
    }
}
