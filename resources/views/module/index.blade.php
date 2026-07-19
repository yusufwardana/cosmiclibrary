<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Modul - CosmicLib</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="bg-dark text-light p-4">
    <div class="container">
        <h1 class="mb-4">Manajemen Modul</h1>

        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <div class="row">
            @foreach ($modules as $slug => $manifest)
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">{{ $manifest['name'] ?? $slug }}</h5>
                            <p class="card-text">{{ $manifest['description'] ?? '' }}</p>
                            <span class="badge bg-{{ ($manifest['enabled'] ?? false) ? 'success' : 'secondary' }}">
                                {{ ($manifest['enabled'] ?? false) ? 'Aktif' : 'Nonaktif' }}
                            </span>
                            @if ($manifest['enabled'] ?? false)
                                <form method="POST" action="{{ route('module.disable', $slug) }}" class="mt-2">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Nonaktifkan</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('module.enable', $slug) }}" class="mt-2">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-success">Aktifkan</button>
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