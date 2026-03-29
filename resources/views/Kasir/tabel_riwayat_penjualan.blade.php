{{-- <table id="tabelriwayatpenjualan" class="table table-sm table-bordered table-hover table-striped">
    <thead>
        <th>Tgl transaksi</th>
        <th>No Invoice</th>
        <th>Nama Barang</th>
        <th>Qty</th>
        <th>Harga</th>
        <th>Subtotal</th>
        <th>Diskon</th>
        <th>Grandtotal</th>
        <th>Status</th>
    </thead>
    <tbody>
        @foreach ($data as $d)
            <tr>
                <td>{{ \Carbon\Carbon::parse($d->tgl_transaksi)->format('d-m-Y') }}</td>
                <td>{{ $d->no_invoice }}</td>
                <td>{{ $d->nama_dagang }} *( 1 {{ $d->satuan_besar }} / {{ $d->rasio_sedang }} {{ $d->satuan_sedang }} /
                    {{ $d->rasio_kecil }} {{ $d->satuan_kecil }})</td>
                <td>
                    @php
                        $sisa = $d->qty;
                        $hasil_detail = [];

                        // 1. Hitung Satuan Besar (misal: Pak)
                        $jml_besar = floor($sisa / ($d->rasio_sedang * $d->rasio_kecil));
                        if ($jml_besar > 0) {
                            $hasil_detail[] = $jml_besar . ' ' . $d->satuan_besar;
                            $sisa %= $d->rasio_sedang * $d->rasio_kecil;
                        }

                        // 2. Hitung Satuan Sedang (misal: Strip)
                        $jml_sedang = floor($sisa / $d->rasio_kecil);
                        if ($jml_sedang > 0) {
                            $hasil_detail[] = $jml_sedang . ' ' . $d->satuan_sedang;
                            $sisa %= $d->rasio_kecil;
                        }

                        // 3. Hitung Satuan Kecil (misal: Tablet/Pcs)
                        if ($sisa > 0) {
                            $hasil_detail[] = $sisa . ' ' . $d->satuan_kecil;
                        }
                    @endphp
                    @if (count($hasil_detail) > 0)
                        {!! implode('<br>', $hasil_detail) !!}
                        <hr class="my-1">
                        <small class="text-muted">(Total: {{ $d->qty }} {{ $d->satuan_kecil }})</small>
                    @else
                        0 {{ $d->satuan_kecil }}
                    @endif
                </td>
                <td>Rp {{ number_format($d->harga_jual, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($d->diskon, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($d->grandtotal, 0, ',', '.') }}</td>
                <td>
                    @if ($d->status_retur == 2)
                        Retur
                        <i class="bi bi-x text-danger"></i>
                    @else
                        OK <i class="bi bi-hand-thumbs-up text-success"></i>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
<script>
    $(function() {
        $("#tabelriwayatpenjualan").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "pageLength": 12,
            "searching": true,
            "ordering": false,
        })
    });
</script> --}}
<style>
    #tabelriwayatpenjualan thead th {
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #dee2e6;
    }

    #tabelriwayatpenjualan tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.05) !important;
        transition: 0.3s;
    }

    .text-end {
        font-family: 'Courier New', Courier, monospace;
        /* Font monospaced agar angka sejajar */
        font-weight: 500;
    }

    .badge {
        font-weight: 500;
    }
</style>
<table id="tabelriwayatpenjualan" class="table table-hover table-striped align-middle" style="width:100%">
    <thead class="table-dark">
        <tr>
            <th class="text-center">Tgl Transaksi</th>
            <th>No. Invoice</th>
            <th>Detail Barang</th>
            <th class="text-center">Qty Detail</th>
            <th class="text-end">Harga</th>
            <th class="text-end">Subtotal</th>
            <th class="text-end">Grandtotal</th>
            <th class="text-center">Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $d)
            <tr>
                <td class="text-center">
                    <span class="d-block fw-bold">{{ \Carbon\Carbon::parse($d->tgl_transaksi)->format('d/m/Y') }}</span>
                    <small class="text-muted">{{ \Carbon\Carbon::parse($d->tgl_transaksi)->format('H:i') }}</small>
                </td>
                <td><code class="fw-bold text-primary">{{ $d->no_invoice }}</code></td>
                <td>
                    <div class="fw-bold">{{ $d->nama_dagang }}</div>
                    <small class="text-muted" style="font-size: 0.75rem;">
                        Rasio: 1 {{ $d->satuan_besar }} ({{ $d->rasio_sedang }} {{ $d->satuan_sedang }} /
                        {{ $d->rasio_kecil }} {{ $d->satuan_kecil }})
                    </small>
                </td>
                <td class="text-center">
                    @php
                        $sisa = $d->qty;
                        $hasil_detail = [];
                        $jml_besar = floor($sisa / ($d->rasio_sedang * $d->rasio_kecil));
                        if ($jml_besar > 0) {
                            $hasil_detail[] = "<span class='badge bg-light text-dark border'>$jml_besar $d->satuan_besar</span>";
                            $sisa %= $d->rasio_sedang * $d->rasio_kecil;
                        }
                        $jml_sedang = floor($sisa / $d->rasio_kecil);
                        if ($jml_sedang > 0) {
                            $hasil_detail[] = "<span class='badge bg-light text-dark border'>$jml_sedang $d->satuan_sedang</span>";
                            $sisa %= $d->rasio_kecil;
                        }
                        if ($sisa > 0) {
                            $hasil_detail[] = "<span class='badge bg-light text-dark border'>$sisa $d->satuan_kecil</span>";
                        }
                    @endphp
                    {!! implode(' ', $hasil_detail) !!}
                </td>
                <td class="text-end">Rp {{ number_format($d->harga_jual, 0, ',', '.') }}</td>
                <td class="text-end">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td>
                <td class="text-end fw-bold text-success">Rp {{ number_format($d->grandtotal, 0, ',', '.') }}</td>
                <td class="text-center">
                    @if ($d->status_retur == 2)
                        <span class="badge rounded-pill bg-danger">
                            <i class="bi bi-arrow-counterclockwise"></i> Retur
                        </span>
                    @else
                        <span class="badge rounded-pill bg-success">
                            <i class="bi bi-check-circle"></i> Berhasil
                        </span>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
