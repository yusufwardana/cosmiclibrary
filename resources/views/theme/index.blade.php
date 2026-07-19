<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tema - CosmicLib</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="bg-dark text-light p-4">
    <div class="container">
        <h1 class="mb-4">Manajemen Tema</h1>

        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <div class="row">
            @foreach ($themes as $slug => $manifest)
                <div class="col-md-4 mb-3">
                    <div class="card {{ $slug === $active ? 'border-primary' : '' }}">
                        <div class="card-body">
                            <h5 class="card-title">{{ $manifest['name'] ?? $slug }}</h5>
                            <p class="card-text">{{ $manifest['description'] ?? '' }}</p>
                            <span class="badge bg-{{ $slug === $active ? 'primary' : 'secondary' }}">
                                {{ $slug === $active ? 'Aktif' : 'Tersedia' }}
                            </span>
                            @if ($slug !== $active)
                                <form method="POST" action="{{ route('theme.activate', $slug) }}" class="mt-2">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-primary">Aktifkan</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</body>
</html>