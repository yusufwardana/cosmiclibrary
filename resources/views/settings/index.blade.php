@extends('layouts.app')

@section('title', 'Pengaturan')

@section('content')
<div class="library-header">
    <h1>Pengaturan</h1>
</div>

<form action="{{ route('settings.update') }}" method="POST">
    @csrf
    @method('PUT')

    @foreach($groups as $groupName => $items)
    <div class="card">
        <h2 class="card-title">{{ ucfirst($groupName) }}</h2>

        @foreach($items as $setting)
        <div class="form-group">
            <label class="form-label" for="{{ $setting->key }}">{{ $setting->key }}</label>
            @php
                $current = $setting->typedValue();
                $fieldId = 'setting_' . str_replace('.', '_', $setting->key);
            @endphp

            @if($setting->type === 'boolean')
            <select class="form-select" id="{{ $fieldId }}" name="{{ $setting->key }}">
                <option value="1" @if((bool)$current) selected @endif>Ya</option>
                <option value="0" @if(!(bool)$current) selected @endif>Tidak</option>
            </select>
            @elseif($setting->type === 'json')
            <textarea class="form-textarea" id="{{ $fieldId }}" name="{{ $setting->key }}">{{ is_array($current) ? json_encode($current, JSON_PRETTY_PRINT) : $current }}</textarea>
            @elseif($setting->type === 'integer')
            <input type="number" class="form-input" id="{{ $fieldId }}" name="{{ $setting->key }}" value="{{ $current }}">
            @else
            <input type="text" class="form-input" id="{{ $fieldId }}" name="{{ $setting->key }}" value="{{ $current }}">
            @endif
        </div>
        @endforeach
    </div>
    @endforeach

    @if($groups->isEmpty())
    <div class="card">
        <p class="text-muted">Belum ada pengaturan tersimpan.</p>
    </div>
    @endif

    <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
</form>
@endsection