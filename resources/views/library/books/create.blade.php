@extends('layouts.app')

@section('title', 'Tambah Buku')

@section('content')
<h1>Tambah Buku</h1>

<form action="{{ route('books.store') }}" method="POST">
    @csrf
    <div class="form-group">
        <label for="title">Judul</label>
        <input type="text" name="title" id="title" class="form-input" required>
    </div>
    <div class="form-group">
        <label for="author">Penulis</label>
        <input type="text" name="author" id="author" class="form-input" required>
    </div>
    <div class="form-group">
        <label for="isbn">ISBN</label>
        <input type="text" name="isbn" id="isbn" class="form-input">
    </div>
    <div class="form-group">
        <label for="category_id">Kategori</label>
        <select name="category_id" id="category_id" class="form-input">
            <option value="">-- Pilih Kategori --</option>
        </select>
    </div>
    <div class="form-group">
        <label for="publisher">Penerbit</label>
        <input type="text" name="publisher" id="publisher" class="form-input">
    </div>
    <div class="form-group">
        <label for="publication_year">Tahun Terbit</label>
        <input type="number" name="publication_year" id="publication_year" class="form-input">
    </div>
    <div class="form-group">
        <label for="description">Deskripsi</label>
        <textarea name="description" id="description" class="form-input" rows="4"></textarea>
    </div>
    <div class="form-group">
        <label for="cover_image">URL Sampul</label>
        <input type="text" name="cover_image" id="cover_image" class="form-input">
    </div>
    <button type="submit" class="btn btn-primary">Simpan</button>
    <a href="{{ route('books.index') }}" class="btn">Batal</a>
</form>
@endsection