<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\BorrowRecord;
use App\Models\Fine;
use App\Notifications\FineCreatedNotification;
use App\Services\SettingEngine;

class BorrowRecordObserver
{
    public function __construct(protected SettingEngine $settings) {}

    public function updated(BorrowRecord $borrowRecord): void
    {
        if (! $borrowRecord->wasChanged('status')) {
            return;
        }

        // Auto-create fine when borrow marked overdue
        if ($borrowRecord->status === 'overdue' && ! $borrowRecord->fines()->exists()) {
            if (! $borrowRecord->due_date->isPast()) {
                return;
            }

            $perDay = (int) $this->settings->get('library.overdue_fine_per_day', 1000);
            $overdueDays = (int) $borrowRecord->due_date->diffInDays(now());

            if ($overdueDays > 0) {
                $fine = Fine::create([
                    'borrow_record_id' => $borrowRecord->id,
                    'fine_type' => 'overdue',
                    'fine_amount' => $perDay * $overdueDays,
                    'status' => 'unpaid',
                    'notes' => "Keterlambatan {$overdueDays} hari",
                ]);

                $user = $borrowRecord->member->user;
                if ($user !== null) {
                    $user->notify(new FineCreatedNotification($fine));
                }
            }
        }
    }
}
