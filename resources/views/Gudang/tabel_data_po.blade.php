{{-- <table id="tabelpoheader" class="table table-sm table-hover table-bordered">
    <thead>
        <th>Tanggal Pembelian</th>
        <th>Nomor Faktur</th>
        <th>Nama Supplier</th>
        <th>Subtotal</th>
        <th>PPn</th>
        <th>Grandtotal</th>
        <th>Keterangan</th>
        <th>Aksi</th>
    </thead>
    <tbody>
        @foreach ($data_po as $po)
            <tr> --}}
{{-- <td>{{ $loop->iteration }}</td> --}}
{{-- <td>{{ $po->tanggal_pembelian ? \Carbon\Carbon::parse($po->tanggal_pembelian)->translatedFormat('d F Y') : '-' }}
                </td>
                <td>{{ $po->nomor_faktur }}</td>
                <td>{{ $po->nama_supplier }}</td>
                <td>Rp {{ number_format($po->sub_total, 0, ',', '.') }}</td>
                <td>{{ $po->pajak_persen }} %</td>
                <td class="text-end">Rp {{ number_format($po->grand_total, 0, ',', '.') }}</td>
                <td>
                    @if ($po->jenis_pembayaran == 'Kredit')
                        <span class="badge bg-warning">Hutang</span>
                    @else
                        <span class="badge bg-success">Lunas</span>
                    @endif
                    |
                    @if ($po->status == '1')
                        <span class="badge bg-success">OK</span>
                    @else
                        <span class="badge bg-danger">RETUR</span>
                    @endif
                </td>
                <td>
                    <button class="btn btn-info btn-sm btn-detail" data-id="{{ $po->id }}" data-bs-toggle="modal"
                        data-bs-target="#modaldetail">
                        <i class="bi bi-ticket-detailed"></i>
                    </button>
                    <button class="btn btn-danger btn-sm btn-batal" data-id="{{ $po->id }}">
                        <i class="bi bi-x-circle"></i>
                    </button>
                </td>
            </tr>
        @endforeach

    </tbody>
</table> --}}
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
                    Rp {{ number_format($po->sub_total, 0, ',', '.') }}
                </td>
                <td class="text-center">
                    <span class="badge bg-secondary-subtle text-secondary small">{{ $po->pajak_persen }}%</span>
                </td>
                <td class="text-end fw-bold text-success">
                    Rp {{ number_format($po->grand_total, 0, ',', '.') }}
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
                        @if ($po->status == '1')
                            <button class="btn btn-white btn-sm border btn-batal" data-id="{{ $po->id }}"
                                title="Batalkan/Retur">
                                <i class="bi bi-x-circle text-danger"></i>
                            </button>
                        @endif
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
        $("#tabelpoheader").DataTable({
            "responsive": true,
            "pageLength": 10,
            "order": [
                [0, "desc"]
            ], // Urutkan tanggal terbaru
            "language": {
                "search": "",
                "searchPlaceholder": "Cari supplier atau no faktur...",
            },
            "dom": "<'row mb-3'<'col-md-6'l><'col-md-6'f>>rt<'row mt-3'<'col-md-5'i><'col-md-7'p>>",
        });

        // Styling input pencarian agar seragam dengan dashboard KynovaPharma
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
