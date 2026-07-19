@extends('layouts.app')

@section('title', 'Reservasi')

@section('content')
<div class="library-header">
    <h1>Reservasi</h1>
    <a href="{{ route('reservations.create') }}" class="btn btn-primary">Reservasi Baru</a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<table class="table">
    <thead>
        <tr>
            <th>Anggota</th>
            <th>Buku</th>
            <th>Status</th>
            <th>Kedaluwarsa</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($reservations as $res)
        <tr>
            <td>{{ $res->member?->user?->name ?? '-' }}</td>
            <td>{{ $res->book?->title ?? '-' }}</td>
            <td>{{ $res->status }}</td>
            <td>{{ $res->expires_at?->format('d/m/Y H:i') ?? '-' }}</td>
            <td>
                <a href="{{ route('reservations.show', $res) }}" class="btn btn-sm btn-info">Detail</a>
                @if($res->status === 'pending')
                <form action="{{ route('reservations.cancel', $res) }}" method="POST" style="display:inline">
                    @csrf
                    <button class="btn btn-sm btn-danger" onclick="return confirm('Batalkan?')">Batal</button>
                </form>
                @endif
            </td>
        </tr>
        @empty
        <tr><td colspan="5">Belum ada reservasi.</td></tr>
        @endforelse
    </tbody>
</table>
{{ $reservations->links() }}
@endsection