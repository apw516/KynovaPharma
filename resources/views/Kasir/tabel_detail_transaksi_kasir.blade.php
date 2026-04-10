<table id="tabellog" class="table table-sm table-bordered table-hover table-striped">
    <thead>
        <th>Nama Barang</th>
        <th>qty</th>
        {{-- <th>Harga</th> --}}
        <th>Subtotal</th>
        <th>Diskon</th>
        <th>Grandtotal</th>
        <th></th>
    </thead>
    <tbody>
        @foreach ($data as $d)
            <tr>
                <td>{{ $d->nama_dagang }} @if ($d->status_retur == 2)
                        <span class="badge text-bg-danger">Retur</span>
                    @endif
                </td>
                <td>
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

                    {{-- Tampilkan hasil dengan pemisah koma atau spasi --}}
                    @if (empty($result))
                        0 Tablet
                    @else
                        {{ implode(', ', $result) }}
                    @endif
                    {{-- {{ $d->qty }} --}}
                </td>
                {{-- <td>Rp {{ number_format($d->harga_jual, 0, ',', '.') }}</td> --}}
                <td>Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($d->diskon, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($d->grandtotal, 0, ',', '.') }}</td>
                <td>
                    <button class="btn btn-danger btn-sm returdetail" iddetail="{{ $d->iddetail }}"
                        barang="{{ $d->nama_dagang }}" @if ($d->status_retur == 2) disabled @endif><i
                            class="bi bi-trash3"></i></button>
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
