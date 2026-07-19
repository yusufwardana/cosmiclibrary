@extends('layouts.app')

@section('title', 'Analitik & Laporan')

@section('content')
<div class="library-header">
    <h1>📊 Analitik & Laporan</h1>
</div>

<!-- Summary Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Total Buku</div>
        <div class="stat-value">{{ number_format($summary['books']) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total Anggota</div>
        <div class="stat-value">{{ number_format($summary['members']) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Peminjaman Aktif</div>
        <div class="stat-value">{{ number_format($summary['active_borrows']) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Keterlambatan</div>
        <div class="stat-value" style="color: var(--danger)">{{ number_format($summary['overdue']) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total Denda</div>
        <div class="stat-value">Rp {{ number_format($summary['fines_total'], 0, ',', '.') }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Denda Belum Dibayar</div>
        <div class="stat-value" style="color: var(--warning)">Rp {{ number_format($summary['fines_unpaid'], 0, ',', '.') }}</div>
    </div>
</div>

<!-- Charts Grid -->
<div class="charts-grid">
    <div class="card">
        <h2 class="card-title">📈 Tren Peminjaman (12 Bulan)</h2>
        <canvas id="borrowsChart" height="100"></canvas>
    </div>
    
    <div class="card">
        <h2 class="card-title">💰 Denda Terkumpul (12 Bulan)</h2>
        <canvas id="finesChart" height="100"></canvas>
    </div>
</div>

<div class="charts-grid">
    <div class="card">
        <h2 class="card-title">📚 Buku Terpopuler</h2>
        <canvas id="popularBooksChart"></canvas>
    </div>
    
    <div class="card">
        <h2 class="card-title">🏷️ Peminjaman per Kategori</h2>
        <canvas id="categoryChart"></canvas>
    </div>
</div>

<div class="card">
    <h2 class="card-title">⭐ Anggota Paling Aktif</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Nama Anggota</th>
                <th>Total Peminjaman</th>
            </tr>
        </thead>
        <tbody>
            @forelse($activeMembers as $name => $total)
                <tr>
                    <td>{{ $name }}</td>
                    <td>{{ number_format($total) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="text-muted">Belum ada data</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<style>
    .charts-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
        gap: 1rem;
        margin-bottom: 1rem;
    }
    canvas {
        max-height: 300px;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    const chartDefaults = {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: { labels: { color: '#a0a0b0' } }
        },
        scales: {
            x: { ticks: { color: '#a0a0b0' }, grid: { color: '#2a2a4a' } },
            y: { ticks: { color: '#a0a0b0' }, grid: { color: '#2a2a4a' } }
        }
    };

    // Borrows by month
    new Chart(document.getElementById('borrowsChart'), {
        type: 'line',
        data: {
            labels: @json(array_keys($borrowsByMonth)),
            datasets: [{
                label: 'Peminjaman',
                data: @json(array_values($borrowsByMonth)),
                borderColor: '#0ff0b3',
                backgroundColor: 'rgba(15, 240, 179, 0.1)',
                tension: 0.3,
                fill: true
            }]
        },
        options: chartDefaults
    });

    // Fines by month
    new Chart(document.getElementById('finesChart'), {
        type: 'bar',
        data: {
            labels: @json(array_keys($finesByMonth)),
            datasets: [{
                label: 'Denda (Rp)',
                data: @json(array_values($finesByMonth)),
                backgroundColor: 'rgba(255, 165, 2, 0.6)',
                borderColor: '#ffa502',
                borderWidth: 1
            }]
        },
        options: chartDefaults
    });

    // Popular books
    new Chart(document.getElementById('popularBooksChart'), {
        type: 'bar',
        data: {
            labels: @json(array_keys($popularBooks)),
            datasets: [{
                label: 'Peminjaman',
                data: @json(array_values($popularBooks)),
                backgroundColor: 'rgba(15, 240, 179, 0.6)',
                borderColor: '#0ff0b3',
                borderWidth: 1
            }]
        },
        options: {
            ...chartDefaults,
            indexAxis: 'y'
        }
    });

    // Category chart
    new Chart(document.getElementById('categoryChart'), {
        type: 'doughnut',
        data: {
            labels: @json(array_keys($borrowsByCategory)),
            datasets: [{
                data: @json(array_values($borrowsByCategory)),
                backgroundColor: [
                    '#0ff0b3', '#ffa502', '#ff4757', '#3742fa', 
                    '#2ed573', '#ff6b81', '#70a1ff', '#eccc68'
                ],
                borderColor: '#1a1a2e',
                borderWidth: 2
            }]
        },
        options: {
            plugins: { legend: { position: 'right', labels: { color: '#a0a0b0' } } }
        }
    });
</script>
@endsection