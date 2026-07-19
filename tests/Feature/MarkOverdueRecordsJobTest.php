<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Jobs\MarkOverdueRecords;
use App\Models\BookItem;
use App\Models\BorrowRecord;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class MarkOverdueRecordsJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_marks_overdue_borrowed_records(): void
    {
        Notification::fake();

        $member = Member::factory()->create();
        $item = BookItem::factory()->create(['status' => 'borrowed']);

        $overdue = BorrowRecord::factory()->create([
            'member_id' => $member->id,
            'book_item_id' => $item->id,
            'due_date' => now()->subDays(5),
            'status' => 'borrowed',
        ]);

        $notOverdue = BorrowRecord::factory()->create([
            'member_id' => $member->id,
            'book_item_id' => BookItem::factory()->create(['status' => 'borrowed']),
            'due_date' => now()->addDays(5),
            'status' => 'borrowed',
        ]);

        (new MarkOverdueRecords)->handle();

        $overdue->refresh();
        $notOverdue->refresh();

        $this->assertSame('overdue', $overdue->status);
        $this->assertSame('borrowed', $notOverdue->status);
    }

    public function test_job_triggers_observer_and_creates_fine(): void
    {
        Notification::fake();

        $member = Member::factory()->create();
        $item = BookItem::factory()->create(['status' => 'borrowed']);

        $record = BorrowRecord::factory()->create([
            'member_id' => $member->id,
            'book_item_id' => $item->id,
            'due_date' => now()->subDays(3),
            'status' => 'borrowed',
        ]);

        (new MarkOverdueRecords)->handle();

        $this->assertDatabaseHas('fines', [
            'borrow_record_id' => $record->id,
            'fine_type' => 'overdue',
            'status' => 'unpaid',
        ]);
    }
}
