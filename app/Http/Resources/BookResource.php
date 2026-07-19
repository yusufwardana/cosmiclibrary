<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Book */
class BookResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'isbn' => $this->isbn,
            'author' => $this->author,
            'publisher' => $this->publisher,
            'publish_year' => $this->publish_year,
            'ddc_classification' => $this->ddc_classification,
            'available_copies' => $this->available_copies,
            'total_copies' => $this->total_copies,
            'category' => $this->whenLoaded('category', fn () => ['id' => $this->category->id, 'name' => $this->category->name]),
            'items' => BookItemResource::collection($this->whenLoaded('items')),
        ];
    }
}