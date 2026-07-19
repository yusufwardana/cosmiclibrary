<?php

declare(strict_types=1);

namespace App\Http\Requests\Installer;

use Illuminate\Foundation\Http\FormRequest;

class SchoolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'school_name' => 'required|string|max:255',
            'school_address' => 'nullable|string|max:500',
            'school_phone' => 'nullable|string|max:20',
            'school_email' => 'nullable|email|max:255',
            'school_logo' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ];
    }

    public function attributes(): array
    {
        return [
            'school_name' => 'Nama Sekolah',
            'school_address' => 'Alamat Sekolah',
            'school_phone' => 'Telepon Sekolah',
            'school_email' => 'Email Sekolah',
            'school_logo' => 'Logo Sekolah',
        ];
    }
}
