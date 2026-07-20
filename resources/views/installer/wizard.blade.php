<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Instalasi CosmicLib</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 min-h-screen">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-2xl">
            @if(request()->query('installed') === '1')
                <div class="bg-green-500/20 border border-green-500/50 rounded-xl p-6 text-center">
                    <div class="w-16 h-16 mx-auto mb-4 bg-green-500 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-white mb-2">Instalasi Berhasil!</h2>
                    <p class="text-green-100 mb-6">CosmicLib telah berhasil diinstal. Silakan login untuk mulai menggunakan sistem.</p>
                    <a href="{{ url('/login') }}"
                       class="inline-block px-8 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition">
                        Login Sekarang
                    </a>
                </div>
            @else
                <div class="bg-white/5 backdrop-blur-sm rounded-xl border border-white/10 p-6 mb-4">
                    <div class="flex items-center justify-between">
                        @php
                            $steps = [
                                'welcome' => 'Mulai',
                                'license' => 'Lisensi',
                                'requirements' => 'Sistem',
                                'database' => 'Database',
                                'admin' => 'Admin',
                                'school' => 'Sekolah',
                                'smtp' => 'Email',
                                'confirm' => 'Konfirmasi',
                            ];
                            $currentRoute = request()->route()->getName() ?? 'installer.welcome';
                            $currentStep = str_replace('installer.', '', $currentRoute);
                            $currentStep = explode('.', $currentStep)[0];
                            $stepKeys = array_keys($steps);
                            $currentIndex = array_search($currentStep, $stepKeys, true);
                            if ($currentIndex === false) {
                                $currentIndex = 0;
                            }
                        @endphp
                        @foreach($steps as $key => $label)
                            @php $index = array_search($key, $stepKeys, true); @endphp
                            <div class="flex items-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold
                                        {{ $index < $currentIndex ? 'bg-green-500 text-white' : ($index === $currentIndex ? 'bg-purple-600 text-white' : 'bg-white/10 text-white/50') }}">
                                        @if($index < $currentIndex)
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        @else
                                            {{ $index + 1 }}
                                        @endif
                                    </div>
                                    <span class="text-xs mt-1 {{ $index === $currentIndex ? 'text-white' : 'text-white/50' }}">{{ $label }}</span>
                                </div>
                                @if($index < count($steps) - 1)
                                    <div class="w-8 h-0.5 mx-2 {{ $index < $currentIndex ? 'bg-green-500' : 'bg-white/10' }}"></div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                @if(session('error'))
                    <div class="bg-red-500/20 border border-red-500/50 rounded-lg p-4 mb-4">
                        <p class="text-red-100 text-sm">{{ session('error') }}</p>
                    </div>
                @endif

                @yield('installer-content')
            @endif
        </div>
    </div>

    @stack('scripts')
</body>
</html>