@php
    $totalSales = $total_transaksi->sum('total_bayar');
    $totalQty = $data_penjualan->sum('qty');
    $totalPO = $total_po->sum('grand_total');

    // Hitung Profit
    $totalProfit = 0;
    foreach ($data_penjualan as $tt) {
        $totalProfit += ($tt->harga_jual - $tt->harga_modal) * $tt->qty;
    }

    // Hitung Persentase Margin (Laba Bersih / Omset)
    $marginPercentage = $totalSales > 0 ? ($totalProfit / $totalSales) * 100 : 0;
@endphp
<div class="card border-0 shadow-sm overflow-hidden mb-4">
    <div class="card-header bg-white py-3 border-bottom">
        <h6 class="fw-bold mb-0"><i class="bi bi-graph-up-arrow text-primary me-2"></i>Summary Performa Periode</h6>
    </div>
    <div class="card-body bg-light-subtle">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="p-3 bg-white rounded border shadow-sm h-100 border-start border-primary border-4">
                    <small class="text-muted text-uppercase fw-bold d-block mb-1" style="font-size: 10px;">Total Omset
                        (Net)</small>
                    <div class="d-flex align-items-center">
                        <h5 class="fw-bold mb-0 text-dark">Rp {{ number_format($totalSales, 0, ',', '.') }}</h5>
                    </div>
                    <div class="mt-2 text-primary small">
                        <i class="bi bi-receipt me-1"></i> {{ $total_transaksi->count() }} Transaksi
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="p-3 bg-white rounded border shadow-sm h-100 border-start border-success border-4">
                    <small class="text-muted text-uppercase fw-bold d-block mb-1" style="font-size: 10px;">Estimasi
                        Profit</small>
                    <h5 class="fw-bold mb-0 text-success">Rp {{ number_format($totalProfit, 0, ',', '.') }}</h5>
                    <div class="mt-2 small">
                        <span class="badge bg-success-subtle text-success">
                            Margin: {{ number_format($marginPercentage, 1) }}%
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="p-3 bg-white rounded border shadow-sm h-100 border-start border-warning border-4">
                    <small class="text-muted text-uppercase fw-bold d-block mb-1" style="font-size: 10px;">Pembelian
                        Stok (PO)</small>
                    <h5 class="fw-bold mb-0 text-danger">Rp {{ number_format($totalPO, 0, ',', '.') }}</h5>
                    <div class="mt-2 text-muted small">
                        <i class="bi bi-box-seam me-1"></i> {{ $totalQty }} Item Terjual
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer bg-primary text-white border-0 py-2">
        <small style="font-size: 11px;">
            <i class="bi bi-info-circle me-1"></i>
            Analisis: @if ($marginPercentage > 20)
                Performa sangat sehat.
            @else
                Perlu tinjauan margin harga obat.
            @endif
        </small>
    </div>
</div>
<div class="card shadow-sm mb-4">
    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
        <span><i class="bi bi-person-badge me-2"></i>DATA LOG SESI KASIR</span>
        <span class="badge bg-primary">{{ count($log_transaksi_kasir) }} Sesi</span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-sm mb-0" id="tabellogsesikasir">
                <thead>
                    <tr>
                        <th>Waktu Sesi</th>
                        <th>User / Kasir</th>
                        <th class="text-end">Saldo Awal</th>
                        <th class="text-end">Saldo Akhir</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($log_transaksi_kasir as $tt)
                        <tr>
                            <td>{{ date('d/m/Y H:i', strtotime($tt->tgl_sesi_kasir)) }}</td>
                            <td class="fw-bold">{{ $tt->nama }}</td>
                            <td class="text-end text-muted">Rp {{ number_format($tt->saldo_awal, 0, ',', '.') }}</td>
                            <td class="text-end fw-bold">Rp {{ number_format($tt->saldo_akhir, 0, ',', '.') }}</td>
                            <td class="text-center">
                                <span class="badge {{ $tt->status == 'open' ? 'bg-success' : 'bg-secondary' }}">
                                    @if ($tt->status == 1)
                                        Open
                                    @else
                                        Close
                                    @endif
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="row mb-3 text-center mt-3">
            <div class="col-md-6 border-end">
                <span class="stats-label fw-bold">TOTAL SALDO AWAL</span>
                <span class="stats-value text-primary fw-bold">Rp
                    {{ number_format($log_transaksi_kasir->sum('saldo_awal'), 0, ',', '.') }}</span>
            </div>
            <div class="col-md-6">
                <span class="stats-label fw-bold">TOTAL SALDO AKHIR</span>
                <span class="stats-value text-success fw-bold">Rp
                    {{ number_format($log_transaksi_kasir->sum('saldo_akhir'), 0, ',', '.') }}</span>
            </div>
        </div>
    </div>
