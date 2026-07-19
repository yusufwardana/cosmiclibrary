@extends('layouts.app')

@section('title', 'Edit Anggota')

@section('content')
<h1>Edit Anggota</h1>

<form action="{{ route('members.update', $member) }}" method="POST">
    @csrf @method('PUT')
    <div class="form-group">
        <label for="member_number" class="form-label">No. Anggota</label>
        <input type="text" name="member_number" id="member_number" class="form-input" value="{{ old('member_number', $member->member_number) }}" required>
        @error('member_number') <p class="form-error">{{ $message }}</p> @enderror
    </div>
    <div class="form-group">
        <label for="type" class="form-label">Tipe</label>
        <select name="type" id="type" class="form-input" required>
            <option value="student" @selected(old('type', $member->type) === 'student')>Siswa</option>
            <option value="teacher" @selected(old('type', $member->type) === 'teacher')>Guru</option>
        </select>
        @error('type') <p class="form-error">{{ $message }}</p> @enderror
    </div>
    <div class="form-group">
        <label for="phone" class="form-label">Telepon</label>
        <input type="text" name="phone" id="phone" class="form-input" value="{{ old('phone', $member->phone) }}">
    </div>
    <div class="form-group">
        <label for="class_name" class="form-label">Kelas</label>
        <input type="text" name="class_name" id="class_name" class="form-input" value="{{ old('class_name', $member->class_name) }}">
    </div>
    <div class="form-group">
        <label for="address" class="form-label">Alamat</label>
        <textarea name="address" id="address" class="form-textarea">{{ old('address', $member->address) }}</textarea>
    </div>
    <div class="form-group">
        <label for="status" class="form-label">Status</label>
        <select name="status" id="status" class="form-input">
            <option value="active" @selected(old('status', $member->status) === 'active')>Aktif</option>
            <option value="inactive" @selected(old('status', $member->status) === 'inactive')>Nonaktif</option>
        </select>
    </div>
    <div class="form-group">
        <label for="notes" class="form-label">Catatan</label>
        <textarea name="notes" id="notes" class="form-textarea">{{ old('notes', $member->notes) }}</textarea>
    </div>
    <button type="submit" class="btn btn-primary">Perbarui</button>
    <a href="{{ route('members.index') }}" class="btn">Batal</a>
</form>
@endsection