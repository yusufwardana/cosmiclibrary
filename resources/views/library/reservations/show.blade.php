@extends('layouts.app')

@section('title', 'Detail Reservasi')

@section('content')
<div class="library-header">
    <h1>Detail Reservasi</h1>
    <a href="{{ route('reservations.index') }}" class="btn btn-secondary">Kembali</a>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-bordered">
            <tr><th>Anggota</th><td>{{ $reservation->member?->user?->name ?? '-' }}</td></tr>
            <tr><th>Buku</th><td>{{ $reservation->book?->title ?? '-' }}</td></tr>
            <tr><th>Status</th><td>{{ $reservation->status }}</td></tr>
            <tr><th>Dibuat</th><td>{{ $reservation->created_at?->format('d/m/Y H:i') ?? '-' }}</td></tr>
            <tr><th>Kedaluwarsa</th><td>{{ $reservation->expires_at?->format('d/m/Y H:i') ?? '-' }}</td></tr>
        </table>
    </div>
</div>

@if($reservation->status === 'pending')
<form action="{{ route('reservations.cancel', $reservation) }}" method="POST" class="mt-3">
    @csrf
    <button class="btn btn-danger" onclick="return confirm('Batalkan reservasi?')">Batalkan Reservasi</button>
</form>
@endif
@endsection