@extends('layouts.app')

@section('title', 'Detail Kategori')

@section('content')
<div class="library-header">
    <h1>{{ $category->name }}</h1>
    <div>
        <a href="{{ route('categories.edit', $category) }}" class="btn btn-warning">Edit</a>
        <a href="{{ route('categories.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-bordered">
            <tr><th>Nama</th><td>{{ $category->name }}</td></tr>
            <tr><th>Slug</th><td>{{ $category->slug }}</td></tr>
            <tr><th>Deskripsi</th><td>{{ $category->description ?? '-' }}</td></tr>
            <tr><th>Kategori Induk</th><td>{{ $category->parent?->name ?? '-' }}</td></tr>
            <tr><th>Posisi</th><td>{{ $category->position ?? 0 }}</td></tr>
        </table>
    </div>
</div>

<div class="card mt-3">
    <div class="card-title">Buku dalam Kategori Ini</div>
    @if($category->books->count())
    <table class="table">
        <thead>
            <tr>
                <th>Judul</th>
                <th>ISBN</th>
                <th>Pengarang</th>
                <th>Tahun</th>
            </tr>
        </thead>
        <tbody>
            @foreach($category->books as $book)
            <tr>
                <td><a href="{{ route('books.show', $book) }}">{{ $book->title }}</a></td>
                <td>{{ $book->isbn }}</td>
                <td>{{ $book->author }}</td>
                <td>{{ $book->publication_year ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p class="text-muted">Belum ada buku dalam kategori ini.</p>
    @endif
</div>
@endsection