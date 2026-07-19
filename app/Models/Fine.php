<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $borrow_record_id
 * @property string $fine_type
 * @property string $fine_amount
 * @property string $paid_amount
 * @property string $status
 * @property Carbon|null $payment_date
 * @property int|null $waived_by
 * @property string|null $notes
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read BorrowRecord $borrowRecord
 * @property-read User|null $waivedBy
 */
class Fine extends Model
{
    use HasFactory;

    protected $fillable = [
        'borrow_record_id', 'fine_type', 'fine_amount', 'paid_amount',
        'status', 'payment_date', 'waived_by', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'fine_amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'payment_date' => 'date',
        ];
    }

    public function borrowRecord(): BelongsTo
    {
        return $this->belongsTo(BorrowRecord::class);
    }

    public function waivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'waived_by');
    }
}
