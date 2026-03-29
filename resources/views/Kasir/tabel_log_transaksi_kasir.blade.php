{{-- <table id="tabellog" class="table table-sm table-bordered table-hover table-striped">
    <thead>
        <th>Tanggal</th>
        <th>No invoice</th>
        <th>Total Retur</th>
        <th>Total Bayar</th>
        <th>Nominal Bayar</th>
        <th>Nominal Kembalian</th>
        <th>User</th>
        <th></th>
    </thead>
    <tbody>
        @php
            $total = 0;
            $total_retur = 0;
        @endphp
        @foreach ($data as $item)
            <tr>
                <td>{{ \Carbon\Carbon::parse($item->tgl_transaksi)->format('d-m-Y') }}</td>
                <td>{{ $item->no_invoice }} @if ($item->status == 2)
                        <span class="badge text-bg-light">Retur</span>
                    @endif
                </td>
                <td>Rp {{ number_format($item->total_retur, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($item->total_bayar, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($item->nominal_terima, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($item->nominal_kembali, 0, ',', '.') }}</td>
                <td>
                    {{ $item->nama }}
                </td>
                <td>
                    <button class="btn btn-primary detailpembayaran" data-bs-toggle="modal" data-bs-target="#modaldetail"
                        idheader="{{ $item->id }}"><i class="bi bi-ticket-detailed"></i></button>
                    <button @if ($item->status == 2) disabled @endif class="btn btn-danger returpembayaran"
                        idheader="{{ $item->id }}" inv="{{ $item->no_invoice }}"><i
                            class="bi bi-trash3"></i></button>
                </td>
            </tr>
            @php
                $total = $total + $item->total_bayar;
                $total_retur = $total_retur + $item->total_retur;
            @endphp
        @endforeach
    </tbody>
</table> --}}
<style>
    /* Styling Tabel Modern */
    #tabellog thead th {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #6c757d;
        border-top: none;
    }

    #tabellog tbody td {
        border-bottom: 1px solid #f2f2f2;
        padding: 12px 8px;
    }

    /* Background Badge Subtitle (Soft Colors) */
    .bg-success-subtle {
        background-color: #d1e7dd !important;
    }

    .bg-danger-subtle {
        background-color: #f8d7da !important;
    }

    .bg-secondary-subtle {
        background-color: #e2e3e5 !important;
    }

    /* Tombol Style */
    .btn-white {
        background: #fff;
    }

    .btn-white:hover {
        background: #f8f9fa;
    }

    /* Efek Card */
    .shadow-sm {
        box-shadow: 0 .125rem .25rem rgba(0, 0, 0, .075) !important;
    }
</style>
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h6 class="m-0 fw-bold text-dark">
            <i class="bi bi-clock-history me-2"></i>Riwayat Transaksi:
            <span class="text-primary">{{ \Carbon\Carbon::parse($awal)->format('d M Y') }} -
                {{ \Carbon\Carbon::parse($tglakhir)->format('d M Y') }}</span>
        </h6>
    </div>
    <div class="card-body">
        <table id="tabellog" class="table table-hover align-middle" style="width:100%">
            <thead class="table-light">
                <tr>
                    <th class="text-center">Tanggal</th>
                    <th>No. Invoice</th>
                    <th class="text-end">Total Bayar</th>
                    <th class="text-end">Tunai</th>
                    <th class="text-end">Kembali</th>
                    <th class="text-center">Kasir</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total = 0;
                    $total_retur = 0;
                @endphp
                @foreach ($data as $item)
                    <tr class="{{ $item->status == 2 ? 'table-light opacity-75' : '' }}">
                        <td class="text-center small">
                            {{ \Carbon\Carbon::parse($item->tgl_transaksi)->format('d/m/Y') }}
                        </td>
                        <td>
                            <span class="fw-bold text-dark">{{ $item->no_invoice }}</span>
                            @if ($item->status == 2)
                                <span class="badge rounded-pill bg-danger-subtle text-danger ms-1">Retur</span>
                            @else
                                <span class="badge rounded-pill bg-success-subtle text-success ms-1">Lunas</span>
                            @endif
                        </td>
                        <td class="text-end fw-bold">Rp {{ number_format($item->total_bayar, 0, ',', '.') }}</td>
                        <td class="text-end text-muted">Rp {{ number_format($item->nominal_terima, 0, ',', '.') }}</td>
                        <td class="text-end text-muted">Rp {{ number_format($item->nominal_kembali, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <span class="badge bg-secondary-subtle text-secondary small">{{ $item->nama }}</span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm shadow-sm">
                                <button title="Detail" class="btn btn-white border detailpembayaran"
                                    data-bs-toggle="modal" data-bs-target="#modaldetail" idheader="{{ $item->id }}">
                                    <i class="bi bi-eye text-primary"></i>
                                </button>
                                <button title="Retur" @if ($item->status == 2) disabled @endif
                                    class="btn btn-white border returpembayaran" idheader="{{ $item->id }}"
                                    inv="{{ $item->no_invoice }}">
                                    <i class="bi bi-arrow-counterclockwise text-danger"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @php
                        $total = $total + $item->total_bayar;
                        $total_retur = $total_retur + $item->total_retur;
                    @endphp
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-primary text-white">
            <div class="card-body">
                <h6 class="text-uppercase opacity-75 small fw-bold">Total Penjualan Kotor</h6>
                <h3 class="mb-0 fw-bold">Rp {{ number_format($total, 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-danger text-white">
            <div class="card-body">
                <h6 class="text-uppercase opacity-75 small fw-bold">Total Retur</h6>
                <h3 class="mb-0 fw-bold">Rp {{ number_format($total_retur, 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-success text-white">
            <div class="card-body">
                <h6 class="text-uppercase opacity-75 small fw-bold">Pendapatan Bersih</h6>
                <h3 class="mb-0 fw-bold">Rp {{ number_format($total - $total_retur, 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>
</div>
{{-- <div class="card">
    <div class="card-footer">
       <h5 class="fw-bold fst-italic">  Periode {{ \Carbon\Carbon::parse($awal)->format('d-M-Y') }} S.d
            {{ \Carbon\Carbon::parse($tglakhir)->format('d-M-Y') }} </h5>
        <h5 class=" fst-italic"> Total Penjualan : Rp {{ number_format($total, 0, ',', '.') }}</h5>
        <h5 class=" fst-italic"> Total Retur : Rp
            {{ number_format($total_retur, 0, ',', '.') }}</h5>
        <br>
        <h4 class="fw-bold fst-italic">Total Pendapatan :  Rp
            {{ number_format($total - $total_retur, 0, ',', '.') }}</h4>
        </h4>
    </div>
</div> --}}
<!-- Modal -->
<div class="modal fade" id="modaldetail" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Detail Transaksi</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="v_detail">

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
        $("#tabellog").DataTable({
            "responsive": true,
            "lengthChange": true,
            "pageLength": 10,
            "order": [
                [0, 'desc']
            ], // Urutkan tgl terbaru otomatis
            "dom": "<'row mb-3'<'col-md-6'l><'col-md-6'f>>rt<'row mt-3'<'col-md-5'i><'col-md-7'p>>",
            "language": {
                "search": "",
                "searchPlaceholder": "Cari invoice atau kasir...",
            }
        });

        // Custom Search styling
        $('.dataTables_filter input').addClass('form-control shadow-sm border-0 bg-light px-3');
    });
    $('.detailpembayaran').on('click', function() {
        spinner_on()
        id = $(this).attr('idheader')
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                _token: "{{ csrf_token() }}",
                id
            },
            url: '<?= route('ambildetailtransaksi') ?>',
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
                        timer: 1500, // Agar Swal menutup otomatis
                        showConfirmButton: false
                    })

                    // Gunakan .html() bukan .val() untuk merender HTML di dalam elemen
                    // Pastikan .v_d_t_r adalah class milik <tbody> tabel Anda
                    $('.v_detail').html(data.view);
                }
            }
        });
    })
    $('.returpembayaran').on('click', function() {
        id = $(this).attr('idheader')
        inv = $(this).attr('inv')
        Swal.fire({
            title: "Anda yakin ?",
            text: "Data pembayaran dengan no invoice : " + inv + " akan diretur ... !",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, Retur "
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: "Invoice : " + inv + " Akan diretur",
                    showDenyButton: true,
                    showCancelButton: false,
                    confirmButtonText: "OK",
                    denyButtonText: `Batal`
                }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        returheader(id)
                    } else if (result.isDenied) {
                        Swal.fire("Batal retur ...", "", "info");
                    }
                });
            };
        });
    })

    function returheader(id) {
        spinner_on()
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                _token: "{{ csrf_token() }}",
                id
            },
            url: '<?= route('returheader') ?>',
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
                    spinner_off()
                    ambildatalog()
                }
            }
        });
    }
</script>
