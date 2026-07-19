@extends('layouts.app')

@section('title', 'Edit Buku')

@section('content')
<h1>Edit Buku</h1>

<form action="{{ route('books.update', $book) }}" method="POST">
    @csrf @method('PUT')
    <div class="form-group">
        <label for="title">Judul</label>
        <input type="text" name="title" id="title" class="form-input" value="{{ old('title', $book->title) }}" required>
        @error('title') <p class="form-error">{{ $message }}</p> @enderror
    </div>
    <div class="form-group">
        <label for="author">Penulis</label>
        <input type="text" name="author" id="author" class="form-input" value="{{ old('author', $book->author) }}" required>
        @error('author') <p class="form-error">{{ $message }}</p> @enderror
    </div>
    <div class="form-group">
        <label for="isbn">ISBN</label>
        <input type="text" name="isbn" id="isbn" class="form-input" value="{{ old('isbn', $book->isbn) }}">
        @error('isbn') <p class="form-error">{{ $message }}</p> @enderror
    </div>
    <div class="form-group">
        <label for="category_id">Kategori</label>
        <select name="category_id" id="category_id" class="form-input">
            <option value="">-- Pilih Kategori --</option>
        </select>
    </div>
    <div class="form-group">
        <label for="publisher">Penerbit</label>
        <input type="text" name="publisher" id="publisher" class="form-input" value="{{ old('publisher', $book->publisher) }}">
    </div>
    <div class="form-group">
        <label for="publication_year">Tahun Terbit</label>
        <input type="number" name="publication_year" id="publication_year" class="form-input" value="{{ old('publication_year', $book->publication_year) }}">
    </div>
    <div class="form-group">
        <label for="description">Deskripsi</label>
        <textarea name="description" id="description" class="form-input" rows="4">{{ old('description', $book->description) }}</textarea>
    </div>
    <div class="form-group">
        <label for="cover_image">URL Sampul</label>
        <input type="text" name="cover_image" id="cover_image" class="form-input" value="{{ old('cover_image', $book->cover_image) }}">
    </div>
    <button type="submit" class="btn btn-primary">Perbarui</button>
    <a href="{{ route('books.index') }}" class="btn">Batal</a>
</form>
@endsection