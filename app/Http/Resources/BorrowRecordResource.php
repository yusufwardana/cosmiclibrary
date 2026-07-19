<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\BorrowRecord */
class BorrowRecordResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'borrow_date' => $this->borrow_date?->toDateString(),
            'due_date' => $this->due_date?->toDateString(),
            'return_date' => $this->return_date?->toDateString(),
            'status' => $this->status,
            'extend_count' => $this->extend_count,
            'member' => $this->whenLoaded('member', fn () => new MemberResource($this->member)),
            'book_item' => $this->whenLoaded('bookItem', fn () => [
                'id' => $this->bookItem->id,
                'barcode' => $this->bookItem->barcode,
                'book' => $this->whenLoaded('bookItem.book', fn () => [
                    'id' => $this->bookItem->book->id,
                    'title' => $this->bookItem->book->title,
                ]),
            ]),
        ];
    }
}