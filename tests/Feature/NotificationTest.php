<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Book;
use App\Models\BookItem;
use App\Models\BorrowRecord;
use App\Models\Fine;
use App\Models\Member;
use App\Models\Reservation;
use App\Models\User;
use App\Notifications\FineCreatedNotification;
use App\Notifications\OverdueNotification;
use App\Notifications\ReservationAvailableNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_overdue_notification_sent_to_user(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $member = Member::factory()->create(['user_id' => $user->id]);
        $book = Book::factory()->create(['title' => 'Laravel Up & Running']);
        $item = BookItem::factory()->create(['book_id' => $book->id]);
        $record = BorrowRecord::factory()->create([
            'member_id' => $member->id,
            'book_item_id' => $item->id,
        ]);

        $user->notify(new OverdueNotification($record));

        Notification::assertSentTo($user, OverdueNotification::class);
    }

    public function test_fine_created_notification_sent_to_user(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $member = Member::factory()->create(['user_id' => $user->id]);
        $record = BorrowRecord::factory()->create(['member_id' => $member->id]);
        $fine = Fine::factory()->create([
            'borrow_record_id' => $record->id,
        ]);

        $user->notify(new FineCreatedNotification($fine));

        Notification::assertSentTo($user, FineCreatedNotification::class);
    }

    public function test_reservation_available_notification_sent_to_user(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $member = Member::factory()->create(['user_id' => $user->id]);
        $book = Book::factory()->create();
        $reservation = Reservation::factory()->create([
            'member_id' => $member->id,
            'book_id' => $book->id,
        ]);

        $user->notify(new ReservationAvailableNotification($reservation));

        Notification::assertSentTo($user, ReservationAvailableNotification::class);
    }

    public function test_overdue_notification_array_structure(): void
    {
        $member = Member::factory()->create();
        $book = Book::factory()->create(['title' => 'Clean Code']);
        $item = BookItem::factory()->create(['book_id' => $book->id]);
        $record = BorrowRecord::factory()->create([
            'member_id' => $member->id,
            'book_item_id' => $item->id,
        ]);

        $notification = new OverdueNotification($record);
        $array = $notification->toArray($member);

        $this->assertSame('overdue', $array['type']);
        $this->assertSame($record->id, $array['borrow_record_id']);
        $this->assertSame('Clean Code', $array['book_title']);
    }

    public function test_fine_created_notification_array_structure(): void
    {
        $record = BorrowRecord::factory()->create();
        $fine = Fine::factory()->create([
            'borrow_record_id' => $record->id,
            'fine_amount' => 25000,
            'fine_type' => 'overdue',
        ]);

        $notification = new FineCreatedNotification($fine);
        $array = $notification->toArray($record->member);

        $this->assertSame('fine_created', $array['type']);
        $this->assertSame($fine->id, $array['fine_id']);
        $this->assertSame('25000.00', $array['amount']);
        $this->assertSame('overdue', $array['reason']);
    }
}
