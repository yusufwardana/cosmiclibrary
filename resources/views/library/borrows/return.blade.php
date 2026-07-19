@extends('layouts.app')

@section('title', 'Kembalikan Buku')

@section('content')
<h1>Kembalikan Buku</h1>

<div class="card">
    <dl class="detail-list">
        <dt>Peminjam</dt>
        <dd>{{ $borrow->member->user->name ?? '-' }}</dd>
        <dt>Buku</dt>
        <dd>{{ $borrow->bookItem->book->title ?? '-' }}</dd>
        <dt>Tanggal Pinjam</dt>
        <dd>{{ $borrow->borrow_date->format('d/m/Y') }}</dd>
        <dt>Jatuh Tempo</dt>
        <dd>{{ $borrow->due_date->format('d/m/Y') }}</dd>
    </dl>
</div>

@if($borrow->due_date->isPast())
    <div class="alert alert-danger">
        Buku ini terlambat {{ now()->diffInDays($borrow->due_date) }} hari. Denda akan dihitung otomatis.
    </div>
@endif

<form action="{{ route('borrows.return.process', $borrow) }}" method="POST">
    @csrf @method('PUT')
    <p class="mb-4">Apakah Anda yakin ingin mengembalikan buku ini?</p>
    <button type="submit" class="btn btn-primary">Konfirmasi Pengembalian</button>
    <a href="{{ route('borrows.index') }}" class="btn">Batal</a>
</form>
@endsection