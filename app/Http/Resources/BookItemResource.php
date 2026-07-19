<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\BookItem */
class BookItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'barcode' => $this->barcode,
            'call_number' => $this->call_number,
            'shelf_location' => $this->shelf_location,
            'condition' => $this->condition,
            'status' => $this->status,
        ];
    }
}