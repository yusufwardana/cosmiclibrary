@extends('installer.wizard')

@section('installer-content')
    <h2 class="text-2xl font-semibold text-white mb-4">Persyaratan Sistem</h2>
    <p class="text-purple-200 mb-6">Pastikan semua persyaratan server terpenuhi sebelum melanjutkan.</p>

    @if(session('error'))
        <div class="mb-4 p-3 rounded-lg bg-red-500/20 border border-red-500/30 text-red-100 text-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-2 gap-3 mb-6">
        @foreach($requirements as $key => $req)
            <div class="flex items-center justify-between p-3 rounded-lg bg-white/5 border border-white/10">
                <span class="text-white text-sm">{{ $req['label'] }}</span>
                @if($req['meets'])
                    <span class="text-green-400">✓</span>
                @else
                    <span class="text-red-400">✗</span>
                @endif
            </div>
        @endforeach
    </div>

    <div class="flex gap-4">
        <a href="{{ route('installer.license') }}"
           class="flex-1 py-3 rounded-lg bg-white/10 hover:bg-white/20 text-white text-center transition">
            Kembali
        </a>
        <form method="POST" action="{{ route('installer.requirements.verify') }}" class="flex-1">
            @csrf
            <button type="submit"
                    class="w-full py-3 rounded-lg bg-purple-600 hover:bg-purple-700 text-white font-semibold transition">
                Verifikasi & Lanjutkan
            </button>
        </form>
    </div>
@endsection