@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
<div class="library-header">
    <h1>Selamat Datang di CosmicLib</h1>
</div>

<div class="card">
    <p class="text-muted mb-4">Sistem perpustakaan digital untuk pengelolaan buku, anggota, dan peminjaman.</p>

    @auth
        <p>Anda sudah login sebagai <strong>{{ Auth::user()->name }}</strong>.</p>
        <a href="{{ route('dashboard') }}" class="btn btn-primary mt-4">Buka Dasbor</a>
    @else
        <p>Silakan <a href="{{ route('auth.login') }}">login</a> untuk mengakses sistem.</p>
    @endauth
</div>
@endsection