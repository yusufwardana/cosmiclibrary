@extends('layouts.app')

@section('title', 'Tambah Anggota')

@section('content')
<h1>Tambah Anggota</h1>

<form action="{{ route('members.store') }}" method="POST">
    @csrf
    <div class="form-group">
        <label for="member_number" class="form-label">No. Anggota</label>
        <input type="text" name="member_number" id="member_number" class="form-input" value="{{ old('member_number') }}" required>
        @error('member_number') <p class="form-error">{{ $message }}</p> @enderror
    </div>
    <div class="form-group">
        <label for="user_id" class="form-label">User ID</label>
        <input type="number" name="user_id" id="user_id" class="form-input" value="{{ old('user_id') }}" required>
        @error('user_id') <p class="form-error">{{ $message }}</p> @enderror
    </div>
    <div class="form-group">
        <label for="type" class="form-label">Tipe</label>
        <select name="type" id="type" class="form-input" required>
            <option value="">-- Pilih Tipe --</option>
            <option value="student" @selected(old('type') === 'student')>Siswa</option>
            <option value="teacher" @selected(old('type') === 'teacher')>Guru</option>
        </select>
        @error('type') <p class="form-error">{{ $message }}</p> @enderror
    </div>
    <div class="form-group">
        <label for="phone" class="form-label">Telepon</label>
        <input type="text" name="phone" id="phone" class="form-input" value="{{ old('phone') }}">
    </div>
    <div class="form-group">
        <label for="class_name" class="form-label">Kelas</label>
        <input type="text" name="class_name" id="class_name" class="form-input" value="{{ old('class_name') }}">
    </div>
    <div class="form-group">
        <label for="address" class="form-label">Alamat</label>
        <textarea name="address" id="address" class="form-textarea">{{ old('address') }}</textarea>
    </div>
    <div class="form-group">
        <label for="join_date" class="form-label">Tanggal Bergabung</label>
        <input type="date" name="join_date" id="join_date" class="form-input" value="{{ old('join_date', date('Y-m-d')) }}">
    </div>
    <div class="form-group">
        <label for="notes" class="form-label">Catatan</label>
        <textarea name="notes" id="notes" class="form-textarea">{{ old('notes') }}</textarea>
    </div>
    <button type="submit" class="btn btn-primary">Simpan</button>
    <a href="{{ route('members.index') }}" class="btn">Batal</a>
</form>
@endsection