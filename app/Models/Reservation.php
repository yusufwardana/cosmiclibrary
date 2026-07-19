<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $member_id
 * @property int $book_id
 * @property Carbon|null $reserved_at
 * @property Carbon|null $expires_at
 * @property string $status
 * @property string|null $notes
 * @property-read Member $member
 * @property-read Book $book
 */
class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id', 'book_id', 'reserved_at', 'expires_at', 'status', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'reserved_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
}
