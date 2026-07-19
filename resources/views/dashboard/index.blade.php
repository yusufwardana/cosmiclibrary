@extends('layouts.app')

@section('title', 'Dasbor')

@section('content')
<div class="library-header">
    <h1>Dasbor</h1>
</div>

<div class="stats-grid">
    <div class="card stat-card">
        <h3>Total Buku</h3>
        <p class="stat-number">{{ $totalBooks }}</p>
    </div>
    <div class="card stat-card">
        <h3>Peminjaman Aktif</h3>
        <p class="stat-number">{{ $activeBorrows }}</p>
    </div>
    <div class="card stat-card stat-overdue">
        <h3>Terlambat</h3>
        <p class="stat-number">{{ $overdueBorrows }}</p>
    </div>
    <div class="card stat-card">
        <h3>Anggota Aktif</h3>
        <p class="stat-number">{{ $totalMembers }}</p>
    </div>
</div>

@if($sidebarWidgets->isNotEmpty())
<div class="widgets-sidebar">
    @foreach($sidebarWidgets as $widget)
        @if(View::exists($widget->view))
            @include($widget->view, ['widget' => $widget, 'settings' => $widget->settings ?? []])
        @endif
    @endforeach
</div>
@endif
@endsection
