<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\BorrowRecord;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MarkOverdueRecords implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        BorrowRecord::where('status', 'borrowed')
            ->where('due_date', '<', now())
            ->chunkById(100, function ($records): void {
                $records->each(fn (BorrowRecord $r) => $r->update(['status' => 'overdue']));
            });
    }
}
