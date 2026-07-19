@extends('layouts.app')

@section('title', 'Reservasi Baru')

@section('content')
<div class="library-header">
    <h1>Reservasi Baru</h1>
    <a href="{{ route('reservations.index') }}" class="btn btn-secondary">Kembali</a>
</div>

<form action="{{ route('reservations.store') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label for="member_id" class="form-label">Anggota</label>
        <select name="member_id" id="member_id" class="form-control" required>
            <option value="">Pilih Anggota</option>
            @foreach(\App\Models\Member::with('user')->get() as $member)
                <option value="{{ $member->id }}">{{ $member->user->name }} ({{ $member->member_number }})</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label for="book_id" class="form-label">Buku</label>
        <select name="book_id" id="book_id" class="form-control" required>
            <option value="">Pilih Buku</option>
            @foreach(\App\Models\Book::all() as $book)
                <option value="{{ $book->id }}">{{ $book->title }} ({{ $book->isbn }})</option>
            @endforeach
        </select>
    </div>
    <button class="btn btn-primary">Simpan</button>
</form>
@endsection