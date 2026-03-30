<style>
    #tabellog thead th {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #5a5c69;
        font-weight: 700;
        border: none;
    }

    #tabellog tbody td {
        font-size: 0.9rem;
        padding: 1rem 0.75rem;
    }

    /* Soft Background Badges */
    .bg-success-subtle {
        background-color: #e8f5e9 !important;
    }

    .bg-warning-subtle {
        background-color: #fff3e0 !important;
    }

    .text-success {
        color: #2e7d32 !important;
    }

    .text-warning {
        color: #ef6c00 !important;
    }

    /* Font monospaced untuk angka agar sejajar */
    .text-end {
        font-family: 'Roboto Mono', monospace;
        font-size: 0.85rem;
    }

    .shadow-sm {
        box-shadow: 0 .125rem .25rem rgba(0, 0, 0, .075) !important;
    }
</style>
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex align-items-center">
        <h6 class="m-0 fw-bold text-dark"><i class="bi bi-person-badge me-2"></i>Laporan Aktivitas Sesi Kasir</h6>
    </div>
    <div class="card-body">
        <table id="tabellog" class="table table-hover align-middle" style="width:100%">
            <thead class="table-light">
                <tr>
                    <th class="text-center">Tgl Sesi</th>
                    <th>Nama Admin</th>
                    <th class="text-end">Saldo Awal</th>
                    <th class="text-end">Pendapatan</th>
                    <th class="text-end">Saldo Akhir</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total = 0;
                    $total_pendapatan = 0;
                @endphp
                @foreach ($data as $item)
                    @php
                        $pendapatan = $item->status == 2 ? $item->saldo_akhir - $item->saldo_awal : 0;
                    @endphp
                    @php
                        $total = $total + $item->saldo_akhir;
                        if ($item->status == 2) {
                            $pendapatan = $item->saldo_akhir - $item->saldo_awal;
                        } else {
                            $pendapatan = 0;
                        }
                        $total_pendapatan = $total_pendapatan + $pendapatan;
                    @endphp
                    <tr>
                        <td class="text-center">
                            <span
                                class="fw-bold">{{ \Carbon\Carbon::parse($item->tgl_sesi_kasir)->format('d/m/Y') }}</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-secondary-subtle d-flex align-items-center justify-content-center me-2"
                                    style="width: 30px; height: 30px;">
                                    <i class="bi bi-person small text-secondary"></i>
                                </div>
                                <span class="text-dark fw-medium">{{ $item->nama }}</span>
                            </div>
                        </td>
                        <td class="text-end text-muted">Rp {{ number_format($item->saldo_awal, 0, ',', '.') }}</td>
                        <td class="text-end fw-bold text-primary">
                            Rp {{ number_format($pendapatan, 0, ',', '.') }}
                        </td>
                        <td class="text-end fw-bold bg-light-subtle">
                            Rp {{ number_format($item->saldo_akhir, 0, ',', '.') }}
                        </td>
                        <td class="text-center">
                            @if ($item->status == 1)
                                <span
                                    class="badge rounded-pill bg-warning-subtle text-warning px-3 border border-warning-subtle">
                                    <i class="bi bi-door-open-fill me-1"></i> TERBUKA
                                </span>
                            @else
                                <span
                                    class="badge rounded-pill bg-success-subtle text-success px-3 border border-success-subtle">
                                    <i class="bi bi-lock-fill me-1"></i> TERTUTUP
                                </span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm bg-gradient-primary text-white"
            style="background: linear-gradient(45deg, #4e73df, #224abe);">
            <div class="card-body">
                <h6 class="text-uppercase opacity-75 small fw-bold">Total Pendapatan Bersih</h6>
                <h2 class="mb-0 fw-bold">Rp {{ number_format($total_pendapatan, 0, ',', '.') }}</h2>
                <small class="opacity-75 fst-italic">Periode: {{ \Carbon\Carbon::parse($awal)->format('d M') }} -
                    {{ \Carbon\Carbon::parse($tglakhir)->format('d M Y') }}</small>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm bg-light">
            <div class="card-body">
                <h6 class="text-uppercase text-muted small fw-bold">Akumulasi Saldo Akhir</h6>
                <h2 class="mb-0 fw-bold text-dark">Rp {{ number_format($total, 0, ',', '.') }}</h2>
                <small class="text-muted">Total dari seluruh sesi yang tercatat</small>
            </div>
        </div>
    </div>
</div>
<script>
    $(function() {
        var table = $("#tabellog").DataTable({
            "responsive": true,
            "pageLength": 10,
            "searching": true,
            "ordering": true,
            "order": [
                [0, "desc"]
            ], // Sesi terbaru di atas
            // l=length, f=filter, B=buttons, r=processing, t=table, i=info, p=pagination
            "dom": "<'row mb-3'<'col-md-4'l><'col-md-8 d-flex justify-content-end align-items-center'f B>>" +
                "<'row'<'col-md-12'tr>>" +
                "<'row mt-3'<'col-md-5'i><'col-md-7'p>>",
            "buttons": [{
                    extend: 'excelHtml5',
                    text: '<i class="bi bi-file-earmark-excel me-1"></i> Excel',
                    className: 'btn btn-success btn-sm border-0 shadow-sm mx-1',
                    title: 'Log_Satu_Admin_KynovaPharma',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="bi bi-file-earmark-pdf me-1"></i> PDF',
                    className: 'btn btn-danger btn-sm border-0 shadow-sm mx-1',
                    orientation: 'portrait',
                    pageSize: 'A4',
                    title: 'Laporan Log Sesi Admin',
                    exportOptions: {
                        columns: ':visible'
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
                "searchPlaceholder": "Cari admin atau tanggal...",
                "lengthMenu": "_MENU_",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ log"
            }
        });

        // Rapikan search bar agar tetap konsisten dengan gaya KynovaPharma
        $('.dataTables_filter input').addClass('form-control border-0 bg-light px-3 shadow-none py-2');
    });
</script>
