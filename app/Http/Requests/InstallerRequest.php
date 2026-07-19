<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InstallerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'APP_NAME' => 'required|string|max:255',
            'APP_URL' => 'required|url',
            'DB_HOST' => 'required|string',
            'DB_PORT' => 'required|numeric',
            'DB_DATABASE' => 'required|string',
            'DB_USERNAME' => 'required|string',
            'DB_PASSWORD' => 'nullable|string',
        ];
    }
}
