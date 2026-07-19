<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Fine;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FineCreatedNotification extends Notification
{
    use Queueable;

    public function __construct(public Fine $fine) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Denda Perpustakaan')
            ->line("Denda sebesar Rp {$this->fine->fine_amount} telah dibuat.")
            ->line("Alasan: {$this->fine->fine_type}")
            ->action('Lihat Detail', route('fines.show', $this->fine))
            ->line('Silakan lakukan pembayaran sebelum batas waktu.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'fine_created',
            'fine_id' => $this->fine->id,
            'amount' => $this->fine->fine_amount,
            'reason' => $this->fine->fine_type,
        ];
    }
}