</div>
<div class="card shadow-sm mb-4 border-start border-primary border-4">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 text-primary fw-bold"><i class="bi bi-cart-check me-2"></i>DATA TRANSAKSI PENJUALAN</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-sm" id="tabeltransaksi">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Invoice</th>
                        <th class="text-end">Subtotal</th>
                        <th class="text-end text-danger">Potongan</th>
                        <th class="text-end fw-bold">Grand Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($total_transaksi as $tt)
                        <tr>
                            <td>{{ date('d/m/Y', strtotime($tt->tgl_transaksi)) }}</td>
                            <td><code class="text-primary">{{ $tt->no_invoice }}</code></td>
                            <td class="text-end">Rp {{ number_format($tt->total_harga, 0, ',', '.') }}</td>
                            <td class="text-end">Rp {{ number_format($tt->diskon, 0, ',', '.') }}</td>
                            <td class="text-end fw-bold">Rp {{ number_format($tt->total_bayar, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="row g-2 mb-4 fw-bold">
            <div class="col-md-4">
                <div class="stats-box bg-primary-subtle border border-primary-subtle">
                    <span class="stats-label text-primary">TOTAL OMSET (GROSS)</span>
                    <span class="stats-value">Rp
                        {{ number_format($total_transaksi->sum('total_harga'), 0, ',', '.') }}</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-box bg-danger-subtle border border-danger-subtle">
                    <span class="stats-label text-danger">TOTAL DISKON</span>
                    <span class="stats-value">Rp
                        {{ number_format($total_transaksi->sum('diskon'), 0, ',', '.') }}</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-box bg-success-subtle border border-success-subtle">
                    <span class="stats-label text-success">NET INCOME (GRAND TOTAL)</span>
                    <span class="stats-value text-success">Rp
                        {{ number_format($total_transaksi->sum('total_bayar'), 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card shadow-sm mb-4">
    <div class="card-header bg-info text-white fw-bold">
        <i class="bi bi-box-seam me-2"></i>DETAIL PENJUALAN PER ITEM
    </div>
    <div class="card-body">
        @php
            $total_qty = $data_penjualan->sum('qty');
            $total_profit = $data_penjualan->sum(function ($item) {
                return $item->grandtotal - $item->harga_modal * $item->qty;
            });
        @endphp
        <div class="table-responsive">
            <table class="table table-sm" id="tabelpenjualan">
                <thead>
                    <tr>
                        <th>Barang</th>
                        <th class="text-center">Qty</th>
                        <th class="text-end">Harga Modal</th>
                        <th class="text-end">Harga Jual</th>
                        <th class="text-end">Margin</th>
                        <th class="text-end">Grand Total Penjualan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data_penjualan as $tt)
                        @php
                            $profit = $tt->harga_jual - $tt->harga_modal;
                            $ttp = $profit / $tt->harga_modal;
                            $ttp2 = $ttp * 100;
                        @endphp
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $tt->nama_dagang }}</div>
                                <small class="text-muted">{{ $tt->no_invoice }}</small>
                            </td>
                            <td class="text-center"><span
                                    class="badge bg-light text-dark border">{{ $tt->qty }}</span></td>
                            <td class="text-end">Rp {{ number_format($tt->harga_modal, 0, ',', '.') }}</td>
                            <td class="text-end">Rp {{ number_format($tt->harga_jual, 0, ',', '.') }}</td>
                            <td class="text-end fw-bold">{{ number_format($ttp2, 0, ',', '.') }} %</td>
                            <td class="text-end fw-bold">Rp {{ number_format($tt->grandtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="row mb-3 fw-bold">
            <div class="col-md-6 border-end">
                <div class="px-3">
                    <span class="stats-label">TOTAL ITEM TERJUAL : </span>
                    <span class="stats-value">{{ number_format($total_qty, 0) }} Unit</span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="px-3">
                    <span class="stats-label">ESTIMASI MARGIN LABA : </span>
                    <span class="stats-value text-info">Rp {{ number_format($total_profit, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card shadow-sm border-0 border-top border-warning border-4">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 text-warning fw-bold"><i class="bi bi-truck me-2"></i>DATA PEMBELIAN STOK (PO)</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-sm" id="tabelpo">
                <thead>
                    <tr>
                        <th>Faktur</th>
                        <th>Supplier</th>
                        <th>Metode Pembayaran</th>
                        <th>Status Pembayaran</th>
                        <th class="text-end">Total PO</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($total_po as $tt)
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $tt->nomor_faktur }}</div>
                                <small
                                    class="text-muted">{{ date('d/m/Y', strtotime($tt->tanggal_pembelian)) }}</small>
                            </td>
                            <td>{{ $tt->nama_supplier }}</td>
                            <td>
                                @if ($tt->status_pembayaran == 1)
                                    <span class="small text-uppercase">Kredit</span>
                                @else
                                    <span class="small text-uppercase">Tunai</span>
                                @endif
                            </td>
                            <td>
                                <span
                                    class="badge {{ $tt->status_bayar == '1' ? 'bg-success' : 'bg-warning text-dark' }}">
                                    @if ($tt->status_bayar == 1)
                                        Lunas
                                    @else
                                        Belum Lunas
                                    @endif
                                </span>
                            </td>
                            <td class="text-end fw-bold">Rp {{ number_format($tt->grand_total, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="stats-box bg-warning-subtle text-center fw-bold mt-2">
            <span class="stats-label text-warning-emphasis">TOTAL PENGELUARAN BELANJA</span>
            <span class="stats-value">Rp {{ number_format($total_po->sum('grand_total'), 0, ',', '.') }}</span>
        </div>
    </div>
</div>
<script>
    $(function() {
        $("#tabellogsesikasir").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "pageLength": 12,
            "searching": true,
            "ordering": false,
        })
    });
    $(function() {
        $("#tabeltransaksi").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "pageLength": 12,
            "searching": true,
            "ordering": false,
        })
    });
    $(function() {
        $("#tabelpenjualan").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "pageLength": 12,
            "searching": true,
            "ordering": false,
        })
    });
    $(function() {
        $("#tabelpo").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "pageLength": 12,
            "searching": true,
            "ordering": false,
        })
    });
</script>
