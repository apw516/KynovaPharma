<div class="card border-0 shadow-sm overflow-hidden">
    <div class="card-header bg-white border-0 py-3">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h6 class="fw-bold text-dark mb-1">
                    <i class="bi bi-graph-up-arrow text-success me-2"></i>Analisis Produk Fast Moving
                </h6>
                <p class="text-muted small mb-0">Produk dengan rotasi stok tercepat dalam 30 hari terakhir</p>
            </div>
            <div class="col-md-6 text-md-end mt-2 mt-md-0">
                <div id="btn-export-container"></div>
            </div>
        </div>
    </div>

    <div class="row g-0 border-top border-bottom bg-light-subtle">
        <div class="col-6 col-md-3 border-end p-3">
            <small class="text-muted d-block text-uppercase extra-small fw-bold">Total Item</small>
            <span class="fw-bold text-dark">{{ count($data) }} Produk</span>
        </div>
        <div class="col-6 col-md-3 border-end p-3">
            <small class="text-muted d-block text-uppercase extra-small fw-bold">Perlu Re-Order</small>
            <span class="fw-bold text-danger">{{ $data->where('stok_aktual', '<=', 'safety_stock')->count() }}
                Item</span>
        </div>
        <div class="col-6 col-md-3 border-end p-3">
            <small class="text-muted d-block text-uppercase extra-small fw-bold">Periode Analisis</small>
            <span class="fw-bold text-primary">30 Hari Terakhir</span>
        </div>
        <div class="col-6 col-md-3 p-3">
            <small class="text-muted d-block text-uppercase extra-small fw-bold">Update Terakhir</small>
            <span class="fw-bold text-dark">{{ date('H:i') }} WIB</span>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="tabelFastMoving" style="width:100%">
                <thead class="bg-light text-muted extra-small text-uppercase">
                    <tr>
                        <th class="ps-4 border-0">Informasi Produk</th>
                        <th class="text-center border-0">Kategori</th>
                        <th class="text-center border-0">Terjual (30d)</th>
                        <th class="text-center border-0">Visual Stok</th>
                        <th class="text-center border-0">Status Keamanan</th>
                        <th class="text-end pe-4 border-0">Rekomendasi</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @foreach ($data as $D)
                        @php
                            $stok = $D->stok_aktual ?? 0;
                            $kecepatan = $D->kecepatan_harian ?? 0;
                            $sisa_hari = $kecepatan > 0 ? floor($stok / $kecepatan) : ($stok > 0 ? 999 : 0);

                            $warna_bar =
                                $sisa_hari <= 3 ? 'bg-danger' : ($sisa_hari <= 7 ? 'bg-warning' : 'bg-success');
                            $isCritical = $D->stok_aktual <= $D->safety_stock;
                        @endphp
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm me-3 bg-primary-subtle text-primary d-flex align-items-center justify-content-center rounded-2 fw-bold"
                                        style="width: 35px; height: 35px; font-size: 12px;">
                                        {{ substr($D->nama_dagang, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark mb-0" style="font-size: 0.9rem;">
                                            {{ $D->nama_dagang }}</div>
                                        <code class="extra-small text-muted">{{ $D->kode_barang }}</code>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span
                                    class="badge bg-white text-dark border fw-normal px-2 py-1">{{ $D->nama_obat }}</span>
                            </td>
                            <td class="text-center fw-bold">
                                {{ number_format($D->total_terjual, 0, ',', '.') }}
                                <small class="text-muted fw-normal">{{ $D->satuan_kecil }}</small>
                            </td>
                            <td class="text-center" style="min-width: 140px;">
                                <div class="d-flex align-items-center justify-content-center">
                                    <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                        <div class="progress-bar {{ $warna_bar }} rounded-pill"
                                            style="width: {{ $stok > 100 ? 100 : $stok }}%"></div>
                                    </div>
                                    <span
                                        class="small fw-bold {{ $sisa_hari <= 3 ? 'text-danger' : '' }}">{{ number_format($stok, 0) }}</span>
                                </div>
                                <div class="extra-small text-muted mt-1">Est:
                                    {{ $sisa_hari > 30 ? '>30' : $sisa_hari }} hari lagi</div>
                            </td>
                            <td class="text-center">
                                @if ($isCritical)
                                    <span
                                        class="badge bg-danger-subtle text-danger px-2 py-2 rounded-pill border border-danger border-opacity-25 w-100">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i> Re-Order
                                    </span>
                                @else
                                    <span
                                        class="badge bg-success-subtle text-success px-2 py-2 rounded-pill border border-success border-opacity-25 w-100">
                                        <i class="bi bi-check-circle-fill me-1"></i> Aman
                                    </span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                @if ($isCritical)
                                    <button class="btn btn-sm btn-primary shadow-sm px-3 rounded-pill"
                                        style="font-size: 11px;">
                                        Beli {{ ceil($D->kecepatan_harian * 7) }} {{ $D->satuan_kecil }}
                                    </button>
                                @else
                                    <span class="text-muted small">--</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white border-0 py-3">
        <small class="text-muted"><i class="bi bi-info-circle me-1"></i> Data diperbarui berdasarkan transaksi real-time
            POS.</small>
    </div>
</div>

<style>
    .extra-small {
        font-size: 0.7rem;
    }

    #tabelFastMoving tbody tr {
        transition: all 0.2s ease;
    }

    #tabelFastMoving tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.015);
    }

    .bg-light-subtle {
        background-color: #fcfcfc;
    }
</style>
<script>
    $(function() {
        var table = $("#tabelFastMoving").DataTable({
            "responsive": true,
            "pageLength": 10,
            "order": [
                [2, "desc"]
            ],
            // Modifikasi DOM agar filter dan button terpisah rapi
            "dom": "<'row px-4 py-3'<'col-md-6'f><'col-md-6 text-end'B>>" +
                "<'row'<'col-md-12'tr>>" +
                "<'row px-4 py-3'<'col-md-5'i><'col-md-7'p>>",
            "buttons": [{
                    extend: 'excelHtml5',
                    text: '<i class="bi bi-file-earmark-excel"></i>',
                    className: 'btn btn-outline-success btn-sm border shadow-none mx-1',
                    titleAttr: 'Export ke Excel'
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="bi bi-file-earmark-pdf"></i>',
                    className: 'btn btn-outline-danger btn-sm border shadow-none mx-1',
                    titleAttr: 'Export ke PDF',
                    orientation: 'landscape'
                },
                {
                    extend: 'print',
                    text: '<i class="bi bi-printer"></i>',
                    className: 'btn btn-outline-dark btn-sm border shadow-none mx-1',
                    titleAttr: 'Cetak Laporan'
                }
            ],
            "language": {
                "search": "",
                "searchPlaceholder": "Cari produk...",
            }
        });

        // Pindahkan tombol ke container kustom jika diperlukan, 
        // atau biarkan di DOM default (Bootstrap 5 grid).
        $('.dataTables_filter input').removeClass('form-control-sm').addClass(
            'form-control border-0 bg-light px-3 py-2 rounded-pill');
    });
</script>
