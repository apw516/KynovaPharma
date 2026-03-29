@extends('Template.Main')
@section('container')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Master Barang</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Master Barang</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="app-content">
        <div class="container-fluid">
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modal_add_master"><i
                    class="bi bi-folder-plus" style="margin-right:4px"></i> Master
                Barang</button>
            <div class="card mt-3">
                <div class="card-header">Tabel Master Barang</div>
                <div class="card-body">
                    <table class="table table-bordered table-hover" id="table-barang">
                        <thead>
                            <tr>
                                <th>Kode Barang</th>
                                <th>Kategori</th>
                                <th>Nama Barang</th>
                                <th>Produsen</th>
                                <th>Satuan Besar</th>
                                <th>Satuan Sedang</th>
                                <th>Satuan Kecil</th>
                                <th>Sediaan</th>
                                <th>Aturan Pakai</th>
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
    <div class="modal fade" id="modal_add_master" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Tambah Master Barang</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="formmasterbarang" id="formmasterbarang">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="exampleInputEmail1" class="form-label">Kategori</label>
                                    <input type="text" placeholder="Masukan nama barang ..." class="form-control"
                                        id="namabarang" name="namabarang" aria-describedby="emailHelp">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="exampleInputEmail1" class="form-label">Nama Barang</label>
                                    <input type="text" placeholder="Masukan nama merk dagang ..." class="form-control"
                                        id="merkdagang" name="merkdagang" aria-describedby="emailHelp">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="exampleInputEmail1" class="form-label">Produsen</label>
                                    <input type="text" placeholder="Masukan nama produsen ..." class="form-control"
                                        id="produsen" name="produsen" aria-describedby="emailHelp">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="exampleInputEmail1" class="form-label">Satuan Besar</label>
                                    <select class="form-select" aria-label="Default select example" id="satuanbesar"
                                        name="satuanbesar">
                                        <option selected>- Silahkan Pilih - </option>
                                        @foreach ($satuan as $s)
                                            <option value="{{ $s->kode_satuan }}">{{ $s->nama_satuan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="exampleInputEmail1" class="form-label">Satuan Sedang</label>
                                    <select class="form-select" aria-label="Default select example" id="satuansedang"
                                        name="satuansedang">
                                        <option selected>- Silahkan Pilih - </option>
                                        @foreach ($satuan as $s)
                                            <option value="{{ $s->kode_satuan }}">{{ $s->nama_satuan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="exampleInputEmail1" class="form-label">Satuan Kecil</label>
                                    <select class="form-select" aria-label="Default select example" id="satuankecil"
                                        name="satuankecil">
                                        <option selected>- Silahkan Pilih - </option>
                                        @foreach ($satuan as $s)
                                            <option value="{{ $s->kode_satuan }}">{{ $s->nama_satuan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="exampleInputEmail1" class="form-label">Sediaan</label>
                                    <select class="form-select" aria-label="Default select example" id="sediaan"
                                        name="sediaan">
                                        <option selected>- Silahkan Pilih - </option>
                                        @foreach ($satuan as $s)
                                            <option value="{{ $s->kode_satuan }}">{{ $s->nama_satuan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-5">
                                <div class="mb-3">
                                    <label for="exampleInputEmail1" class="form-label">Rasio Satuan Besar Ke Satuan
                                        Sedang</label>
                                    <input type="text" placeholder="Masukan rasio sedang ..." class="form-control"
                                        id="rasiosatuansedang" name="rasiosatuansedang" aria-describedby="emailHelp">
                                    <div id="emailHelp" class="form-text">Contoh: 10 (1 Box isi 10 Strip)
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="mb-3">
                                    <label for="exampleInputEmail1" class="form-label">Rasio Satuan Besar Ke Satuan
                                        Kecil</label>
                                    <input type="text" placeholder="Masukan rasio kecil ..." class="form-control"
                                        id="rasiosatuankecil" name="rasiosatuankecil" aria-describedby="emailHelp">
                                    <div id="emailHelp" class="form-text">Contoh: 10 (1 Strip isi 10 Tablet)
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="exampleInputPassword1" class="form-label">Aturan Pakai</label>
                            <textarea rows="5" type="text" class="form-control" id="aturanpakai" name="aturanpakai"
                                placeholder="Masukan aturan pakai barang ..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" onclick="simpanmasterbarang()">Simpan</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="modaleditbarang" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Edit Master Barang</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="v_editbarang">

                    </div>                   
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" onclick="editmasterbarang()">Simpan</button>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(function() {
            var table = $('#table-barang').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('getdatabarang') }}",
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
        

        $(document).on('click', '.hapusbarang', function() {
            idbarang = $(this).attr('idbarang')
            namabarang = $(this).attr('namabarang')
            Swal.fire({
                title: "Anda yakin ?",
                text: "data barang " + namabarang + " akan dihapus !",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "hapus data barang ..."
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: "hapus data barang ?",
                        showDenyButton: true,
                        showCancelButton: false,
                        confirmButtonText: "Simpan",
                        denyButtonText: `Batal`
                    }).then((result) => {
                        if (result.isConfirmed) {
                            hapusbarang(idbarang)
                        } else if (result.isDenied) {
                            Swal.fire("Changes are not saved", "", "info");
                        }
                    });
                }
            });
        });

        $(document).on('click', '.editbarang', function() {
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
                url: '<?= route('formeditbarang') ?>',
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
                        $('.v_editbarang').html(data.html);
                    }
                }
            });
        });

        function hapusbarang(idbarang) {
            spinner_on()
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    _token: "{{ csrf_token() }}",
                    idbarang
                },
                url: '<?= route('hapusbarang') ?>',
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
