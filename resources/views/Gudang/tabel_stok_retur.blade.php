<style>
    #tabelstokretur thead th {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #6c757d;
        border: none;
        padding: 15px 10px;
    }

    #tabelstokretur tbody td {
        border-bottom: 1px solid #f8f9fc;
        padding: 12px 10px;
        font-size: 0.85rem;
    }

    /* Warna lembut untuk badge retur */
    .bg-danger-subtle {
        background-color: #fff5f5 !important;
        border: 1px solid #feb2b2;
    }

    /* Efek hover yang halus */
    #tabelstokretur tbody tr:hover {
        background-color: #fffafa;
        transition: all 0.2s ease;
    }

    code {
        font-size: 0.8rem;
    }
</style>
<style>
    /* Membuat tampilan detail qty lebih terstruktur */
    .bg-danger-subtle {
        background-color: #fff5f5 !important;
        min-width: 120px;
    }

    .lh-sm {
        line-height: 1.25 !important;
    }

    /* Memastikan teks tetap rapi meski barisnya banyak */
    #tabelstokretur td {
        vertical-align: top;
        /* Agar sejajar jika detailnya panjang */
    }
</style>
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 fw-bold text-danger"><i class="bi bi-arrow-left-right me-2"></i>Riwayat Retur Pembelian (PO)</h6>
        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-file-earmark-pdf me-1"></i> Cetak Laporan</button>
    </div>
    <div class="card-body">
        <table class="table table-hover align-middle" id="tabelstokretur" style="width:100%">
            <thead class="table-light">
                <tr>
                    <th class="text-center">Tanggal</th>
                    <th>Ref. Transaksi</th>
                    <th>Supplier</th>
                    <th>Item & Batch</th>
                    <th class="text-center">Qty Retur</th>
                    <th>Alasan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $d)
                    <tr>
                        <td class="text-center">
                            <span
                                class="fw-bold d-block">{{ \Carbon\Carbon::parse($d->tgl_retur)->format('d/m/Y') }}</span>
                            <small class="text-muted">{{ \Carbon\Carbon::parse($d->tgl_retur)->format('H:i') }}</small>
                        </td>
                        <td>
                            <div class="small mb-1"><span class="text-muted">ID:</span> <span
                                    class="fw-bold text-dark">{{ $d->kode_retur }}</span></div>
                            <div class="small"><span class="text-muted">Inv:</span> <code
                                    class="text-primary">{{ $d->nomor_faktur }}</code></div>
                        </td>
                        <td>
                            <span class="fw-semibold text-dark">{{ $d->nama_supplier }}</span>
                        </td>
                        <td>
                            <div class="fw-bold">{{ $d->nama_dagang }}</div>
                            <small class="badge bg-light text-secondary border">Batch: {{ $d->no_batch }}</small>
                        </td>
                        <td class="text-center">
                            @php
                                // Logika memecah Qty Retur ke satuan Besar, Sedang, Kecil
                                $sisa_qty = $d->qty_retur;
                                $detail_retur = [];

                                // Hitung Satuan Besar (misal: Box/Dus)
                                $jml_besar = floor($sisa_qty / ($d->rasio_sedang * $d->rasio_kecil));
                                if ($jml_besar > 0) {
                                    $detail_retur[] =
                                        '<span class="fw-bold">' . $jml_besar . '</span> ' . $d->satuan_besar;
                                    $sisa_qty %= $d->rasio_sedang * $d->rasio_kecil;
                                }

                                // Hitung Satuan Sedang (misal: Strip/Pack)
                                $jml_sedang = floor($sisa_qty / $d->rasio_kecil);
                                if ($jml_sedang > 0) {
                                    $detail_retur[] =
                                        '<span class="fw-bold">' . $jml_sedang . '</span> ' . $d->satuan_sedang;
                                    $sisa_qty %= $d->rasio_kecil;
                                }

                                // Sisa Satuan Kecil (misal: Tablet/Pcs)
                                if ($sisa_qty > 0 || empty($detail_retur)) {
                                    $detail_retur[] =
                                        '<span class="fw-bold">' . $sisa_qty . '</span> ' . $d->satuan_kecil;
                                }
                            @endphp

                            <div
                                class="d-inline-block text-start p-2 rounded bg-danger-subtle border border-danger-subtle">
                                <div class="small text-danger fw-bold mb-1"
                                    style="font-size: 0.7rem; text-transform: uppercase;">Total Detail:</div>
                                <div class="text-dark lh-sm" style="font-size: 0.85rem;">
                                    {!! implode('<br>', $detail_retur) !!}
                                </div>
                                <hr class="my-1 opacity-25">
                                <div class="small text-muted" style="font-size: 0.7rem;">
                                    Sesuai Satuan Terkecil: {{ number_format($d->qty_retur, 0, ',', '.') }}
                                    {{ $d->satuan_kecil }}
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="text-muted small italic">"{{ $d->alasan_retur }}"</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<script>
    $(function() {
        var table = $("#tabelstokretur").DataTable({
            "responsive": true,
            "lengthChange": true,
            "autoWidth": false,
            "pageLength": 10,
            "order": [
                [0, 'desc']
            ],
            "dom": "<'row mb-3'<'col-md-4'l><'col-md-8 d-flex justify-content-end align-items-center'f B>>" +
                "<'row'<'col-md-12'tr>>" +
                "<'row mt-3'<'col-md-5'i><'col-md-7'p>>",
            "buttons": [{
                    extend: 'excelHtml5',
                    text: '<i class="bi bi-file-earmark-excel me-1"></i> Excel',
                    className: 'btn btn-success btn-sm border-0 shadow-sm mx-1',
                    title: 'Riwayat_Retur_KynovaPharma_' + new Date().toISOString().slice(0, 10),
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5],
                        format: {
                            body: function(data, row, column, node) {
                                // Membersihkan HTML pada kolom Qty Retur (index 4) agar rapi di Excel
                                if (column === 4) {
                                    return node.innerText.replace(/\n\s*\n/g, '\n').trim();
                                }
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
                    title: 'Laporan Riwayat Retur Pembelian',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5],
                        format: {
                            body: function(data, row, column, node) {
                                return node.innerText || node.textContent;
                            }
                        }
                    },
                    customize: function(doc) {
                        doc.styles.tableHeader.fillColor =
                        '#dc3545'; // Warna merah menyesuaikan tema Retur
                        doc.defaultStyle.fontSize = 9;
                    }
                }
            ],
            "language": {
                "search": "",
                "searchPlaceholder": "Cari supplier, faktur, atau batch...",
                "lengthMenu": "_MENU_",
            }
        });

        // Styling input pencarian
        $('.dataTables_filter input').addClass('form-control shadow-none border-0 bg-light px-3 py-2');
    });
</script>
