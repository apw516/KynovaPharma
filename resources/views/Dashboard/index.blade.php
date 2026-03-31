@extends('Template.Main')
@section('container')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h3 class="mb-0 fw-bold text-dark">Dashboard Analitik</h3>
                    <p class="text-muted small mb-0">Selamat datang di Sistem Manajemen KynovaPharma</p>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Dashboard Utama</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid mt-3 mb-3">
            <div class="row g-3">
                {{-- Alert Stok Expired --}}
                @if ($notif_ed > 0)
                    <div class="col-md-6">
                        <div class="alert alert-danger border-0 shadow-sm d-flex align-items-center py-3 mb-0 animate-pulse-red"
                            role="alert">
                            <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                style="width: 45px; height: 45px;">
                                <i class="bi bi-exclamation-octagon-fill fs-4"></i>
                            </div>
                            <div>
                                <h6 class="alert-heading fw-bold mb-1">Perhatian: Sediaan Kadaluwarsa</h6>
                                <p class="mb-0 small">
                                    Terdapat <strong>{{ $notif_ed }} item</strong> yang akan expired dalam waktu dekat (
                                    < 90 hari). <a href="{{ route('indexdatastokpersediaan') }}"
                                        class="fw-bold text-danger text-decoration-underline ms-1">Lihat Detail</a>
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Alert Hutang Gabungan --}}
                @if ($notif_hutang > 0)
                    <div class="col-md-6">
                        <div class="alert {{ $overdue_count > 0 ? 'alert-danger animate-pulse-red' : 'alert-warning animate-pulse-yellow' }} border-0 shadow-sm d-flex align-items-center py-3 mb-0"
                            role="alert">
                            <div class="{{ $overdue_count > 0 ? 'bg-danger' : 'bg-warning' }} text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                style="width: 45px; height: 45px;">
                                <i
                                    class="bi {{ $overdue_count > 0 ? 'bi-exclamation-triangle-fill' : 'bi-cash-stack' }} fs-4"></i>
                            </div>
                            <div>
                                <h6 class="alert-heading fw-bold mb-1">
                                    {{ $overdue_count > 0 ? 'Peringatan: Hutang Menunggak!' : 'Peringatan: Tagihan PO' }}
                                </h6>
                                <p class="mb-0 small">
                                    Terdapat <strong>{{ $notif_hutang }} tagihan</strong> belum lunas.
                                    @if ($overdue_count > 0)
                                        <span class="badge bg-white text-danger ms-1">{{ $overdue_count }} Lewat Jatuh
                                            Tempo</span>
                                    @endif
                                    <a href="{{ route('indexpurchaseorder') }}"
                                        class="fw-bold text-decoration-underline ms-1 text-dark">Selesaikan Pembayaran</a>
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <style>
            @keyframes pulse-red {
                0% {
                    transform: scale(1);
                    box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
                }

                70% {
                    transform: scale(1.01);
                    box-shadow: 0 0 0 10px rgba(220, 53, 69, 0);
                }

                100% {
                    transform: scale(1);
                    box-shadow: 0 0 0 0 rgba(220, 53, 69, 0);
                }
            }

            @keyframes pulse-yellow {
                0% {
                    transform: scale(1);
                    box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.7);
                }

                70% {
                    transform: scale(1.01);
                    box-shadow: 0 0 0 10px rgba(255, 193, 7, 0);
                }

                100% {
                    transform: scale(1);
                    box-shadow: 0 0 0 0 rgba(255, 193, 7, 0);
                }
            }

            .animate-pulse-red {
                animation: pulse-red 2s infinite;
            }

            .animate-pulse-yellow {
                animation: pulse-yellow 2s infinite;
            }
        </style>
        <div class="container-fluid">
            <a href="{{ route('dashboard.cetak') }}" target="_blank"
                class="btn btn-danger btn-sm shadow-sm rounded-pill px-3 mb-2">
                <i class="bi bi-file-earmark-pdf-fill me-1"></i> Cetak Laporan PDF
            </a>
            <div class="row mb-4">
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box shadow-sm border-0 rounded-3">
                        <span class="info-box-icon bg-primary-subtle text-primary shadow-sm"><i
                                class="bi bi-cart-check-fill"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text text-muted small">Transaksi Hari Ini</span>
                            <span class="info-box-number fw-bold fs-5">24</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box shadow-sm border-0 rounded-3">
                        <span class="info-box-icon bg-danger-subtle text-danger shadow-sm"><i
                                class="bi bi-box-seam-fill"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text text-muted small">Stok Hampir Habis</span>
                            <span class="info-box-number fw-bold fs-5 text-danger">{{ count($stokKritis) }} Item</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box shadow-sm border-0 rounded-3">
                        <span class="info-box-icon bg-warning-subtle text-warning shadow-sm"><i
                                class="bi bi-calendar-x-fill"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text text-muted small">Hampir Expired</span>
                            <span class="info-box-number fw-bold fs-5 text-warning">{{ count($dataED) }} Batch</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box shadow-sm border-0 rounded-3">
                        <span class="info-box-icon bg-success-subtle text-success shadow-sm"><i
                                class="bi bi-currency-dollar"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text text-muted small">Omzet Bulan Ini</span>
                            <span class="info-box-number fw-bold fs-5">
                               @if(count($totals) > 0 ) Rp
                                {{ number_format((float) $totals[0], 0, ',', '.') }} @else 0 @endif</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm mb-4 overflow-hidden">
                        <div class="card-header bg-white border-0 py-3">
                            <h6 class="fw-bold text-dark mb-0"><i
                                    class="bi bi-shield-exclamation text-danger me-2"></i>Kontrol Kadaluwarsa</h6>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" id="tableED">
                                <thead class="bg-light text-muted extra-small text-uppercase">
                                    <tr>
                                        <th class="ps-4">Produk & Batch</th>
                                        <th class="text-center">Tanggal ED</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-end pe-4">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($dataED as $ed)
                                        <tr>
                                            <td class="ps-4">
                                                <div class="fw-bold small">{{ $ed->nama_dagang }}</div>
                                                <div class="extra-small text-muted">BN: {{ $ed->kode_batch }}</div>
                                            </td>
                                            <td class="text-center small">{{ date('d/m/Y', strtotime($ed->tgl_expired)) }}
                                            </td>
                                            <td class="text-center">
                                                <span
                                                    class="badge bg-{{ $ed->sisa_hari <= 90 ? 'warning' : 'info' }}-subtle text-{{ $ed->sisa_hari <= 90 ? 'warning' : 'info' }} rounded-pill border border-{{ $ed->sisa_hari <= 90 ? 'warning' : 'info' }} border-opacity-25 extra-small">
                                                    {{ $ed->sisa_hari <= 90 ? 'Kritis' : 'Waspada' }}
                                                </span>
                                            </td>
                                            <td class="text-end pe-4">
                                                <a type="button" href="{{ route('indexdatastokpersediaan') }}"
                                                    class="btn btn-sm btn-light border-0"><i
                                                        class="bi bi-arrow-right"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm mb-4 overflow-hidden">
                        <div class="card-header bg-white border-0 py-3">
                            <h6 class="fw-bold text-dark mb-0"><i
                                    class="bi bi-box-arrow-in-down text-primary me-2"></i>Perlu Re-Order</h6>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle mb-0" id="tableStok">
                                <thead class="bg-light text-muted extra-small text-uppercase">
                                    <tr>
                                        <th class="ps-4">Produk</th>
                                        <th class="text-center">Sisa</th>
                                        <th class="text-end pe-4">Saran</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($stokKritis as $s)
                                        <tr>
                                            <td class="ps-4 small fw-bold">{{ $s->nama_dagang }}</td>
                                            <td class="text-center small text-danger fw-bold">{{ $s->stok_sekarang }}</td>
                                            <td class="text-end pe-4">
                                                <span
                                                    class="badge bg-primary px-2">+{{ ceil($s->kecepatan_harian * 7) }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-0 py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="fw-bold text-dark mb-0">
                                    <i class="bi bi-graph-up-arrow text-success me-2"></i>Tren Penjualan (7 Hari Terakhir)
                                </h6>
                                <span class="badge bg-success-subtle text-success px-3">Live Update</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="salesChart"
                                style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm mb-4 h-100">
                        <div class="card-header bg-white border-0 py-3">
                            <h6 class="fw-bold text-dark mb-0"><i class="bi bi-pie-chart text-info me-2"></i>Top Kategori
                            </h6>
                        </div>
                        <div class="card-body d-flex flex-column align-items-center justify-content-center">
                            <canvas id="categoryChart" style="max-height: 250px;"></canvas>
                            <div id="chartLegend" class="mt-3 w-100"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="v_tabel_analisis">

                </div>
            </div>
        </div>
    </div>

    <style>
        .extra-small {
            font-size: 0.7rem;
        }

        .info-box {
            display: flex;
            align-items: center;
            background: #fff;
            padding: 1rem;
        }

        .info-box-icon {
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            font-size: 1.5rem;
        }

        .info-box-content {
            padding-left: 1rem;
        }
    </style>
    {{-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> --}}

    <script>
        const ctx = document.getElementById('salesChart').getContext('2d');

        // Gradient Effect
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(13, 110, 253, 0.2)');
        gradient.addColorStop(1, 'rgba(13, 110, 253, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($labels) !!},
                datasets: [{
                    label: 'Omzet Penjualan',
                    data: {!! json_encode($totals) !!},
                    borderColor: '#0d6efd',
                    backgroundColor: gradient,
                    fill: true,
                    borderWidth: 3,
                    tension: 0.4, // Membuat garis melengkung (smooth)
                    pointRadius: 4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#0d6efd',
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
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

        // --- Script untuk Pie Chart Kategori ---
        const ctxKat = document.getElementById('categoryChart').getContext('2d');
        new Chart(ctxKat, {
            type: 'doughnut', // Menggunakan Doughnut agar lebih modern
            data: {
                labels: {!! json_encode($katLabels) !!},
                datasets: [{
                    data: {!! json_encode($katTotals) !!},
                    backgroundColor: [
                        '#0d6efd', '#6610f2', '#6f42c1', '#d63384', '#fd7e14'
                    ],
                    hoverOffset: 10,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: {
                                size: 11
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                let value = context.raw || 0;
                                return label + ': Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                cutout: '70%' // Membuat lubang di tengah lebih besar agar terlihat clean
            }
        });
    </script>
    <script>
        $(document).ready(function() {
            tampilkan_tabel_analisis()
        })

        function tampilkan_tabel_analisis() {
            tanggal = $('#tanggalmulai').val()
            spinner_on()
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    _token: "{{ csrf_token() }}",
                    tanggal
                },
                url: '<?= route('ambildatatabelanalisisprodukfastmoving') ?>',
                error: function(data) {
                    spinner_off()
                    Swal.fire({
                        icon: 'error',
                        title: 'Ooops....',
                        text: 'Sepertinya ada masalah......',
                        footer: ''
                    })
                },
                success: function(data) {
                    spinner_off()
                    if (data.code == 500) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oopss...',
                            text: data.message,
                            footer: ''
                        })
                    } else {
                        $('.v_tabel_analisis').html(data.view);
                    }
                }
            });
        }

        function tampilkan_tabel_barang_hampir_habis() {
            tanggal = $('#tanggalmulai').val()
            spinner_on()
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    _token: "{{ csrf_token() }}",
                    tanggal
                },
                url: '<?= route('ambildatabaranghampirhabis') ?>',
                error: function(data) {
                    spinner_off()
                    Swal.fire({
                        icon: 'error',
                        title: 'Ooops....',
                        text: 'Sepertinya ada masalah......',
                        footer: ''
                    })
                },
                success: function(data) {
                    spinner_off()
                    if (data.code == 500) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oopss...',
                            text: data.message,
                            footer: ''
                        })
                    } else {
                        $('.v_tabel_barang_hampir_habis').html(data.view);
                    }
                }
            });
        }

        function tampilkan_tabel_barang_hampir_ed() {
            tanggal = $('#tanggalmulai').val()
            spinner_on()
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    _token: "{{ csrf_token() }}",
                    tanggal
                },
                url: '<?= route('ambildatabaranghampired') ?>',
                error: function(data) {
                    spinner_off()
                    Swal.fire({
                        icon: 'error',
                        title: 'Ooops....',
                        text: 'Sepertinya ada masalah......',
                        footer: ''
                    })
                },
                success: function(data) {
                    spinner_off()
                    if (data.code == 500) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oopss...',
                            text: data.message,
                            footer: ''
                        })
                    } else {
                        $('.v_tabel_barang_expired').html(data.view);
                    }
                }
            });
        }
    </script>
@endsection
