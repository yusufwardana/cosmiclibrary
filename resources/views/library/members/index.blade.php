@extends('layouts.app')

@section('title', 'Daftar Anggota')

@section('content')
<div class="library-header">
    <h1>Daftar Anggota</h1>
    <a href="{{ route('members.create') }}" class="btn btn-primary">Tambah Anggota</a>
</div>

<form method="GET" class="mb-4">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama/nomor anggota" class="form-input" style="width:auto;display:inline-block">
    <select name="status" class="form-input" style="width:auto;display:inline-block">
        <option value="">Semua Status</option>
        <option value="active" @selected(request('status') === 'active')>Aktif</option>
        <option value="inactive" @selected(request('status') === 'inactive')>Nonaktif</option>
    </select>
    <button type="submit" class="btn">Cari</button>
</form>

<table class="table">
    <thead>
        <tr>
            <th>No. Anggota</th>
            <th>Nama</th>
            <th>Tipe</th>
            <th>Kelas</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
    @foreach ($members as $member)
        <tr>
            <td>{{ $member->member_number }}</td>
            <td>{{ $member->user->name ?? '-' }}</td>
            <td>{{ ucfirst($member->type) }}</td>
            <td>{{ $member->class_name ?? '-' }}</td>
            <td>
                <span class="badge {{ $member->status === 'active' ? 'badge-success' : 'badge-danger' }}">
                    {{ $member->status === 'active' ? 'Aktif' : 'Nonaktif' }}
                </span>
            </td>
            <td>
                <a href="{{ route('members.show', $member) }}" class="btn btn-sm">Lihat</a>
                <a href="{{ route('members.edit', $member) }}" class="btn btn-sm">Edit</a>
                <form action="{{ route('members.destroy', $member) }}" method="POST" style="display:inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Hapus anggota ini?')">Hapus</button>
                </form>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

{{ $members->withQueryString()->links() }}
@endsection