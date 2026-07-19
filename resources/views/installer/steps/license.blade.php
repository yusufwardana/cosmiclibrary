@extends('installer.wizard')

@section('installer-content')
    <h2 class="text-2xl font-semibold text-white mb-4">Lisensi</h2>
    <p class="text-purple-200 mb-4">
        CosmicLib dilisensikan di bawah <strong class="text-white">MIT License</strong>. Silakan baca ketentuan lisensi berikut sebelum melanjutkan.
    </p>

    <div class="bg-slate-900/80 rounded-lg p-4 text-sm text-slate-300 mb-6 h-48 overflow-y-auto border border-white/10">
        <p class="mb-2">MIT License</p>
        <p class="mb-2">Copyright (c) {{ date('Y') }} CosmicLib</p>
        <p class="mb-2">
            Permission is hereby granted, free of charge, to any person obtaining a copy
            of this software and associated documentation files (the "Software"), to deal
            in the Software without restriction...
        </p>
    </div>

    <form method="POST" action="{{ route('installer.license.accept') }}" class="space-y-4">
        @csrf
        <label class="flex items-center space-x-3 text-white">
            <input type="checkbox" name="accept" value="1" required
                   class="w-5 h-5 rounded border-white/20 bg-white/5 text-purple-600 focus:ring-purple-500">
            <span>Saya menyetujui ketentuan lisensi di atas</span>
        </label>

        <div class="flex gap-4">
            <a href="{{ route('installer.welcome') }}"
               class="flex-1 py-3 rounded-lg bg-white/10 hover:bg-white/20 text-white text-center transition">
                Kembali
            </a>
            <button type="submit"
                    class="flex-1 py-3 rounded-lg bg-purple-600 hover:bg-purple-700 text-white font-semibold transition">
                Lanjutkan
            </button>
        </div>
    </form>
@endsection