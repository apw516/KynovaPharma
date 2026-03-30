{{-- <table class="table table-sm table-bordered table-hover" id="tabelstok">
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
            <tr>
                <td>{{ $d->tgl_input }}</td>
                <td>{{ $d->nama_dagang }} ( {{ $d->nama_obat }} , {{ $d->produsen }} )</td>
                <td>{{ $d->keterangan }}</td>
                <td>{{ number_format($d->stok_last, 0, ',', '.') }}</td>
                <td>{{ number_format($d->stok_in, 0, ',', '.') }}</td>
                <td>{{ number_format($d->stok_out, 0, ',', '.') }}</td>
                <td>{{ number_format($d->stok_now, 0, ',', '.') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
<script>
    $(function() {
        $("#tabelstok").DataTable({
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
    /* Styling Header Tabel */
    #tabelstok thead th {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #5a5c69;
        font-weight: 700;
        border-top: none;
    }

    /* Warna Background Soft untuk Status */
    .bg-success-subtle {
        background-color: #e8f5e9 !important;
    }

    .bg-info-subtle {
        background-color: #e3f2fd !important;
    }

    .bg-warning-subtle {
        background-color: #fff3e0 !important;
    }

    .bg-danger-subtle {
        background-color: #ffebee !important;
    }

    /* Font Angka agar Rapi */
    .text-end {
        font-family: 'Inter', -apple-system, sans-serif;
        line-height: 1.2;
    }

    /* Hover effect */
    #tabelstok tbody tr:hover {
        background-color: rgba(78, 115, 223, 0.03);
        transition: 0.2s;
    }
</style>
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h6 class="m-0 fw-bold text-primary"><i class="bi bi-box-seam me-2"></i> Log Mutasi Stok Barang</h6>
    </div>
    <div class="card-body">
        <table class="table table-hover align-middle" id="tabelstok" style="width:100%">
            <thead class="table-light">
                <tr>
                    <th class="text-center">Waktu</th>
                    <th>Informasi Barang</th>
                    <th class="text-center">Tipe Log</th>
                    <th class="text-end">Awal</th>
                    <th class="text-end text-success">IN</th>
                    <th class="text-end text-danger">OUT</th>
                    <th class="text-end fw-bold">Saldo Akhir</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $d)
                    @php
                        // Fungsi konversi Qty ke Detail Satuan
                        $formatQty = function ($qty, $item) {
                            if ($qty == 0) {
                                return '<span class="text-muted">0</span>';
                            }
                            $sisa = $qty;
                            $detail = [];
                            $besar = floor($sisa / ($item->rasio_sedang * $item->rasio_kecil));
                            if ($besar > 0) {
                                $detail[] = "<strong>$besar</strong> $item->satuan_besar";
                                $sisa %= $item->rasio_sedang * $item->rasio_kecil;
                            }
                            $sedang = floor($sisa / $item->rasio_kecil);
                            if ($sedang > 0) {
                                $detail[] = "<strong>$sedang</strong> $item->satuan_sedang";
                                $sisa %= $item->rasio_kecil;
                            }
                            if ($sisa > 0) {
                                $detail[] = "<strong>$sisa</strong> $item->satuan_kecil";
                            }
                            return implode('<br>', $detail);
                        };

                        // Warna Badge Keterangan
                        $badgeColor = match ($d->keterangan) {
                            'Pembelian' => 'success',
                            'Penjualan' => 'info',
                            'Retur' => 'warning',
                            'Koreksi' => 'danger',
                            default => 'secondary',
                        };
                    @endphp
                    <tr>
                        <td class="text-center">
                            <span
                                class="d-block fw-bold">{{ \Carbon\Carbon::parse($d->tgl_input)->format('d/m/y') }}</span>
                            <small class="text-muted">{{ \Carbon\Carbon::parse($d->tgl_input)->format('H:i') }}</small>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $d->nama_dagang }}</div>
                            <small class="text-muted" style="font-size: 0.7rem;">{{ $d->nama_obat }} |
                                {{ $d->produsen }}</small>
                        </td>
                        <td class="text-center">
                            <span
                                class="badge rounded-pill bg-{{ $badgeColor }}-subtle text-{{ $badgeColor }} border border-{{ $badgeColor }}-subtle px-3">
                                {{ $d->keterangan }}
                            </span>
                        </td>
                        <td class="text-end small">{!! $formatQty($d->stok_last, $d) !!}</td>
                        <td class="text-end text-success fw-semibold">{!! $d->stok_in > 0 ? $formatQty($d->stok_in, $d) : '-' !!}</td>
                        <td class="text-end text-danger fw-semibold">{!! $d->stok_out > 0 ? $formatQty($d->stok_out, $d) : '-' !!}</td>
                        <td class="text-end bg-light-subtle fw-bold">{!! $formatQty($d->stok_now, $d) !!}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<script>
    $(function() {
        var table = $("#tabelstok").DataTable({
            "responsive": true,
            "lengthChange": true,
            "pageLength": 10,
            "order": [
                [0, "desc"]
            ],
            // Layout DOM dengan tombol download di kanan atas
            "dom": "<'row mb-3'<'col-md-4'l><'col-md-8 d-flex justify-content-end align-items-center'f B>>" +
                "<'row'<'col-md-12'tr>>" +
                "<'row mt-3'<'col-md-5'i><'col-md-7'p>>",
            "buttons": [{
                    extend: 'excelHtml5',
                    text: '<i class="bi bi-file-earmark-excel me-1"></i> Excel',
                    className: 'btn btn-success btn-sm border-0 shadow-sm mx-1',
                    title: 'Log_Mutasi_Stok_' + new Date().getTime(),
                    exportOptions: {
                        columns: ':visible',
                        format: {
                            body: function(data, row, column, node) {
                                // Membersihkan tag HTML <br> menjadi spasi agar rapi di Excel
                                return node.innerText || node.textContent;
                            }
                        }
                    }
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="bi bi-file-earmark-pdf me-1"></i> PDF',
                    className: 'btn btn-danger btn-sm border-0 shadow-sm mx-1',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    title: 'Log Mutasi Stok Barang',
                    exportOptions: {
                        columns: ':visible',
                        format: {
                            body: function(data, row, column, node) {
                                return node.innerText || node.textContent;
                            }
                        }
                    }
                },
                {
                    extend: 'print',
                    text: '<i class="bi bi-printer me-1"></i> Cetak',
                    className: 'btn btn-dark btn-sm border-0 shadow-sm mx-1',
                    exportOptions: {
                        columns: ':visible'
                    }
                }
            ],
            "language": {
                "search": "",
                "searchPlaceholder": "Cari mutasi stok...",
                "lengthMenu": "_MENU_",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ log"
            }
        });

        // Percantik input pencarian
        $('.dataTables_filter input').addClass('form-control border-0 bg-light shadow-none px-3 py-2');
    });
</script>
