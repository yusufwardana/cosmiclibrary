<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\BorrowRecord;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OverdueNotification extends Notification
{
    use Queueable;

    public function __construct(public BorrowRecord $borrowRecord) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Pengembalian Buku Terlambat')
            ->line("Buku \"{$this->borrowRecord->bookItem->book->title}\" telah melewati batas waktu pengembalian.")
            ->line("Batas waktu: {$this->borrowRecord->due_date->format('d/m/Y')}")
            ->action('Lihat Detail', route('borrows.show', $this->borrowRecord))
            ->line('Segera kembalikan buku untuk menghindari denda tambahan.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'overdue',
            'borrow_record_id' => $this->borrowRecord->id,
            'book_title' => $this->borrowRecord->bookItem->book->title ?? null,
            'due_date' => $this->borrowRecord->due_date->toDateString(),
        ];
    }
}
