@extends('Template.Main')
@section('container')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Stok Sediaan Barang</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Stok Sediaan Barang</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="app-content">
        <div class="container-fluid">
            <div class="card mt-3">
                <div class="card-header">Tabel Master Barang</div>
                <div class="card-body">
                    <table class="table table-bordered table-hover table-sm" id="table-barang">
                        <thead>
                            <tr>
                                <th>Kode Barang</th>
                                <th>Nama Barang</th>
                                <th>Merk Dagang</th>
                                <th>Produsen</th>
                                <th>Satuan Besar</th>
                                <th>Satuan Sedang</th>
                                <th>Satuan Kecil</th>
                                <th>Sediaan</th>
                                <th>Aturan Pakai</th>
                                <th>Stok Tersedia</th>
                                {{-- <th>Harga Modal</th> --}}
                                <th>Harga Jual</th>
                                {{-- <th>Margin</th> --}}
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="modaledithargajual" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Edit Master Barang</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="exampleFormControlInput1" class="form-label">Harga Jual</label>
                        <input type="email" class="form-control input-mask-uang" id="hargajual"
                            placeholder="Input harga jual ...">
                        <input hidden type="email" class="form-control" id="idbarangharga"
                            placeholder="Input harga jual ...">
                        <input hidden type="email" class="form-control nilai-asli" id="hargajualasli"
                            placeholder="Input harga jual ...">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" onclick="simpanhargajual()">Simpan</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="modaldetailsediaan" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Detail Sediaan Barang</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="v_sediaanbarang">

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(function() {
            var table = $('#table-barang').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('getdatabarang2') }}",
                columns: [{
                        data: 'kode_barang',
                        name: 'kode_barang',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nama_obat',
                        name: 'nama_obat'
                    },
                    {
                        data: 'nama_dagang',
                        name: 'nama_dagang'
                    },
                    {
                        data: 'produsen',
                        name: 'produsen'
                    },
                    {
                        data: 'satuan_besar',
                        name: 'satuan_besar'
                    },
                    {
                        data: 'satuan_sedang',
                        name: 'satuan_sedang'
                    },
                    {
                        data: 'satuan_kecil',
                        name: 'satuan_kecil'
                    },
                    {
                        data: 'sediaan',
                        name: 'sediaan'
                    },
                    {
                        data: 'aturan_pakai',
                        name: 'aturan_pakai'
                    },
                    {
                        data: 'stok_terakhir',
                        name: 'stok_terakhir',
                        className: 'text-center',
                        searchable: false, // Tambahkan ini
                        orderable: false // Tambahkan ini agar tidak error saat di-klik judul kolomnya
                    },
                    // {
                    //     data: 'harga_modal',
                    //     name: 'harga_modal'
                    // },
                    {
                        data: 'harga_jual',
                        name: 'harga_jual',
                        render: function(data, type, row) {
                            if (data == null) return '0';

                            // Menambahkan Rp dan mengubah angka ke format titik (1.000.000)
                            return 'Rp ' + parseFloat(data).toFixed(0).replace(
                                /(\d)(?=(\d{3})+(?!\d))/g, '$1.');
                        }
                    },
                    // {
                    //     data: 'margin_penjualan',
                    //     name: 'margin_penjualan'
                    // },
                    // Tambahkan kolom aksi di bawah ini
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });
        });

        function simpanmasterbarang() {
            Swal.fire({
                title: "Anda yakin ?",
                text: "data barang akan disimpan !",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "simpan data barang ..."
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: "simpan data barang ?",
                        showDenyButton: true,
                        showCancelButton: false,
                        confirmButtonText: "Simpan",
                        denyButtonText: `Batal`
                    }).then((result) => {
                        if (result.isConfirmed) {
                            simpanbarang()
                        } else if (result.isDenied) {
                            Swal.fire("Changes are not saved", "", "info");
                        }
                    });
                }
            });
        }

        function editmasterbarang() {
            Swal.fire({
                title: "Anda yakin ?",
                text: "data barang akan diubah !",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "simpan edit data barang ..."
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: "simpan perubahan data barang ?",
                        showDenyButton: true,
                        showCancelButton: false,
                        confirmButtonText: "Simpan",
                        denyButtonText: `Batal`
                    }).then((result) => {
                        if (result.isConfirmed) {
                            simpaneditbarang()
                        } else if (result.isDenied) {
                            Swal.fire("Changes are not saved", "", "info");
                        }
                    });
                }
            });
        }

        function simpaneditbarang() {
            spinner_on()
            var data = $('.formeditmasterbarang').serializeArray();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    _token: "{{ csrf_token() }}",
                    data: JSON.stringify(data)
                },
                url: '<?= route('simpaneditbarang') ?>',
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
                            footer: ''
                        })
                        $('#modaleditbarang').modal('toggle');
                        document.getElementById("formeditmasterbarang").reset();
                        location.reload()
                    }
                }
            });
        }

        function simpanbarang() {
            spinner_on()
            var data = $('.formmasterbarang').serializeArray();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    _token: "{{ csrf_token() }}",
                    data: JSON.stringify(data)
                },
                url: '<?= route('simpanbarang') ?>',
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
                            footer: ''
                        })
                        $('#modal_add_master').modal('toggle');
                        document.getElementById("formmasterbarang").reset();
                        location.reload()
                    }
                }
            });
        }
        $(document).on('click', '.infosediaan', function() {
            spinner_on()
            idbarang = $(this).attr('idbarang')
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    _token: "{{ csrf_token() }}",
                    idbarang
                },
                url: '<?= route('ambildetailsediaanbarang') ?>',
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
                        spinner_off()
                        $('.v_sediaanbarang').html(data.html);
                    }
                }
            });
        });
        $(document).on('click', '.editharga', function() {
            spinner_on()
            idbarang = $(this).attr('idbarang')
            hargajual = $(this).attr('hargajual')
            hargajual2 = $(this).attr('hargajual2')
            $('#idbarangharga').val(idbarang)
            $('#hargajual').val(hargajual2)
            $('#hargajualasli').val(hargajual)
            spinner_off()

            // Fungsi Format Ribuan
            function formatRupiah(angka) {
                let number_string = angka.replace(/[^,\d]/g, '').toString(),
                    split = number_string.split(','),
                    sisa = split[0].length % 3,
                    rupiah = split[0].substr(0, sisa),
                    ribuan = split[0].substr(sisa).match(/\d{3}/gi);

                if (ribuan) {
                    let separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }

                return split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            }

            // Event Delegation: Memantau class .input-mask-uang meski baru ditambahkan
            $(document).on('keyup', '.input-mask-uang', function() {
                let nilai = $(this).val();

                // 1. Format tampilan (tambah titik)
                $(this).val(formatRupiah(nilai));

                // 2. Ambil angka bersih (tanpa titik)
                let angkaBersih = nilai.replace(/\./g, '');

                // 3. Masukkan ke hidden input di sebelahnya agar terkirim ke server
                $(this).siblings('.nilai-asli').val(angkaBersih);
            });
        });

        function simpanhargajual() {
            spinner_on()
            idbarang = $('#idbarangharga').val()
            harga = $('#hargajualasli').val()
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    _token: "{{ csrf_token() }}",
                    idbarang,
                    harga
                },
                url: '<?= route('simpanhargajual') ?>',
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
                            footer: ''
                        })
                        location.reload()
                    }
                }
            });
        }
    </script>
@endsection
