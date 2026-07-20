<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk — CosmicLib</title>
    @vite('resources/css/app.css')
    <style>
        :root { --bg-primary: #0f0f1a; --bg-secondary: #1a1a2e; --bg-card: #16213e; --text-primary: #e0e0e0; --text-secondary: #a0a0b0; --accent: #0ff0b3; --accent-hover: #0cd89e; --danger: #ff4757; --border: #2a2a4a; --radius: 8px; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', -apple-system, sans-serif; background: var(--bg-primary); color: var(--text-primary); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        a { color: var(--accent); text-decoration: none; }
        a:hover { color: var(--accent-hover); }
        .auth-card { background: var(--bg-secondary); border: 1px solid var(--border); border-radius: var(--radius); padding: 2rem; width: 100%; max-width: 400px; margin: 1rem; }
        .auth-card h1 { font-family: 'Space Grotesk', sans-serif; font-size: 1.5rem; margin-bottom: 1.5rem; color: var(--text-primary); }
        .alert { padding: 0.75rem 1rem; border-radius: var(--radius); margin-bottom: 1rem; font-size: 0.875rem; }
        .alert-success { background: rgba(15, 240, 179, 0.15); border: 1px solid var(--accent); color: var(--accent); }
        .alert-danger { background: rgba(255, 71, 87, 0.15); border: 1px solid var(--danger); color: var(--danger); }
        .form-group { margin-bottom: 1rem; }
        label { display: block; margin-bottom: 0.25rem; font-size: 0.875rem; color: var(--text-secondary); }
        input[type="email"], input[type="password"], input[type="text"], input[type="checkbox"] { width: 100%; padding: 0.5rem 0.75rem; background: var(--bg-primary); border: 1px solid var(--border); border-radius: var(--radius); color: var(--text-primary); font-size: 0.875rem; }
        input:focus { outline: none; border-color: var(--accent); }
        .checkbox-group { display: flex; align-items: center; gap: 0.5rem; }
        .checkbox-group input[type="checkbox"] { width: auto; }
        .btn { display: block; width: 100%; padding: 0.5rem 1rem; border-radius: var(--radius); border: 1px solid var(--border); background: var(--bg-card); color: var(--text-primary); font-size: 0.875rem; cursor: pointer; transition: all 0.2s; }
        .btn:hover { border-color: var(--accent); color: var(--accent); }
        .btn-primary { background: var(--accent); color: var(--bg-primary); border-color: var(--accent); font-weight: 600; }
        .btn-primary:hover { background: var(--accent-hover); color: var(--bg-primary); }
        .footer-link { margin-top: 1rem; text-align: center; font-size: 0.875rem; }
    </style>
</head>
<body>
    <div class="auth-card">
        <h1>Masuk</h1>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">{{ $errors->first('email') }}</div>
        @endif

        <form method="POST" action="{{ route('auth.login') }}">
            @csrf
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
            </div>
            <div class="form-group">
                <label for="password">Kata Sandi</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group checkbox-group">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember" style="margin-bottom:0">Ingat Saya</label>
            </div>
            <button type="submit" class="btn btn-primary">Masuk</button>
        </form>

        <div class="footer-link">
            <a href="{{ route('auth.register') }}">Belum punya akun? Daftar</a>
        </div>
    </div>
</body>
</html>
