@extends('Template.Main')
@section('container')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h3 class="mb-0 fw-bold text-dark">Dashboard Keuangan</h3>
                    <p class="text-muted small mb-0">Laporan real-time arus kas dan profitabilitas</p>
                </div>
                <div class="col-sm-6 text-end">
                    <div class="btn-group shadow-sm">
                        <button class="btn btn-white border btn-sm"><i class="bi bi-calendar3 me-2"></i>Bulan Ini</button>
                        <a href="{{ route('dashboard.cetak') }}" class="btn btn-danger btn-sm px-3">
                            <i class="bi bi-file-earmark-pdf me-1"></i> Cetak Laporan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="app-content">
        <div class="container-fluid">
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm bg-primary text-white">
                        <div class="card-body">
                            <small class="text-white-50 d-block mb-1 text-uppercase fw-bold">Total Penjualan</small>
                            <h4 class="fw-bold mb-0">Rp {{ number_format($totalSales, 0, ',', '.') }}</h4>

                            <div class="mt-2 small">
                                @if ($salesTrend >= 0)
                                    <span class="badge bg-success-subtle text-success p-1 rounded">
                                        <i class="bi bi-arrow-up-short"></i> {{ number_format($salesTrend, 1) }}%
                                    </span>
                                    <span class="text-white-50 ms-1">naik dari bulan lalu</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger p-1 rounded">
                                        <i class="bi bi-arrow-down-short"></i> {{ number_format(abs($salesTrend), 1) }}%
                                    </span>
                                    <span class="text-white-50 ms-1">turun dari bulan lalu</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm bg-white">
                        <div class="card-body">
                            <small class="text-muted d-block mb-1 text-uppercase fw-bold">Laba Kotor (Estimasi)</small>
                            <h4 class="fw-bold mb-0 text-success">Rp {{ number_format($grossProfit, 0, ',', '.') }}</h4>
                            <div class="mt-2 small text-muted">Margin rata - rata: <span
                                    class="fw-bold">{{ number_format($marginPercentage, 0, ',', '.') }}%</span></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm bg-white">
                        <div class="card-body">
                            <small class="text-muted d-block mb-1 text-uppercase fw-bold">Hutang Jatuh Tempo</small>
                            <h4 class="fw-bold mb-0 text-danger">Rp {{ number_format($accountPayable, 0, ',', '.') }}</h4>
                            <div class="mt-2 small text-danger fw-bold"><i class="bi bi-exclamation-circle me-1"></i>
                                {{ count($pendingPayments) }} PBF
                                Perlu Bayar</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm bg-success text-white">
                        <div class="card-body">
                            <small class="text-white-50 d-block mb-1 text-uppercase fw-bold">Saldo Kas/Bank</small>
                            <h4 class="fw-bold mb-0 text-warning">Rp {{ number_format($cashBalance, 0, ',', '.') }}</h4>
                            <i class="bi bi-arrow-repeat me-1"></i> Sinkronisasi: {{ now()->format('H:i') }} WIB
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between">
                            <h6 class="fw-bold text-dark mb-0">Arus Kas Mingguan</h6>
                            <div class="extra-small text-muted">
                                <span class="me-2"><i class="bi bi-circle-fill text-primary small"></i> Masuk</span>
                                <span><i class="bi bi-circle-fill text-light-emphasis small"></i> Keluar</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="cashflowChart" style="height: 280px;"></canvas>
                        </div>
                    </div>
                </div>
                {{-- <div class="col-lg-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white border-0 py-3">
                            <h6 class="fw-bold text-dark mb-0">Alokasi Biaya</h6>
                        </div>
                        <div class="card-body text-center">
                            <canvas id="expenseDonut" style="max-height: 200px;"></canvas>
                            <ul class="list-group list-group-flush mt-3 text-start small">
                                <li class="list-group-item d-flex justify-content-between px-0 py-1 border-0">
                                    <span><i class="bi bi-dot text-primary fs-5"></i> Kulakan Obat</span>
                                    <span class="fw-bold">70%</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between px-0 py-1 border-0">
                                    <span><i class="bi bi-dot text-warning fs-5"></i> Operasional</span>
                                    <span class="fw-bold">20%</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between px-0 py-1 border-0">
                                    <span><i class="bi bi-dot text-danger fs-5"></i> Gaji Karyawan</span>
                                    <span class="fw-bold">10%</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div> --}}
            </div>
            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold text-dark mb-0"><i class="bi bi-clock-history text-warning me-2"></i>Jatuh Tempo
                        Pembayaran PBF</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted extra-small text-uppercase">
                            <tr>
                                <th class="ps-4">Nama Supplier (PBF)</th>
                                <th>No. Faktur</th>
                                <th class="text-center">Tgl Jatuh Tempo</th>
                                <th class="text-end">Jumlah Tagihan</th>
                                <th class="text-center">Urgensi</th>
                                <th class="text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pendingPayments as $pay)
                                <tr>
                                    <td class="ps-4 fw-bold text-dark">{{ $pay->nama_pbf }}</td>
                                    <td class="small text-muted">{{ $pay->nomor_faktur }}</td>
                                    <td class="text-center small">{{ date('d M Y', strtotime($pay->jatuh_tempo)) }}</td>
                                    <td class="text-end fw-bold">Rp {{ number_format($pay->total, 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        @php
                                            $daysLeft = now()->diffInDays($pay->jatuh_tempo, false);
                                            $badgeColor = $daysLeft <= 3 ? 'danger' : 'warning';
                                        @endphp
                                        <span
                                            class="badge bg-{{ $badgeColor }}-subtle text-{{ $badgeColor }} px-3 rounded-pill"
                                            style="font-size: 10px;">
                                            {{ $daysLeft <= 0 ? 'Jatuh Tempo' : $daysLeft . ' Hari Lagi' }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <button class="btn btn-sm btn-outline-primary border-0"><i
                                                class="bi bi-wallet2 me-1"></i> Bayar</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script>
        const ctxCash = document.getElementById('cashflowChart').getContext('2d');

        // Helper untuk format rupiah simpel
        const formatRupiah = (val) => {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(val);
        };

        new Chart(ctxCash, {
            type: 'bar',
            data: {
                labels: {!! json_encode($labels) !!},
                datasets: [{
                    label: 'Uang Masuk',
                    data: {!! json_encode($dataMasuk) !!},
                    backgroundColor: '#0d6efd',
                    borderRadius: 5,
                }, {
                    label: 'Uang Keluar',
                    data: {!! json_encode($dataKeluar) !!},
                    backgroundColor: '#e9ecef',
                    borderRadius: 5,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                let value = context.raw || 0;
                                // Menggunakan helper formatRupiah untuk tooltip
                                return label + ': ' + formatRupiah(value);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            display: false
                        },
                        ticks: {
                            callback: function(value) {
                                // Format singkat untuk sumbu Y agar tidak terlalu panjang
                                if (value >= 1000000) return 'Rp ' + (value / 1000000).toLocaleString('id-ID') +
                                    'M';
                                if (value >= 1000) return 'Rp ' + (value / 1000).toLocaleString('id-ID') + 'K';
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    </script>
@endsection
