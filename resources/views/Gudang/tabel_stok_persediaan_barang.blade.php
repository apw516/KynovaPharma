<style>
    /* Menghilangkan border default tombol datatables */
    .dt-buttons .btn {
        border-radius: 6px;
        margin-left: 5px;
        font-size: 12px;
        font-weight: 500;
        box-shadow: none !important;
    }

    /* Memperbaiki tampilan search box */
    .dataTables_filter {
        text-align: left !important;
    }

    .extra-small {
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }

    #tabelstokpersediaan thead th {
        padding-top: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid #eee;
    }

    .badge {
        font-size: 10px;
        font-weight: 600;
    }
</style>
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="tabelstokpersediaan">
                <thead class="bg-light text-muted extra-small text-uppercase">
                    <tr>
                        <th class="ps-4">Informasi Barang</th>
                        <th class="text-center">No. Batch</th>
                        <th class="text-center">Supplier</th>
                        <th class="text-center">Tgl Expired</th>
                        <th class="text-center">Status ED</th>
                        <th class="text-center">Jumlah Stok</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $r)
                        @php
                            $tgl_ed = \Carbon\Carbon::parse($r->tgl_expired);
                            $is_expired = $tgl_ed->isPast();
                            $sisa_hari = now()->diffInDays($tgl_ed, false);

                            // Logika Konversi Satuan Detail
                            $sisa = $r->stok_sekarang;

                            // Hitung Satuan Besar (Box/Pcs)
                            $jml_besar = floor($sisa / ($r->rasio_sedang * $r->rasio_kecil));
                            $sisa %= $r->rasio_sedang * $r->rasio_kecil;

                            // Hitung Satuan Sedang (Strip/Pack)
                            $jml_sedang = floor($sisa / $r->rasio_kecil);
                            $sisa %= $r->rasio_kecil;

                            // Sisa adalah Satuan Kecil (Tablet/Capsule)
                            $jml_kecil = $sisa;
                        @endphp
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark">{{ $r->nama_dagang }}</div>
                                <small class="text-muted">{{ $r->nama_obat }} | {{ $r->produsen }}</small>
                            </td>
                            <td class="text-center">
                                <code class="fw-bold text-primary">{{ $r->kode_batch }}</code>
                                <button class="btn btn-secondary btn-sm editsediaan" batch="{{ $r->kode_batch }}" kode_barang="{{ $r->kode_barang }}"
                                    stoksekarang="{{ $r->stok_sekarang }}" ed="{{ $tgl_ed->format('Y-m-d') }}"
                                    kode_supplier="{{ $r->kode_supplier }}" idsediaan="{{ $r->id }}">
                                    <i class="bi bi-pencil-square"></i></button>
                            </td>
                            <td class="text-center small">{{ $r->nama_supplier }}</td>
                            <td class="text-center">
                                <span class="fw-bold">{{ $tgl_ed->format('d/m/Y') }}</span>
                            </td>
                            <td class="text-center">
                                @if ($is_expired)
                                    <span
                                        class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill border border-danger border-opacity-25">
                                        <i class="bi bi-x-circle-fill me-1"></i> EXPIRED
                                    </span>
                                @elseif($sisa_hari <= 90)
                                    <span
                                        class="badge bg-warning-subtle text-warning px-3 py-2 rounded-pill border border-warning border-opacity-25">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i> HAMPIR ED
                                    </span>
                                @else
                                    <span
                                        class="badge bg-success-subtle text-success px-3 py-2 rounded-pill border border-success border-opacity-25">
                                        <i class="bi bi-check-circle-fill me-1"></i> AKTIF
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="mb-1">
                                    <span class="badge bg-dark px-2 py-1">
                                        Total: {{ $r->stok_sekarang }} {{ $r->satuan_kecil }}
                                    </span>
                                </div>

                                <div class="d-flex justify-content-center gap-1" style="font-size: 0.7rem;">
                                    @if ($jml_besar > 0)
                                        <div class="border rounded px-1 bg-light">
                                            <span class="fw-bold text-primary">{{ $jml_besar }}</span>
                                            <span class="text-muted">{{ $r->satuan_besar }}</span>
                                        </div>
                                    @endif

                                    @if ($jml_sedang > 0)
                                        <div class="border rounded px-1 bg-light">
                                            <span class="fw-bold text-primary">{{ $jml_sedang }}</span>
                                            <span class="text-muted">{{ $r->satuan_sedang }}</span>
                                        </div>
                                    @endif

                                    <div class="border rounded px-1 bg-light">
                                        <span class="fw-bold text-primary">{{ $jml_kecil }}</span>
                                        <span class="text-muted">{{ $r->satuan_kecil }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <button @if (auth()->user()->hak_akses != 1) disabled @endif
                                    class="btn btn-danger btn-sm retur" batch="{{ $r->kode_batch }}"
                                    stoksekarang="{{ $r->stok_sekarang }}"
                                    idsediaan="{{ $r->id }}">Retur</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="modaleditsediaan" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Edit Sediaan</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" class="form_edit_sediaan">
                    <div class="mb-3">
                        <label for="exampleFormControlInput1" class="form-label">Nomor Batch</label>
                        <input type="text" class="form-control" id="nomorbatch" name="nomorbatch">
                        <input hidden type="text" class="form-control" id="idsediaan" name="idsediaan">
                        <input hidden type="text" class="form-control" id="kode_barang" name="kode_barang">
                    </div>
                    <div class="mb-3">
                        <label for="exampleFormControlInput1" class="form-label">Tanggal ED</label>
                        <input type="date" class="form-control" id="tanggaled" name="tanggaled"
                            placeholder="Masukan jumlah stok yang akan diretur ...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Supplier</label>
                        <select class="form-select border-0 bg-light" id="kode_supplier" name="kode_supplier">
                            @foreach ($supplier as $s)
                                <option value="{{ $s->kode_supplier }}">{{ $s->nama_supplier }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Koreksi Stok</label> <br>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="koreksistok" id="koreksistok"
                                value="1">
                            <label class="form-check-label" for="inlineRadio1">Ya</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="koreksistok" id="koreksistok"
                                value="2" checked>
                            <label class="form-check-label" for="inlineRadio2">Tidak</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="exampleFormControlInput1" class="form-label">Stok Sekarang</label>
                        <input readonly type="text" class="form-control" id="stoksekarang" name="stoksekarang">
                    </div>
                    <div class="mb-3">
                        <label for="exampleFormControlInput1" class="form-label">Stok Setelah Koreksi</label>
                        <input type="text" class="form-control" id="stokkoreksi" name="stokkoreksi"
                            value="0">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary simpenedit">Simpan Edit</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="modalretursediaan" tabindex="-1" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Modal title</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning small">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    Pastikan jumlah retur tidak melebihi stok yang tersedia di batch ini.
                </div>
                <div class="mb-3">
                    <label for="exampleFormControlInput1" class="form-label">Jumlah Stok Sekarang</label>
                    <input readonly type="text" class="form-control" id="jumlahstoksekarang">
                    <input hidden type="text" class="form-control" id="idsediaan">
                </div>
                <div class="mb-3">
                    <label for="exampleFormControlInput1" class="form-label">Jumlah Stok Retur</label>
                    <input type="email" class="form-control" id="jumlahstokretur"
                        placeholder="Masukan jumlah stok yang akan diretur ...">
                    <div id="emailHelp" class="form-text text-danger">*Jumlah yang dimasukan adalah jumlah satuan
                        kecil
                        ...</div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Alasan Retur</label>
                    <select class="form-select border-0 bg-light" id="alasanretur">
                        <option>Barang Rusak/Cacat</option>
                        <option>Mendekati Kadaluarsa (ED)</option>
                        <option>Salah Input/Admin</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary simpanretur">Simpan Retur</button>
            </div>
        </div>
    </div>
</div>
<style>
    .extra-small {
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }

    #tabelRetur thead th {
        padding-top: 15px;
        padding-bottom: 15px;
    }

    .badge {
        font-size: 10px;
        font-weight: 600;
    }
</style>
<script>
    $(document).ready(function() {
        var table = $('#tabelstokpersediaan').DataTable({
            "pageLength": 15,
            "responsive": true,
            "order": [
                [3, "asc"]
            ], // Urutkan berdasarkan Tgl Expired (indeks ke-3)
            "language": {
                "search": "",
                "searchPlaceholder": "Cari Obat...",
                "lengthMenu": "_MENU_ data",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ stok",
                "paginate": {
                    "next": '<i class="bi bi-chevron-right"></i>',
                    "previous": '<i class="bi bi-chevron-left"></i>'
                }
            },
            "dom": "<'row px-4 py-3 align-items-center'<'col-md-4'f><'col-md-8 text-end'B>>" +
                "<'row'<'col-md-12'tr>>" +
                "<'row px-4 py-3'<'col-md-5'i><'col-md-7'p>>",
            "buttons": [{
                    extend: 'excelHtml5',
                    text: '<i class="bi bi-file-earmark-excel me-1"></i> Excel',
                    className: 'btn btn-success btn-sm',
                    title: 'Data Stok Persediaan KynovaPharma',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 5]
                    } // Kecualikan kolom Status ED (badge)
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="bi bi-file-earmark-pdf me-1"></i> PDF',
                    className: 'btn btn-danger btn-sm',
                    title: 'Laporan Stok Persediaan',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 5]
                    }
                },
                {
                    extend: 'print',
                    text: '<i class="bi bi-printer me-1"></i> Print',
                    className: 'btn btn-dark btn-sm',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 5]
                    }
                }
            ]
        });

        // Rapikan tampilan input search
        $('.dataTables_filter input').addClass('form-control shadow-none');
    });
