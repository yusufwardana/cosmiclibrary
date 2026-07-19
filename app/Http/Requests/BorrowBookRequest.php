<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BorrowBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'member_id' => ['required', 'exists:members,id'],
            'book_item_id' => ['required', 'exists:book_items,id'],
            'loan_period_days' => ['nullable', 'integer', 'min:1', 'max:90'],
        ];
    }
}
