<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $category_id
 * @property string $title
 * @property string|null $isbn
 * @property string|null $author
 * @property string|null $publisher
 * @property int|null $publish_year
 * @property string|null $edition
 * @property string|null $language
 * @property int|null $pages
 * @property string|null $ddc_classification
 * @property string|null $description
 * @property string|null $cover_image
 * @property int $total_copies
 * @property int $available_copies
 * @property-read Category $category
 * @property-read BookItem $items
 */
class Book extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id', 'title', 'isbn', 'author', 'publisher', 'publish_year',
        'edition', 'language', 'pages', 'ddc_classification', 'description',
        'cover_image', 'total_copies', 'available_copies',
    ];

    protected function casts(): array
    {
        return [
            'publish_year' => 'integer',
            'pages' => 'integer',
            'total_copies' => 'integer',
            'available_copies' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(BookItem::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }
}
