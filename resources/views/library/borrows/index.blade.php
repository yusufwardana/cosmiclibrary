@extends('layouts.app')

@section('title', 'Daftar Peminjaman')

@section('content')
<div class="library-header">
    <h1>Daftar Peminjaman</h1>
    <a href="{{ route('borrows.create') }}" class="btn btn-primary">Pinjam Buku</a>
</div>

<form method="GET" class="mb-4">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama peminjam" class="form-input" style="width:auto;display:inline-block">
    <select name="status" class="form-input" style="width:auto;display:inline-block">
        <option value="">Semua Status</option>
        <option value="borrowed" @selected(request('status') === 'borrowed')>Dipinjam</option>
        <option value="returned" @selected(request('status') === 'returned')>Dikembalikan</option>
        <option value="overdue" @selected(request('status') === 'overdue')>Terlambat</option>
    </select>
    <button type="submit" class="btn">Cari</button>
</form>

<table class="table">
    <thead>
        <tr>
            <th>Peminjam</th>
            <th>Buku</th>
            <th>Tgl Pinjam</th>
            <th>Tgl Jatuh Tempo</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
    @foreach ($records as $record)
        <tr>
            <td>{{ $record->member->user->name ?? '-' }}</td>
            <td>{{ $record->bookItem->book->title ?? '-' }}</td>
            <td>{{ $record->borrow_date->format('d/m/Y') }}</td>
            <td>{{ $record->due_date->format('d/m/Y') }}</td>
            <td>
                @if($record->status === 'returned')
                    <span class="badge badge-success">Dikembalikan</span>
                @elseif($record->status === 'overdue')
                    <span class="badge badge-danger">Terlambat</span>
                @else
                    <span class="badge badge-warning">Dipinjam</span>
                @endif
            </td>
            <td>
                <a href="{{ route('borrows.show', $record) }}" class="btn btn-sm">Lihat</a>
                @if($record->status !== 'returned')
                    <a href="{{ route('borrows.return.form', $record) }}" class="btn btn-sm btn-primary">Kembalikan</a>
                    <a href="{{ route('borrows.extend.form', $record) }}" class="btn btn-sm btn-success">Perpanjang</a>
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

{{ $records->withQueryString()->links() }}
@endsection