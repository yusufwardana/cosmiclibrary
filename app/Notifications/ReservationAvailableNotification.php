<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReservationAvailableNotification extends Notification
{
    use Queueable;

    public function __construct(public Reservation $reservation) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Buku Reservasi Tersedia')
            ->line("Buku \"{$this->reservation->book->title}\" yang Anda reservasi telah tersedia.")
            ->line('Silakan ambil buku dalam 48 jam.')
            ->action('Lihat Detail', route('reservations.show', $this->reservation))
            ->line('Reservasi akan otomatis dibatalkan jika tidak diambil dalam waktu 48 jam.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'reservation_available',
            'reservation_id' => $this->reservation->id,
            'book_title' => $this->reservation->book->title ?? null,
        ];
    }
}
