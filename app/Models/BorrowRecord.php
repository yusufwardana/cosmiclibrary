<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\BorrowRecordFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $member_id
 * @property int $book_item_id
 * @property int|null $librarian_out_id
 * @property int|null $librarian_in_id
 * @property Carbon $borrow_date
 * @property Carbon $due_date
 * @property Carbon|null $return_date
 * @property int $extend_count
 * @property string $status
 * @property string|null $notes
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Member $member
 * @property-read BookItem $bookItem
 * @property-read User|null $librarianOut
 * @property-read User|null $librarianIn
 */
class BorrowRecord extends Model
{
    /** @use HasFactory<BorrowRecordFactory> */
    use HasFactory;

    protected $fillable = [
        'member_id', 'book_item_id', 'librarian_out_id', 'librarian_in_id',
        'borrow_date', 'due_date', 'return_date', 'extend_count', 'status', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'borrow_date' => 'date',
            'due_date' => 'date',
            'return_date' => 'date',
            'extend_count' => 'integer',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function bookItem(): BelongsTo
    {
        return $this->belongsTo(BookItem::class);
    }

    public function librarianOut(): BelongsTo
    {
        return $this->belongsTo(User::class, 'librarian_out_id');
    }

    public function librarianIn(): BelongsTo
    {
        return $this->belongsTo(User::class, 'librarian_in_id');
    }

    public function fines(): HasMany
    {
        return $this->hasMany(Fine::class);
    }
}
