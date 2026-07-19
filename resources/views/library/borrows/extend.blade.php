@extends('layouts.app')

@section('title', 'Perpanjang Peminjaman')

@section('content')
<div class="library-header">
    <h1>Perpanjang Peminjaman</h1>
    <a href="{{ route('borrows.index') }}" class="btn btn-secondary">Kembali</a>
</div>

<div class="card">
    <div class="card-body">
        <p><strong>Anggota:</strong> {{ $borrow->member->user->name ?? '-' }}</p>
        <p><strong>Buku:</strong> {{ $borrow->bookItem->book->title ?? '-' }}</p>
        <p><strong>Tenggat:</strong> {{ $borrow->due_date->format('d/m/Y') }}</p>
        <p><strong>Perpanjangan ke-:</strong> {{ $borrow->extend_count }}</p>

        <form action="{{ route('borrows.extend.process', $borrow) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="extra_days" class="form-label">Hari Tambahan</label>
                <input type="number" name="extra_days" id="extra_days" class="form-control" value="{{ config('library.extend_days', 7) }}" min="1" max="30">
            </div>
            <button class="btn btn-primary">Perpanjang</button>
        </form>
    </div>
</div>
@endsection