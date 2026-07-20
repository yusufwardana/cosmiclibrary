@extends('installer.wizard')

@section('installer-content')
    <h2 class="text-2xl font-semibold text-white mb-4">Konfirmasi Instalasi</h2>
    <p class="text-purple-200 mb-6">Periksa kembali data sebelum instalasi dimulai.</p>

    @if(empty($data['db_database']) || empty($data['admin_email']) || empty($data['school_name']))
        <div class="bg-amber-500/20 border border-amber-500/50 rounded-lg p-4 mb-4">
            <p class="text-amber-100 text-sm">
                Data wizard belum lengkap. Silakan kembali mengisi langkah Database, Admin, dan Sekolah sebelum menjalankan instalasi.
            </p>
        </div>
    @endif

    <div class="space-y-3 mb-6">
        @forelse($data as $key => $value)
            @if($value && !in_array($key, ['admin_password', 'mail_password', 'db_password', 'db_verified'], true))
                <div class="flex justify-between py-2 border-b border-white/10">
                    <span class="text-purple-200 text-sm">{{ str_replace('_', ' ', ucfirst($key)) }}</span>
                    <span class="text-white text-sm">{{ is_string($value) ? $value : json_encode($value) }}</span>
                </div>
            @endif
        @empty
            <p class="text-purple-200 text-sm">Belum ada data sesi instalasi.</p>
        @endforelse
    </div>

    <div id="loading-overlay" class="hidden fixed inset-0 bg-slate-900/80 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="text-center">
            <div class="w-16 h-16 mx-auto mb-4 border-4 border-purple-500 border-t-transparent rounded-full animate-spin"></div>
            <p class="text-white text-lg font-semibold">Memproses Instalasi...</p>
            <p class="text-purple-200 text-sm mt-2">Mohon tunggu, jangan tutup halaman ini.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('installer.run') }}" id="install-form" class="flex gap-4">
        @csrf
        <a href="{{ route('installer.smtp') }}"
           class="flex-1 py-3 rounded-lg bg-white/10 hover:bg-white/20 text-white text-center transition">
            Kembali
        </a>
        <button type="submit" id="install-btn"
                @disabled(empty($data['db_database']) || empty($data['admin_email']) || empty($data['school_name']))
                class="flex-1 py-3 rounded-lg bg-green-600 hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-semibold transition">
            Jalankan Instalasi
        </button>
    </form>

    @push('scripts')
    <script>
        document.getElementById('install-form').addEventListener('submit', function() {
            document.getElementById('loading-overlay').classList.remove('hidden');
            document.getElementById('install-btn').disabled = true;
            document.getElementById('install-btn').textContent = 'Memproses...';
        });
    </script>
    @endpush
@endsection
