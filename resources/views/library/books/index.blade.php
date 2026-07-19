@extends('layouts.app')

@section('title', 'Daftar Buku')

@section('content')
<div class="library-header">
    <h1>Daftar Buku</h1>
    <a href="{{ route('books.create') }}" class="btn btn-primary">Tambah Buku</a>
</div>

<form method="GET" class="mb-4">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul/penulis" class="form-input">
    <button type="submit" class="btn">Cari</button>
</form>

<table class="table">
    <thead>
        <tr>
            <th>Judul</th>
            <th>Penulis</th>
            <th>ISBN</th>
            <th>Total</th>
            <th>Tersedia</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
    @foreach ($books as $book)
        <tr>
            <td>{{ $book->title }}</td>
            <td>{{ $book->author }}</td>
            <td>{{ $book->isbn }}</td>
            <td>{{ $book->total_copies }}</td>
            <td>{{ $book->available_copies }}</td>
            <td>
                <a href="{{ route('books.show', $book) }}" class="btn btn-sm">Lihat</a>
                <a href="{{ route('books.edit', $book) }}" class="btn btn-sm">Edit</a>
                <form action="{{ route('books.destroy', $book) }}" method="POST" style="display:inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Hapus buku ini?')">Hapus</button>
                </form>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

{{ $books->withQueryString()->links() }}
@endsection