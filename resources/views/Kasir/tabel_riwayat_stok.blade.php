<table class="table table-sm table-bordered table-hover" id="tabelstok">
    <thead>
        <th>Tanggal Stok</th>
        <th>Nama Barang</th>
        <th>Keterangan</th>
        <th>Stok Awal</th>
        <th>Stok IN</th>
        <th>Stok OUT</th>
        <th>Stok Akhir</th>
    </thead>
    <tbody>
        @foreach ($data as $d)
            @php
                // Fungsi lokal untuk konversi qty ke teks detail
                $konversi = function ($qty, $d) {
                    if ($qty <= 0) {
                        return '0 ' . $d->satuan_kecil;
                    }

                    $sisa = $qty;
                    $res = [];

                    // Satuan Besar
                    $jml_besar = floor($sisa / ($d->rasio_sedang * $d->rasio_kecil));
                    if ($jml_besar > 0) {
                        $res[] = "<strong>$jml_besar</strong> $d->satuan_besar";
                        $sisa %= $d->rasio_sedang * $d->rasio_kecil;
                    }

                    // Satuan Sedang
                    $jml_sedang = floor($sisa / $d->rasio_kecil);
                    if ($jml_sedang > 0) {
                        $res[] = "<strong>$jml_sedang</strong> $d->satuan_sedang";
                        $sisa %= $d->rasio_kecil;
                    }

                    // Satuan Kecil
                    if ($sisa > 0) {
                        $res[] = "<strong>$sisa</strong> $d->satuan_kecil";
                    }

                    return implode('<br>', $res);
                };
            @endphp
            <tr>
                <td class="align-middle text-center">
                    <small>{{ \Carbon\Carbon::parse($d->tgl_input)->format('d/m/Y') }}</small><br>
                    <small class="text-muted">{{ \Carbon\Carbon::parse($d->tgl_input)->format('H:i') }}</small>
                </td>
                <td class="align-middle">
                    <span class="fw-bold text-primary">{{ $d->nama_dagang }}</span><br>
                    <small class="text-muted">{{ $d->nama_obat }} | {{ $d->produsen }}</small>
                </td>
                <td class="align-middle text-center">
                    @php
                        $color = match ($d->keterangan) {
                            'Pembelian' => 'success',
                            'Penjualan' => 'info',
                            'Retur' => 'warning',
                            default => 'secondary',
                        };
                    @endphp
                    <span class="badge bg-{{ $color }}">{{ $d->keterangan }}</span>
                </td>
                <td class="align-middle text-end">{!! $konversi($d->stok_last, $d) !!}</td>

                <td class="align-middle text-end text-success">
                    {!! $d->stok_in > 0 ? $konversi($d->stok_in, $d) : '-' !!}
                </td>

                <td class="align-middle text-end text-danger">
                    {!! $d->stok_out > 0 ? $konversi($d->stok_out, $d) : '-' !!}
                </td>

                <td class="align-middle text-end bg-light fw-bold">
                    {!! $konversi($d->stok_now, $d) !!}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
<style>
    #tabelstok thead th {
        background-color: #f8f9fa;
        text-align: center;
        vertical-align: middle;
        font-size: 0.85rem;
        text-transform: uppercase;
    }

    #tabelstok td {
        font-size: 0.9rem;
        line-height: 1.2;
    }

    .text-end {
        text-align: right;
    }
</style>

<script>
    $(function() {
        $("#tabelstok").DataTable({
            "responsive": true,
            "pageLength": 10,
            "searching": true,
            "ordering": true, // Aktifkan ordering agar user bisa urutkan tgl terbaru
            "order": [
                [0, "desc"]
            ], // Default urutkan dari tanggal terbaru
            "columnDefs": [{
                "className": "dt-center",
                "targets": [0, 2]
            }]
        });
    });
</script>
