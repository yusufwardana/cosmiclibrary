<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\BookItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $book_id
 * @property string $barcode
 * @property string|null $call_number
 * @property string|null $shelf_location
 * @property string|null $acquisition_date
 * @property string|null $acquisition_source
 * @property string|null $price
 * @property string $condition
 * @property string $status
 * @property string|null $notes
 * @property-read Book $book
 */
class BookItem extends Model
{
    /** @use HasFactory<BookItemFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'book_id', 'barcode', 'call_number', 'shelf_location',
        'acquisition_date', 'acquisition_source', 'price',
        'condition', 'status', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'acquisition_date' => 'date',
        ];
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function borrowRecords(): HasMany
    {
        return $this->hasMany(BorrowRecord::class);
    }
}
