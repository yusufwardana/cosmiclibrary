@extends('layouts.app')

@section('title', 'Detail Buku')

@section('content')
<h1>{{ $book->title }}</h1>
<p><strong>Penulis:</strong> {{ $book->author }}</p>
<p><strong>ISBN:</strong> {{ $book->isbn }}</p>
<p><strong>Penerbit:</strong> {{ $book->publisher }}</p>
<p><strong>Tahun Terbit:</strong> {{ $book->publication_year }}</p>
<p><strong>Deskripsi:</strong> {{ $book->description }}</p>
<p><strong>Total Kopi:</strong> {{ $book->total_copies }} | <strong>Tersedia:</strong> {{ $book->available_copies }}</p>

<h2>Daftar Eksemplar</h2>
<table class="table">
    <thead>
        <tr>
            <th>Kode</th>
            <th>Status</th>
            <th>Lokasi</th>
        </tr>
    </thead>
    <tbody>
    @foreach ($book->items as $item)
        <tr>
            <td>{{ $item->barcode }}</td>
            <td>{{ $item->status }}</td>
            <td>{{ $item->location }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<a href="{{ route('books.index') }}" class="btn">Kembali</a>
@endsection