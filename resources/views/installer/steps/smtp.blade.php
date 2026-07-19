@extends('installer.wizard')

@section('installer-content')
    <h2 class="text-2xl font-semibold text-white mb-4">Konfigurasi Email</h2>
    <p class="text-purple-200 mb-6">Atur pengiriman email untuk notifikasi sistem. Bisa dilewati dengan memilih <strong>log</strong>.</p>

    <form method="POST" action="{{ route('installer.smtp.store') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-white text-sm mb-1">Mail Driver</label>
            <select name="mail_driver" class="w-full px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-white focus:ring-2 focus:ring-purple-500">
                <option value="log" class="bg-slate-800">Log (file)</option>
                <option value="smtp" class="bg-slate-800">SMTP</option>
            </select>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-white text-sm mb-1">Host</label>
                <input type="text" name="mail_host" value="{{ old('mail_host') }}"
                       class="w-full px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-white focus:ring-2 focus:ring-purple-500">
            </div>
            <div>
                <label class="block text-white text-sm mb-1">Port</label>
                <input type="number" name="mail_port" value="{{ old('mail_port', '587') }}"
                       class="w-full px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-white focus:ring-2 focus:ring-purple-500">
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-white text-sm mb-1">Username</label>
                <input type="text" name="mail_username" value="{{ old('mail_username') }}"
                       class="w-full px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-white focus:ring-2 focus:ring-purple-500">
            </div>
            <div>
                <label class="block text-white text-sm mb-1">Password</label>
                <input type="password" name="mail_password"
                       class="w-full px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-white focus:ring-2 focus:ring-purple-500">
            </div>
        </div>
        <div>
            <label class="block text-white text-sm mb-1">Enkripsi</label>
            <select name="mail_encryption" class="w-full px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-white focus:ring-2 focus:ring-purple-500">
                <option value="tls" class="bg-slate-800">TLS</option>
                <option value="ssl" class="bg-slate-800">SSL</option>
                <option value="" class="bg-slate-800">Tidak ada</option>
            </select>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-white text-sm mb-1">Alamat Pengirim</label>
                <input type="email" name="mail_from_address" value="{{ old('mail_from_address', 'noreply@library.test') }}" required
                       class="w-full px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-white focus:ring-2 focus:ring-purple-500">
            </div>
            <div>
                <label class="block text-white text-sm mb-1">Nama Pengirim</label>
                <input type="text" name="mail_from_name" value="{{ old('mail_from_name', 'CosmicLib') }}" required
                       class="w-full px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-white focus:ring-2 focus:ring-purple-500">
            </div>
        </div>

        <div class="flex gap-4 pt-2">
            <a href="{{ route('installer.school') }}"
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