@extends('installer.wizard')

@section('installer-content')
    <h2 class="text-2xl font-semibold text-white mb-4">Selamat Datang</h2>
    <p class="text-purple-200 mb-6">
        Selamat datang di wizard instalasi <strong class="text-white">CosmicLib</strong>.
        Panduan ini akan membantu Anda mengatur sistem perpustakaan dalam beberapa langkah sederhana.
    </p>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        @foreach([
            ['icon' => '1', 'label' => 'Persyaratan'],
            ['icon' => '2', 'label' => 'Database'],
            ['icon' => '3', 'label' => 'Administrasi'],
        ] as $step)
            <div class="bg-white/5 rounded-lg p-4 text-center border border-white/10">
                <div class="w-10 h-10 rounded-full bg-purple-600 text-white font-bold mx-auto mb-2 flex items-center justify-center">
                    {{ $step['icon'] }}
                </div>
                <p class="text-white text-sm">{{ $step['label'] }}</p>
            </div>
        @endforeach
    </div>

    <a href="{{ route('installer.license') }}"
       class="block w-full text-center py-3 rounded-lg bg-purple-600 hover:bg-purple-700 text-white font-semibold transition">
        Mulai Instalasi
    </a>
@endsection