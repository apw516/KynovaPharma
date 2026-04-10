<style>
    /* Menggunakan Font yang lebih modern untuk angka keuangan */
    @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@500&display=swap');

    #tabelriwayatpenjualan thead th {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        padding: 15px 10px;
        border: none;
    }

    #tabelriwayatpenjualan tbody td {
        font-size: 0.85rem;
        padding: 12px 10px;
    }

    .text-end {
        font-family: 'JetBrains Mono', monospace;
        /* Lebih rapi dari Courier */
        font-size: 0.85rem;
    }

    /* Efek hover lembut */
    #tabelriwayatpenjualan tbody tr:hover {
        background-color: rgba(13, 110, 253, 0.04) !important;
        transition: 0.2s ease-in-out;
    }

    /* Memperbaiki tampilan badge qty agar tidak terlalu mencolok */
    .badge.bg-light {
        font-weight: 600;
        color: #444 !important;
        border: 1px solid #ddd !important;
    }
</style>
<table id="tabelriwayatpenjualan" class="table table-hover table-striped align-middle" style="width:100%">
    <thead class="table-dark">
        <tr>
            <th class="text-center">Tgl Transaksi</th>
            <th>No. Invoice</th>
            <th>Detail Barang</th>
            <th class="text-center">Qty Detail</th>
            {{-- <th class="text-end">Harga</th> --}}
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
                        $sisa_qty = $d->qty;
                        $harga_satuan_terkecil = $d->harga_jual; // Harga per 1 Tablet

                        // 1. Hitung Rasio
                        $rasio_ke_bok = $d->rasio_sedang * $d->rasio_kecil; // Misal: 10 * 10 = 100
                        $rasio_ke_strip = $d->rasio_kecil; // Misal: 10

                        // 2. Hitung Harga Satuan per Unit
                        $harga_per_bok = $rasio_ke_bok * $harga_satuan_terkecil;
                        $harga_per_strip = $rasio_ke_strip * $harga_satuan_terkecil;

                        // 3. Logika Pecahan Qty (seperti sebelumnya)
                        $jml_bok = floor($sisa_qty / $rasio_ke_bok);
                        $sisa_setelah_bok = $sisa_qty % $rasio_ke_bok;
                        $jml_strip = floor($sisa_setelah_bok / $rasio_ke_strip);
                        $jml_tablet = $sisa_setelah_bok % $rasio_ke_strip;

                        $result = [];

                        // Tampilan: [Jumlah] [Satuan] (@Harga Satuan Unit)
                        if ($jml_bok > 0) {
                            $result[] =
                                $jml_bok .
                                ' ' .
                                $d->satuan_besar .
                                ' (@Rp ' .
                                number_format($harga_per_bok, 0, ',', '.') .
                                ')';
                        }

                        if ($jml_strip > 0) {
                            $result[] =
                                $jml_strip .
                                ' ' .
                                $d->satuan_sedang .
                                ' (@Rp ' .
                                number_format($harga_per_strip, 0, ',', '.') .
                                ')';
                        }

                        if ($jml_tablet > 0) {
                            $result[] =
                                $jml_tablet .
                                ' ' .
                                $d->satuan_kecil .
                                ' (@Rp ' .
                                number_format($harga_satuan_terkecil, 0, ',', '.') .
                                ')';
                        }
                    @endphp
                    {!! implode(' ', $result) !!}
                </td>
                {{-- <td class="text-end">Rp {{ number_format($d->harga_jual, 0, ',', '.') }}</td> --}}
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
<script>
    $(function() {
        $("#tabelriwayatpenjualan").DataTable({
            "responsive": true,
            "lengthChange": true,
            "autoWidth": false,
            "pageLength": 10,
            "order": [
                [0, "desc"]
            ], // Urutkan transaksi terbaru di atas
            "dom": "<'row mb-3'<'col-md-6'l><'col-md-6 d-flex justify-content-end align-items-center'f B>>" +
                "<'row'<'col-md-12'tr>>" +
                "<'row mt-3'<'col-md-5'i><'col-md-7'p>>",
            "buttons": [{
                    extend: 'excel',
                    text: '<i class="bi bi-file-earmark-excel"></i> Excel',
                    className: 'btn btn-success btn-sm ms-2 shadow-sm'
                },
                {
                    extend: 'pdf',
                    text: '<i class="bi bi-file-earmark-pdf"></i> PDF',
                    className: 'btn btn-danger btn-sm ms-2 shadow-sm'
                },
                {
                    extend: 'print',
                    text: '<i class="bi bi-printer"></i> Cetak',
                    className: 'btn btn-dark btn-sm ms-2 shadow-sm'
                }
            ],
            "language": {
                "search": "",
                "searchPlaceholder": "Cari invoice atau barang...",
                "lengthMenu": "_MENU_ data per halaman",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                "paginate": {
                    "next": '<i class="bi bi-chevron-right"></i>',
                    "previous": '<i class="bi bi-chevron-left"></i>'
                }
            }
        });

        // Percantik Input Search
        $('.dataTables_filter input').addClass('form-control border-0 bg-light px-3 shadow-none');
    });
</script>
