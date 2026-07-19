@extends('layouts.app')

@section('title', 'Detail Anggota')

@section('content')
<div class="library-header">
    <h1>Detail Anggota</h1>
    <div>
        <a href="{{ route('members.edit', $member) }}" class="btn">Edit</a>
        <a href="{{ route('members.index') }}" class="btn">Kembali</a>
    </div>
</div>

<div class="card">
    <dl class="detail-list">
        <dt>No. Anggota</dt>
        <dd>{{ $member->member_number }}</dd>
        <dt>Nama</dt>
        <dd>{{ $member->user->name ?? '-' }}</dd>
        <dt>Tipe</dt>
        <dd>{{ ucfirst($member->type) }}</dd>
        <dt>Kelas</dt>
        <dd>{{ $member->class_name ?? '-' }}</dd>
        <dt>Telepon</dt>
        <dd>{{ $member->phone ?? '-' }}</dd>
        <dt>Alamat</dt>
        <dd>{{ $member->address ?? '-' }}</dd>
        <dt>Tanggal Bergabung</dt>
        <dd>{{ $member->join_date?->format('d/m/Y') ?? '-' }}</dd>
        <dt>Status</dt>
        <dd>
            <span class="badge {{ $member->status === 'active' ? 'badge-success' : 'badge-danger' }}">
                {{ $member->status === 'active' ? 'Aktif' : 'Nonaktif' }}
            </span>
        </dd>
        <dt>Catatan</dt>
        <dd>{{ $member->notes ?? '-' }}</dd>
    </dl>
</div>

@if($history->count())
<div class="card">
    <h2 class="card-title">Riwayat Peminjaman</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Buku</th>
                <th>Tgl Pinjam</th>
                <th>Tgl Kembali</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        @foreach($history as $record)
            <tr>
                <td>{{ $record->bookItem->book->title ?? '-' }}</td>
                <td>{{ $record->borrow_date->format('d/m/Y') }}</td>
                <td>{{ $record->return_date?->format('d/m/Y') ?? '-' }}</td>
                <td>
                    <span class="badge {{ $record->status === 'returned' ? 'badge-success' : ($record->status === 'overdue' ? 'badge-danger' : 'badge-warning') }}">
                        {{ ucfirst($record->status) }}
                    </span>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endif
@endsection