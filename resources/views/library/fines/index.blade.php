@extends('layouts.app')

@section('title', 'Denda')

@section('content')
<div class="library-header">
    <h1>Denda</h1>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<table class="table">
    <thead>
        <tr>
            <th>Anggota</th>
            <th>Jenis</th>
            <th>Jumlah</th>
            <th>Dibayar</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($fines as $fine)
        <tr>
            <td>{{ $fine->borrowRecord?->member?->user?->name ?? '-' }}</td>
            <td>{{ $fine->fine_type }}</td>
            <td>Rp{{ number_format($fine->fine_amount, 0, ',', '.') }}</td>
            <td>Rp{{ number_format($fine->paid_amount, 0, ',', '.') }}</td>
            <td>{{ $fine->status }}</td>
            <td>
                <a href="{{ route('fines.show', $fine) }}" class="btn btn-sm btn-info">Detail</a>
            </td>
        </tr>
        @empty
        <tr><td colspan="6">Belum ada denda.</td></tr>
        @endforelse
    </tbody>
</table>
{{ $fines->links() }}
@endsection