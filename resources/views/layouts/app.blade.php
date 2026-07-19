<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CosmicLib') — CosmicLib</title>
    <style>
        :root {
            --bg-primary: #0f0f1a;
            --bg-secondary: #1a1a2e;
            --bg-card: #16213e;
            --text-primary: #e0e0e0;
            --text-secondary: #a0a0b0;
            --accent: #0ff0b3;
            --accent-hover: #0cd89e;
            --danger: #ff4757;
            --warning: #ffa502;
            --border: #2a2a4a;
            --radius: 8px;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
        }
        a { color: var(--accent); text-decoration: none; }
        a:hover { color: var(--accent-hover); }

        /* Navbar */
        .navbar {
            background: var(--bg-secondary);
            border-bottom: 1px solid var(--border);
            padding: 0.75rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar-brand {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--accent);
        }
        .navbar-nav {
            display: flex;
            gap: 1rem;
            list-style: none;
            align-items: center;
        }
        .navbar-nav a {
            color: var(--text-secondary);
            font-size: 0.875rem;
            padding: 0.5rem 0.75rem;
            border-radius: var(--radius);
            transition: all 0.2s;
        }
        .navbar-nav a:hover, .navbar-nav a.active {
            color: var(--accent);
            background: rgba(15, 240, 179, 0.1);
        }

        /* Layout */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 1.5rem;
        }

        /* Alerts */
        .alert {
            padding: 0.75rem 1rem;
            border-radius: var(--radius);
            margin-bottom: 1rem;
            font-size: 0.875rem;
        }
        .alert-success { background: rgba(15, 240, 179, 0.15); border: 1px solid var(--accent); color: var(--accent); }
        .alert-danger { background: rgba(255, 71, 87, 0.15); border: 1px solid var(--danger); color: var(--danger); }
        .alert-warning { background: rgba(255, 165, 2, 0.15); border: 1px solid var(--warning); color: var(--warning); }

        /* Buttons */
        .btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: var(--radius);
            border: 1px solid var(--border);
            background: var(--bg-card);
            color: var(--text-primary);
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn:hover { border-color: var(--accent); color: var(--accent); }
        .btn-primary { background: var(--accent); color: var(--bg-primary); border-color: var(--accent); font-weight: 600; }
        .btn-primary:hover { background: var(--accent-hover); color: var(--bg-primary); }
        .btn-danger { border-color: var(--danger); color: var(--danger); }
        .btn-danger:hover { background: var(--danger); color: #fff; }
        .btn-sm { padding: 0.25rem 0.5rem; font-size: 0.75rem; }

        /* Tables */
        .table {
            width: 100%;
            border-collapse: collapse;
            background: var(--bg-secondary);
            border-radius: var(--radius);
            overflow: hidden;
        }
        .table th, .table td {
            padding: 0.75rem 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }
        .table th {
            background: var(--bg-card);
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-secondary);
        }
        .table tr:hover { background: rgba(15, 240, 179, 0.03); }

        /* Forms */
        .form-group { margin-bottom: 1rem; }
        .form-label {
            display: block;
            margin-bottom: 0.25rem;
            font-size: 0.875rem;
            color: var(--text-secondary);
        }
        .form-input, .form-select, .form-textarea {
            width: 100%;
            padding: 0.5rem 0.75rem;
            background: var(--bg-primary);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            color: var(--text-primary);
            font-size: 0.875rem;
        }
        .form-input:focus, .form-select:focus, .form-textarea:focus {
            outline: none;
            border-color: var(--accent);
        }
        .form-textarea { min-height: 100px; resize: vertical; }
        .form-error { color: var(--danger); font-size: 0.75rem; margin-top: 0.25rem; }

        /* Cards */
        .card {
            background: var(--bg-secondary);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 1.5rem;
            margin-bottom: 1rem;
        }
        .card-title {
            font-size: 1.125rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--text-primary);
        }

        /* Utility */
        .library-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        .library-header h1 { font-size: 1.5rem; font-weight: 700; }
        .mb-4 { margin-bottom: 1rem; }
        .text-muted { color: var(--text-secondary); }
        .badge {
            display: inline-block;
            padding: 0.15rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        .badge-success { background: rgba(15, 240, 179, 0.2); color: var(--accent); }
        .badge-warning { background: rgba(255, 165, 2, 0.2); color: var(--warning); }
        .badge-danger { background: rgba(255, 71, 87, 0.2); color: var(--danger); }

        /* Stats grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        .stat-card {
            background: var(--bg-secondary);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 1.25rem;
        }
        .stat-card .stat-value {
            font-size: 2rem;
            font-weight: 700;
            font-family: 'JetBrains Mono', monospace;
            color: var(--accent);
        }
        .stat-card .stat-label {
            font-size: 0.75rem;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* Detail list */
        .detail-list dt {
            font-size: 0.75rem;
            color: var(--text-secondary);
            text-transform: uppercase;
            margin-top: 0.75rem;
        }
        .detail-list dd {
            font-size: 1rem;
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="/" class="navbar-brand">🌌 CosmicLib</a>
        <ul class="navbar-nav">
            <li><a href="{{ route('books.index') }}">Buku</a></li>
            <li><a href="{{ route('members.index') }}">Anggota</a></li>
            <li><a href="{{ route('borrows.index') }}">Peminjaman</a></li>
            <li><a href="{{ route('fines.index') }}">Denda</a></li>
            <li><a href="{{ route('reservations.index') }}">Reservasi</a></li>
            <li><a href="{{ route('categories.index') }}">Kategori</a></li>
            <li><a href="{{ route('theme.index') }}">Tema</a></li>
            <li><a href="{{ route('module.index') }}">Modul</a></li>
            @auth
                <li>
                    <form action="{{ route('auth.logout') }}" method="POST" style="display:inline">
                        @csrf
                        <button type="submit" class="btn btn-sm">Keluar</button>
                    </form>
                </li>
            @else
                <li><a href="{{ route('auth.login') }}">Masuk</a></li>
            @endauth
        </ul>
    </nav>

    <div class="container">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if(session('warning'))
            <div class="alert alert-warning">{{ session('warning') }}</div>
        @endif

        @yield('content')
    </div>
</body>
</html>