<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Member;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var Member $member */
        $member = $this->route('member');

        return [
            'user_id' => ['nullable', 'exists:users,id'],
            'member_number' => ['required', 'string', 'max:30', Rule::unique('members,member_number')->ignore($member->id)],
            'type' => ['required', Rule::in(['student', 'teacher', 'staff'])],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string'],
            'class_name' => ['nullable', 'string', 'max:50'],
            'join_date' => ['nullable', 'date'],
            'photo' => ['nullable', 'string', 'max:255'],
            'status' => ['sometimes', Rule::in(['active', 'inactive', 'suspended'])],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'member_number' => 'Nomor Anggota',
            'type' => 'Tipe Anggota',
            'class_name' => 'Kelas',
            'join_date' => 'Tanggal Gabung',
        ];
    }
}
