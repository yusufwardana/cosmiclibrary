@extends('layouts.app')

@section('title', 'Pinjam Buku')

@section('content')
<h1>Pinjam Buku</h1>

<form action="{{ route('borrows.store') }}" method="POST">
    @csrf
    <div class="form-group">
        <label for="member_id" class="form-label">ID Anggota</label>
        <input type="number" name="member_id" id="member_id" class="form-input" value="{{ old('member_id') }}" required>
        @error('member_id') <p class="form-error">{{ $message }}</p> @enderror
    </div>
    <div class="form-group">
        <label for="book_item_id" class="form-label">Eksemplar Buku</label>
        <select name="book_item_id" id="book_item_id" class="form-input" required>
            <option value="">-- Pilih Eksemplar --</option>
            @foreach($availableItems as $item)
                <option value="{{ $item->id }}" @selected(old('book_item_id') == $item->id)>
                    {{ $item->book->title ?? 'Buku #'.$item->book_id }} — {{ $item->barcode }}
                </option>
            @endforeach
        </select>
        @error('book_item_id') <p class="form-error">{{ $message }}</p> @enderror
    </div>
    <div class="form-group">
        <label for="loan_period_days" class="form-label">Lama Pinjam (hari)</label>
        <input type="number" name="loan_period_days" id="loan_period_days" class="form-input" value="{{ old('loan_period_days', 7) }}" min="1" max="30" required>
        @error('loan_period_days') <p class="form-error">{{ $message }}</p> @enderror
    </div>
    <button type="submit" class="btn btn-primary">Pinjamkan</button>
    <a href="{{ route('borrows.index') }}" class="btn">Batal</a>
</form>
@endsection