</script>
<script>
    $('.editsediaan').on('click', function() {
        let id = $(this).attr('idsediaan');
        let stoksekarang = $(this).attr('stoksekarang');
        let kode_barang = $(this).attr('kode_barang');
        let tanggaled = $(this).attr('ed');
        let batch = $(this).attr('batch');
        let kode_supplier = $(this).attr('kode_supplier');
        $('#idsediaan').val(id)
        $('#nomorbatch').val(batch)
        $('#tanggaled').val(tanggaled)
        $('#kode_barang').val(kode_barang)
        $('#stoksekarang').val(stoksekarang)
        $('#kode_supplier').val(kode_supplier)
        // Set data ke dalam modal kedua sebelum ditampilkan
        $('#modaleditsediaan').find('.modal-title').html('Form Edit Sediaan No Batch- ' + batch);
        // Tampilkan modal secara manual
        $('#modaleditsediaan').modal('show');
    });
    $('.retur').on('click', function() {
        let id = $(this).attr('idsediaan');
        let stoksekarang = $(this).attr('stoksekarang');
        let batch = $(this).attr('batch');
        $('#idsediaan').val(id)
        $('#jumlahstoksekarang').val(stoksekarang)
        // Set data ke dalam modal kedua sebelum ditampilkan
        $('#modalretursediaan').find('.modal-title').html('Form Retur Sediaan No Batch- ' + batch);
        // Tampilkan modal secara manual
        $('#modalretursediaan').modal('show');
    });
    $('.simpanretur').on('click', function() {
        Swal.fire({
            title: "Anda yakin ?",
            text: "Stok sediaan akan diretur ...",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya , Retur stok !"
        }).then((result) => {
            if (result.isConfirmed) {
                simpanretur()
            }
        });
    })
    $('.simpenedit').on('click', function() {
        Swal.fire({
            title: "Anda yakin ?",
            text: "Stok sediaan akan diedit ...",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya , Edit Sediaan !"
        }).then((result) => {
            if (result.isConfirmed) {
                simpaneditsediaan()
            }
        });
    })

    function simpaneditsediaan() {
        spinner_on()
        var data = $('.form_edit_sediaan').serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                _token: "{{ csrf_token() }}",
                data: JSON.stringify(data)
            },
            url: '<?= route('simpaneditsediaan') ?>',
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
                    location.reload()
                }
            }
        });
    }

    function simpanretur() {
        jumlahstoksekarang = $('#jumlahstoksekarang').val()
        idsediaan = $('#idsediaan').val()
        jumlahstokretur = $('#jumlahstokretur').val()
        alasanretur = $('#alasanretur').val()
        spinner_on()
        var data = $('.formbarang').serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                _token: "{{ csrf_token() }}",
                jumlahstoksekarang,
                idsediaan,
                jumlahstokretur,
                alasanretur
            },
            url: '<?= route('simpanretursediaan') ?>',
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
                    location.reload()
                }
            }
        });
    }
</script>
