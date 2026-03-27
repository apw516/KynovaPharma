<table id="tabelpoheader" class="table table-sm table-hover table-bordered">
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
            <tr>
                {{-- <td>{{ $loop->iteration }}</td> --}}
                <td>{{ $po->tanggal_pembelian ? \Carbon\Carbon::parse($po->tanggal_pembelian)->translatedFormat('d F Y') : '-' }}
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
            "lengthChange": false,
            "autoWidth": false,
            "pageLength": 12,
            "searching": true,
            "ordering": false,
        })
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
            id = $(this).attr('data-id')
            returpo(id)
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
