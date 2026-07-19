<?php

declare(strict_types=1);

namespace App\Http\Requests\Installer;

use Illuminate\Foundation\Http\FormRequest;

class SmtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'mail_driver' => 'required|string|in:smtp,mail,ses,postmark,sendmail,log',
            'mail_host' => 'required_if:mail_driver,smtp|nullable|string|max:255',
            'mail_port' => 'required_if:mail_driver,smtp|nullable|integer|min:1|max:65535',
            'mail_username' => 'nullable|string|max:255',
            'mail_password' => 'nullable|string|max:255',
            'mail_encryption' => 'nullable|string|in:tls,ssl,null',
            'mail_from_address' => 'required|email|max:255',
            'mail_from_name' => 'required|string|max:255',
        ];
    }

    public function attributes(): array
    {
        return [
            'mail_driver' => 'Driver Email',
            'mail_host' => 'Host SMTP',
            'mail_port' => 'Port SMTP',
            'mail_username' => 'Username SMTP',
            'mail_password' => 'Password SMTP',
            'mail_encryption' => 'Enkripsi',
            'mail_from_address' => 'Alamat Pengirim',
            'mail_from_name' => 'Nama Pengirim',
        ];
    }
}
