<table id="tabellog" class="table table-sm table-bordered table-hover table-striped">
    <thead>
        <th>Nama Barang</th>
        <th>qty</th>
        <th>Harga</th>
        <th>Subtotal</th>
        <th>Diskon</th>
        <th>Grandtotal</th>
        <th></th>
    </thead>
    <tbody>
        @foreach ($data as $d)
            <tr>
                <td>{{ $d->nama_dagang }} @if($d->status_retur == 2) <span class="badge text-bg-danger">Retur</span> @endif</td>
                <td>{{ $d->qty }}</td>
                <td>Rp {{ number_format($d->harga_jual, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($d->diskon, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($d->grandtotal, 0, ',', '.') }}</td>
                <td>
                    <button class="btn btn-danger btn-sm returdetail" iddetail="{{ $d->iddetail }}"
                        barang="{{ $d->nama_dagang }}" @if($d->status_retur == 2) disabled @endif><i class="bi bi-trash3"></i></button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
<script>
    $('.returdetail').on('click', function() {
        id = $(this).attr('iddetail')
        inv = $(this).attr('iddetail')
        barang = $(this).attr('barang')
        Swal.fire({
            title: "Anda yakin ?",
            text: "Pembelian " + barang + " akan diretur !",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, Retur "
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: "Pembelian : " + barang + " Akan diretur",
                    showDenyButton: true,
                    showCancelButton: false,
                    confirmButtonText: "OK",
                    denyButtonText: `Batal`
                }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        returdetail(id)
                    } else if (result.isDenied) {
                        Swal.fire("Batal retur ...", "", "info");
                    }
                });
            };
        });
    })

    function returdetail(id) {
        spinner_on()
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                _token: "{{ csrf_token() }}",
                id
            },
            url: '<?= route('returdetail') ?>',
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
                    location.reload()
                }
            }
        });
    }
</script>
