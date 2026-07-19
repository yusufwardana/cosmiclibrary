@extends('layouts.app')

@section('title', 'Detail Denda')

@section('content')
<div class="library-header">
    <h1>Detail Denda</h1>
    <a href="{{ route('fines.index') }}" class="btn btn-secondary">Kembali</a>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-bordered">
            <tr><th>Anggota</th><td>{{ $fine->borrowRecord?->member?->user?->name ?? '-' }}</td></tr>
            <tr><th>Buku</th><td>{{ $fine->borrowRecord?->bookItem?->book?->title ?? '-' }}</td></tr>
            <tr><th>Jenis Denda</th><td>{{ $fine->fine_type }}</td></tr>
            <tr><th>Jumlah Denda</th><td>Rp{{ number_format($fine->fine_amount, 0, ',', '.') }}</td></tr>
            <tr><th>Sudah Dibayar</th><td>Rp{{ number_format($fine->paid_amount, 0, ',', '.') }}</td></tr>
            <tr><th>Status</th><td>{{ $fine->status }}</td></tr>
            @if($fine->payment_date)
            <tr><th>Tgl Pembayaran</th><td>{{ $fine->payment_date->format('d/m/Y') }}</td></tr>
            @endif
            <tr><th>Catatan</th><td>{{ $fine->notes ?? '-' }}</td></tr>
        </table>
    </div>
</div>

@if($fine->status === 'unpaid' || $fine->status === 'partially_paid')
<div class="mt-3 d-flex gap-2">
    <form action="{{ route('fines.pay', $fine) }}" method="POST" style="display:inline">
        @csrf
        <input type="hidden" name="amount" value="{{ $fine->fine_amount - $fine->paid_amount }}">
        <button class="btn btn-success">Bayar Denda</button>
    </form>
    <form action="{{ route('fines.waive', $fine) }}" method="POST" style="display:inline">
        @csrf
        <button class="btn btn-warning" onclick="return confirm('Hapuskan denda ini?')">Hapuskan Denda</button>
    </form>
</div>
@endif
@endsection