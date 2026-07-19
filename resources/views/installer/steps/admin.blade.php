@extends('installer.wizard')

@section('installer-content')
    <h2 class="text-2xl font-semibold text-white mb-4">Akun Administrator</h2>
    <p class="text-purple-200 mb-6">Buat akun admin pertama untuk mengelola sistem.</p>

    <form method="POST" action="{{ route('installer.admin.store') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-white text-sm mb-1">Nama Lengkap</label>
            <input type="text" name="name" value="{{ old('name') }}" required
                   class="w-full px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-white focus:ring-2 focus:ring-purple-500">
            @error('name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-white text-sm mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                   class="w-full px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-white focus:ring-2 focus:ring-purple-500">
            @error('email') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-white text-sm mb-1">Password</label>
            <input type="password" name="password" required
                   class="w-full px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-white focus:ring-2 focus:ring-purple-500">
            @error('password') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-white text-sm mb-1">Konfirmasi Password</label>
            <input type="password" name="password_confirmation" required
                   class="w-full px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-white focus:ring-2 focus:ring-purple-500">
        </div>

        <div class="flex gap-4 pt-2">
            <a href="{{ route('installer.database') }}"
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