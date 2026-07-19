@extends('installer.wizard')

@section('installer-content')
    <h2 class="text-2xl font-semibold text-white mb-4">Informasi Sekolah</h2>
    <p class="text-purple-200 mb-6">Masukkan data sekolah Anda.</p>

    <form method="POST" action="{{ route('installer.school.store') }}" enctype="multipart/form-data" class="space-y-4">
        @csrf
        <div>
            <label class="block text-white text-sm mb-1">Nama Sekolah <span class="text-red-400">*</span></label>
            <input type="text" name="school_name" value="{{ old('school_name') }}" required
                   class="w-full px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-white focus:ring-2 focus:ring-purple-500">
        </div>
        <div>
            <label class="block text-white text-sm mb-1">Alamat</label>
            <textarea name="school_address" rows="2"
                      class="w-full px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-white focus:ring-2 focus:ring-purple-500">{{ old('school_address') }}</textarea>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-white text-sm mb-1">Telepon</label>
                <input type="text" name="school_phone" value="{{ old('school_phone') }}"
                       class="w-full px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-white focus:ring-2 focus:ring-purple-500">
            </div>
            <div>
                <label class="block text-white text-sm mb-1">Email Sekolah</label>
                <input type="email" name="school_email" value="{{ old('school_email') }}"
                       class="w-full px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-white focus:ring-2 focus:ring-purple-500">
            </div>
        </div>
        <div>
            <label class="block text-white text-sm mb-1">Logo Sekolah</label>
            <input type="file" name="school_logo" accept="image/*"
                   class="w-full text-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-purple-600 file:text-white">
        </div>

        <div class="flex gap-4 pt-2">
            <a href="{{ route('installer.admin') }}"
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