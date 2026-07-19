<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\BookItem;
use App\Models\BorrowRecord;
use App\Models\Fine;
use App\Models\Member;
use App\Models\User;
use App\Notifications\FineCreatedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class BorrowRecordObserverTest extends TestCase
{
    use RefreshDatabase;

    public function test_overdue_status_creates_fine(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $member = Member::factory()->create(['user_id' => $user->id]);
        $item = BookItem::factory()->create(['status' => 'borrowed']);

        /** @var BorrowRecord $record */
        $record = BorrowRecord::factory()->create([
            'member_id' => $member->id,
            'book_item_id' => $item->id,
            'due_date' => now()->subDays(5),
            'status' => 'borrowed',
        ]);

        $record->update(['status' => 'overdue']);

        $this->assertDatabaseHas('fines', [
            'borrow_record_id' => $record->id,
            'fine_type' => 'overdue',
            'status' => 'unpaid',
        ]);

        $fine = Fine::where('borrow_record_id', $record->id)->first();
        $this->assertNotNull($fine);
        $this->assertSame(5000, (int) $fine->fine_amount); // 5 days * 1000 default
    }

    public function test_overdue_fine_not_duplicated(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $member = Member::factory()->create(['user_id' => $user->id]);
        $item = BookItem::factory()->create(['status' => 'borrowed']);

        /** @var BorrowRecord $record */
        $record = BorrowRecord::factory()->create([
            'member_id' => $member->id,
            'book_item_id' => $item->id,
            'due_date' => now()->subDays(3),
            'status' => 'borrowed',
        ]);

        $record->update(['status' => 'overdue']);
        $record->update(['notes' => 'updated note']); // No status change

        $this->assertCount(1, Fine::where('borrow_record_id', $record->id)->get());
    }

    public function test_non_overdue_status_does_not_create_fine(): void
    {
        $member = Member::factory()->create();
        $item = BookItem::factory()->create(['status' => 'borrowed']);

        /** @var BorrowRecord $record */
        $record = BorrowRecord::factory()->create([
            'member_id' => $member->id,
            'book_item_id' => $item->id,
            'status' => 'borrowed',
        ]);

        $record->update(['status' => 'returned']);

        $this->assertDatabaseMissing('fines', ['borrow_record_id' => $record->id]);
    }

    public function test_overdue_sends_notification_to_member_user(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $member = Member::factory()->create(['user_id' => $user->id]);
        $item = BookItem::factory()->create(['status' => 'borrowed']);

        /** @var BorrowRecord $record */
        $record = BorrowRecord::factory()->create([
            'member_id' => $member->id,
            'book_item_id' => $item->id,
            'due_date' => now()->subDays(2),
            'status' => 'borrowed',
        ]);

        $record->update(['status' => 'overdue']);

        Notification::assertSentTo($user, FineCreatedNotification::class);
    }

    public function test_member_without_user_does_not_crash(): void
    {
        $member = Member::factory()->create(['user_id' => null]);
        $item = BookItem::factory()->create(['status' => 'borrowed']);

        /** @var BorrowRecord $record */
        $record = BorrowRecord::factory()->create([
            'member_id' => $member->id,
            'book_item_id' => $item->id,
            'due_date' => now()->subDays(1),
            'status' => 'borrowed',
        ]);

        $record->update(['status' => 'overdue']);

        $this->assertDatabaseHas('fines', ['borrow_record_id' => $record->id]);
    }
}
