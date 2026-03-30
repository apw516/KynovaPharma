<style>
    #tabelpoheader thead th {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
        padding: 12px;
    }

    #tabelpoheader tbody td {
        font-size: 0.9rem;
        padding: 10px 8px;
    }

    /* Warna Badge Modern (Subtle) */
    .bg-success-subtle {
        background-color: #e8f5e9 !important;
    }

    .bg-warning-subtle {
        background-color: #fff3e0 !important;
    }

    .bg-danger-subtle {
        background-color: #ffebee !important;
    }

    .bg-info-subtle {
        background-color: #e3f2fd !important;
    }

    .bg-secondary-subtle {
        background-color: #f5f5f5 !important;
    }

    /* Efek hover baris */
    #tabelpoheader tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.02);
        transition: 0.2s;
    }

    .btn-white {
        background-color: #fff;
    }

    .btn-white:hover {
        background-color: #f8f9fa;
    }
</style>
<style>
    .uppercase {
        text-transform: uppercase;
        letter-spacing: 1px;
        font-weight: 700;
    }

    .card-body h3 {
        font-family: 'JetBrains Mono', monospace;
        /* Menggunakan font monospaced jika tersedia */
    }
</style>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 small mb-1 uppercase">Total PO Berhasil (Netto)</h6>
                        <h3 class="mb-0 fw-bold" id="total_po_sukses">
                            Rp {{ number_format($data_po->where('status', '1')->sum('grand_total'), 0, ',', '.') }}
                        </h3>
                    </div>
                    <div class="fs-1 opacity-25">
                        <i class="bi bi-cart-check-fill"></i>
                    </div>
                </div>
                <div class="mt-2 small text-white-50">
                    Berdasarkan {{ $data_po->where('status', '1')->count() }} transaksi sukses
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card border-0 shadow-sm bg-danger text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 small mb-1 uppercase">Total PO Diretur</h6>
                        <h3 class="mb-0 fw-bold" id="total_po_retur">
                            Rp
                            {{ number_format($data_po->where('status', '!=', '1')->sum('grand_total'), 0, ',', '.') }}
                        </h3>
                    </div>
                    <div class="fs-1 opacity-25">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </div>
                </div>
                <div class="mt-2 small text-white-50">
                    Berdasarkan {{ $data_po->where('status', '!=', '1')->count() }} faktur yang dibatalkan
                </div>
            </div>
        </div>
    </div>
