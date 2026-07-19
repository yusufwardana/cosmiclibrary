@extends('layouts.app')

@section('title', 'Detail Peminjaman')

@section('content')
<div class="library-header">
    <h1>Detail Peminjaman</h1>
    <a href="{{ route('borrows.index') }}" class="btn">Kembali</a>
</div>

<div class="card">
    <dl class="detail-list">
        <dt>Peminjam</dt>
        <dd>{{ $borrow->member->user->name ?? '-' }} ({{ $borrow->member->member_number ?? '-' }})</dd>
        <dt>Buku</dt>
        <dd>{{ $borrow->bookItem->book->title ?? '-' }}</dd>
        <dt>Barcode Eksemplar</dt>
        <dd>{{ $borrow->bookItem->barcode ?? '-' }}</dd>
        <dt>Tanggal Pinjam</dt>
        <dd>{{ $borrow->borrow_date->format('d/m/Y') }}</dd>
        <dt>Jatuh Tempo</dt>
        <dd>{{ $borrow->due_date->format('d/m/Y') }}</dd>
        <dt>Tanggal Kembali</dt>
        <dd>{{ $borrow->return_date?->format('d/m/Y') ?? '-' }}</dd>
        <dt>Status</dt>
        <dd>
            @if($borrow->status === 'returned')
                <span class="badge badge-success">Dikembalikan</span>
            @elseif($borrow->status === 'overdue')
                <span class="badge badge-danger">Terlambat</span>
            @else
                <span class="badge badge-warning">Dipinjam</span>
            @endif
        </dd>
    </dl>
</div>

@if($borrow->fines && $borrow->fines->count())
<div class="card">
    <h2 class="card-title">Denda</h2>
    <table class="table">
        <thead>
            <tr><th>Tipe</th><th>Jumlah</th><th>Status</th></tr>
        </thead>
        <tbody>
        @foreach($borrow->fines as $fine)
            <tr>
                <td>{{ ucfirst($fine->type) }}</td>
                <td>Rp {{ number_format($fine->amount, 0, ',', '.') }}</td>
                <td>
                    <span class="badge {{ $fine->paid_at ? 'badge-success' : 'badge-danger' }}">
                        {{ $fine->paid_at ? 'Lunas' : 'Belum Bayar' }}
                    </span>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endif

@if($borrow->status !== 'returned')
    <a href="{{ route('borrows.return.form', $borrow) }}" class="btn btn-primary">Kembalikan Buku</a>
@endif
@endsection