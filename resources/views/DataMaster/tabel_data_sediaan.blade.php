<table class="table table-sm">
    <thead>
        <th>Tanggal Input</th>
        <th>Supplier</th>
        <th>No Batch</th>
        <th>Expired Date</th>
        <th>Stok Awal</th>
        <th>Stok Sekarang</th>
        <th>Harga Modal</th>
        <th></th>
    </thead>
    <tbody>
        @foreach ($data_sediaan as $d)
            @if ($d->stok_sekarang > 0)
                <tr>
                    <td>{{ $d->tgl_input ? \Carbon\Carbon::parse($d->tgl_input)->translatedFormat('d F Y') : '-' }}</td>
                    <td>{{ $d->supplier->nama_supplier }}</td>
                    <td>{{ $d->kode_batch }}</td>
                    <td>{{ $d->tgl_expired ? \Carbon\Carbon::parse($d->tgl_expired)->translatedFormat('d F Y') : '-' }}
                    </td>
                    <td>{{ $d->stok_awal }}</td>
                    <td>{{ $d->stok_sekarang }} {{ $data_master[0]->satuan_kecil }}</td>
                    <td>{{ number_format($d->harga_modal_satuan_kecil, 0, ',', '.') }} /
                        {{ $data_master[0]->satuan_kecil }}</td>
                    <td>
                        <button class="btn btn-danger btn-sm retur" batch="{{ $d->kode_batch }}"
                            stoksekarang="{{ $d->stok_sekarang }}"idsediaan="{{ $d->idsediaan }}">Retur</button>
                    </td>
                </tr>
            @endif
        @endforeach
    </tbody>
</table>

<!-- Modal -->
<div class="modal fade" id="modalretursediaan" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                    <div id="emailHelp" class="form-text text-danger">*Jumlah yang dimasukan adalah jumlah satuan kecil
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
<script>
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

                }
            }
        });
    }
</script>
