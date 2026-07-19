@extends('installer.wizard')

@section('installer-content')
    <h2 class="text-2xl font-semibold text-white mb-4">Konfigurasi Database</h2>
    <p class="text-purple-200 mb-6">Masukkan kredensial database MySQL Anda. Sistem akan menguji koneksi sebelum melanjutkan.</p>

    @if(session('error'))
        <div class="mb-4 p-3 rounded-lg bg-red-500/20 border border-red-500/30 text-red-100 text-sm">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('installer.database.test') }}" class="space-y-4">
        @csrf
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-white text-sm mb-1">Host</label>
                <input type="text" name="db_host" value="{{ old('db_host', '127.0.0.1') }}" required
                       class="w-full px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-white focus:ring-2 focus:ring-purple-500">
            </div>
            <div>
                <label class="block text-white text-sm mb-1">Port</label>
                <input type="number" name="db_port" value="{{ old('db_port', '3306') }}" required
                       class="w-full px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-white focus:ring-2 focus:ring-purple-500">
            </div>
            <div>
                <label class="block text-white text-sm mb-1">Nama Database</label>
                <input type="text" name="db_database" value="{{ old('db_database', 'cosmiclib') }}" required
                       class="w-full px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-white focus:ring-2 focus:ring-purple-500">
            </div>
            <div>
                <label class="block text-white text-sm mb-1">Username</label>
                <input type="text" name="db_username" value="{{ old('db_username', 'root') }}" required
                       class="w-full px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-white focus:ring-2 focus:ring-purple-500">
            </div>
        </div>
        <div>
            <label class="block text-white text-sm mb-1">Password</label>
            <input type="password" name="db_password"
                   class="w-full px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-white focus:ring-2 focus:ring-purple-500">
        </div>

        <div class="flex gap-4 pt-2">
            <a href="{{ route('installer.requirements') }}"
               class="flex-1 py-3 rounded-lg bg-white/10 hover:bg-white/20 text-white text-center transition">
                Kembali
            </a>
            <button type="submit"
                    class="flex-1 py-3 rounded-lg bg-purple-600 hover:bg-purple-700 text-white font-semibold transition">
                Tes & Lanjutkan
            </button>
        </div>
    </form>
@endsection