@extends('layouts.app')

@section('title', 'Kategori')

@section('content')
<div class="library-header">
    <h1>Kategori</h1>
    <a href="{{ route('categories.create') }}" class="btn btn-primary">Kategori Baru</a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<table class="table">
    <thead>
        <tr>
            <th>Nama</th>
            <th>Deskripsi</th>
            <th>Jumlah Buku</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($categories as $cat)
        <tr>
            <td>{{ $cat->name }}</td>
            <td>{{ Str::limit($cat->description, 50) }}</td>
            <td>{{ $cat->books_count }}</td>
            <td>
                <a href="{{ route('categories.show', $cat) }}" class="btn btn-sm btn-info">Detail</a>
                <a href="{{ route('categories.edit', $cat) }}" class="btn btn-sm btn-warning">Edit</a>
                <form action="{{ route('categories.destroy', $cat) }}" method="POST" style="display:inline">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus?')">Hapus</button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="4">Belum ada kategori.</td></tr>
        @endforelse
    </tbody>
</table>
{{ $categories->links() }}
@endsection