</div>
<table id="tabelpoheader" class="table table-hover align-middle shadow-sm" style="width:100%">
    <thead class="table-dark">
        <tr>
            <th class="text-center">Tgl Pembelian</th>
            <th>No. Faktur</th>
            <th>Supplier</th>
            <th class="text-end">Subtotal</th>
            <th class="text-center">PPN</th>
            <th class="text-end">Grand Total</th>
            <th class="text-center">Status & Pembayaran</th>
            <th class="text-center">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data_po as $po)
            <tr class="{{ $po->status != '1' ? 'table-light opacity-75' : '' }}">
                <td class="text-center">
                    <span
                        class="fw-bold">{{ $po->tanggal_pembelian ? \Carbon\Carbon::parse($po->tanggal_pembelian)->translatedFormat('d M Y') : '-' }}</span>
                </td>
                <td>
                    <code class="text-primary fw-bold">{{ $po->nomor_faktur }}</code>
                </td>
                <td>
                    <div class="fw-semibold text-dark">{{ $po->nama_supplier }}</div>
                </td>
                <td class="text-end text-muted">
                    {{ number_format($po->sub_total, 0, ',', '.') }}
                </td>
                <td class="text-center">
                    <span class="badge bg-secondary-subtle text-secondary small">{{ $po->pajak_persen }}%</span>
                </td>
                <td class="text-end fw-bold text-success">
                    {{ number_format($po->grand_total, 0, ',', '.') }}
                </td>
                <td class="text-center">
                    @if ($po->jenis_pembayaran == 'Kredit')
                        <span
                            class="badge rounded-pill bg-warning-subtle text-warning border border-warning-subtle px-2">Hutang</span>
                    @else
                        <span
                            class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-2">Lunas</span>
                    @endif

                    @if ($po->status == '1')
                        <span
                            class="badge rounded-pill bg-info-subtle text-info border border-info-subtle px-2">OK</span>
                    @else
                        <span
                            class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle px-2">RETUR</span>
                    @endif
                </td>
                <td class="text-center">
                    <div class="btn-group shadow-sm">
                        <button class="btn btn-white btn-sm border btn-detail" data-id="{{ $po->id }}"
                            data-bs-toggle="modal" data-bs-target="#modaldetail" title="Detail Faktur">
                            <i class="bi bi-eye text-info"></i>
                        </button>
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
<!-- Modal -->
<div class="modal fade" id="modaldetail" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Detail Purchase Order</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="v_d">

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
    $(function() {
        var table = $("#tabelpoheader").DataTable({
            "responsive": true,
            "pageLength": 10,
            "order": [
                [0, "desc"]
            ],
            "dom": "<'row mb-3'<'col-md-6'l><'col-md-6 d-flex justify-content-end align-items-center'f B>>" +
                "<'row'<'col-md-12'tr>>" +
                "<'row mt-3'<'col-md-5'i><'col-md-7'p>>",
            "buttons": [{
                    extend: 'excelHtml5',
                    text: '<i class="bi bi-file-earmark-excel me-1"></i> Excel',
                    className: 'btn btn-success btn-sm border-0 shadow-sm mx-1',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6]
                    }
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="bi bi-file-earmark-pdf me-1"></i> PDF',
                    className: 'btn btn-danger btn-sm border-0 shadow-sm mx-1',
                    orientation: 'landscape',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6]
                    }
                }
            ],
            "language": {
                "search": "",
                "searchPlaceholder": "Cari supplier atau no faktur...",
                "lengthMenu": "_MENU_",
            },
            "drawCallback": function(settings) {
                var api = this.api();

                // Fungsi helper untuk parsing nominal Rupiah ke Integer
                var parseCurrency = function(html) {
                    return typeof html === 'string' ?
                        html.replace(/[^\d]/g, '') * 1 :
                        typeof html === 'number' ? html : 0;
                };

                // 1. Kalkulasi Total Sukses (Filter baris yang ada badge 'OK')
                var totalSukses = api.rows({
                        search: 'applied'
                    }).nodes().to$()
                    .filter(function() {
                        return $(this).find('.badge').text().includes('OK');
                    })
                    .map(function() {
                        // Ambil kolom Grand Total (index 5)
                        return parseCurrency(api.cell(this, 5).data());
                    }).toArray().reduce(function(a, b) {
                        return a + b;
                    }, 0);

                // 2. Kalkulasi Total Retur (Filter baris yang ada badge 'RETUR')
                var totalRetur = api.rows({
                        search: 'applied'
                    }).nodes().to$()
                    .filter(function() {
                        return $(this).find('.badge').text().includes('RETUR');
                    })
                    .map(function() {
                        return parseCurrency(api.cell(this, 5).data());
                    }).toArray().reduce(function(a, b) {
                        return a + b;
                    }, 0);

                // Update Tampilan Card
                $('#total_po_sukses').html('Rp ' + totalSukses.toLocaleString('id-ID'));
                $('#total_po_retur').html('Rp ' + totalRetur.toLocaleString('id-ID'));
            }
        });

        // Styling filter input
        $('.dataTables_filter input').addClass('form-control border-0 bg-light shadow-none px-3');
    });
    $('.btn-detail').on('click', function() {
        id = $(this).attr('data-id')
        spinner_on()
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                _token: "{{ csrf_token() }}",
                id
            },
            url: '<?= route('ambildetailpo') ?>',
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
                if (data.kode == 500) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oopss...',
                        text: data.message,
                        footer: ''
                    })
                } else {
                    Swal.fire({
                        icon: 'success',
                        title: 'OK',
                        text: data.message,
                        timer: 1500, // Agar Swal menutup otomatis
                        showConfirmButton: false
                    })

                    // Gunakan .html() bukan .val() untuk merender HTML di dalam elemen
                    // Pastikan .v_d_t_r adalah class milik <tbody> tabel Anda
                    $('.v_d').html(data.view);
                }
            }
        });
    })
    $('.btn-batal').on('click', function() {
        Swal.fire({
            title: "Anda yakin ?",
            text: "Retur PO header akan berpengaruh ke stok sediaan barang !",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, Retur PO !"
        }).then((result) => {
            if (result.isConfirmed) {
                id = $(this).attr('data-id')
                returpo(id)
            }
        });
    })

    function returpo(id) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                _token: "{{ csrf_token() }}",
                id
            },
            url: '<?= route('returpo') ?>',
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
                    Swal.fire({
                        icon: 'success',
                        title: 'OK',
                        text: data.message,
                        footer: ''
                    })
                    location.reload()
                }
            }
        });
    }
</script>
