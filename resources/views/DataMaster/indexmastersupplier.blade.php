@extends('Template.Main')
@section('container')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Master Supplier</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Master Supplier</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="app-content">
        <div class="container-fluid">
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modal_add_supplier"><i
                    class="bi bi-folder-plus" style="margin-right:4px"></i> Master
                Supplier</button>
            <div class="card mt-3">
                <div class="card-header">Tabel Master Supplier</div>
                <div class="card-body">
                    <table class="table table-bordered table-hover" id="table-supplier">
                        <thead>
                            <tr>
                                <th width="5%">Kode Supplier</th>
                                <th>Nama Supplier</th>
                                <th>Nomor Telepon</th>
                                <th>Email</th>
                                <th width="30%">Alamat</th>
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
    <!-- Modal -->
    <div class="modal fade" id="modal_add_supplier" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Tambah Master Supplier</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="formaddsupplier" id="formaddsupplier">
                        <div class="mb-3">
                            <label for="exampleInputEmail1" class="form-label">Nama Supplier</label>
                            <input type="texts" class="form-control" id="nama_supplier" name="nama_supplier"
                                placeholder="Masukan nama supplier ..." aria-describedby="emailHelp">
                        </div>
                        <div class="mb-3">
                            <label for="exampleInputPassword1" class="form-label">Telepon</label>
                            <input type="text" class="form-control" id="telepon" name="telepon"
                                placeholder="Masukan nomor telepon supplier ...">
                        </div>
                        <div class="mb-3">
                            <label for="exampleInputPassword1" class="form-label">Email</label>
                            <input type="text" class="form-control" id="email" name="email"
                                placeholder="Masukan email supplier ..." value="-">
                        </div>
                        <div class="mb-3">
                            <label for="exampleInputPassword1" class="form-label">Alamat</label>
                            <textarea type="text" class="form-control" id="alamat" name="alamat" placeholder="Masukan alamat supplier ..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" onclick="addsupplier()">Simpan</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="modaleditsupplier" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Edit Master Supplier</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="v_editsupplier">

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" onclick="editmastersupplier()">Simpan</button>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(function() {
            var table = $('#table-supplier').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('getdatasupplier') }}",
                columns: [{
                        data: 'kode_supplier',
                        name: 'kode_supplier',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nama_supplier',
                        name: 'nama_supplier'
                    },
                    {
                        data: 'telepon',
                        name: 'telepon'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'alamat',
                        name: 'alamat'
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
        $(document).on('click', '.hapussupplier', function() {
            idsupplier = $(this).attr('idsupplier')
            namasupplier = $(this).attr('namasupplier')
            Swal.fire({
                title: "Anda yakin ?",
                text: "data supplier dengan nama " + namasupplier + " akan dihapus !",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "hapus data supplier ..."
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: "hapus data supplier ?",
                        showDenyButton: true,
                        showCancelButton: false,
                        confirmButtonText: "Simpan",
                        denyButtonText: `Batal`
                    }).then((result) => {
                        if (result.isConfirmed) {
                            hapussupplier(idsupplier)
                        } else if (result.isDenied) {
                            Swal.fire("Changes are not saved", "", "info");
                        }
                    });
                }
            });
        });
        $(document).on('click', '.editsupplier', function() {
            spinner_on()
            idsupplier = $(this).attr('idsupplier')
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    _token: "{{ csrf_token() }}",
                    idsupplier
                },
                url: '<?= route('formeditsupplier') ?>',
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
                        $('.v_editsupplier').html(data.html);
                    }
                }
            });
        });

        function addsupplier() {
            Swal.fire({
                title: "Anda yakin ?",
                text: "data supplier akan disimpan !",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "simpan data supplier ..."
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: "simpan data supplier ?",
                        showDenyButton: true,
                        showCancelButton: false,
                        confirmButtonText: "Simpan",
                        denyButtonText: `Batal`
                    }).then((result) => {
                        if (result.isConfirmed) {
                            simpansupplier()
                        } else if (result.isDenied) {
                            Swal.fire("Changes are not saved", "", "info");
                        }
                    });
                }
            });
        }

        function simpansupplier() {
            spinner_on()
            var data = $('.formaddsupplier').serializeArray();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    _token: "{{ csrf_token() }}",
                    data: JSON.stringify(data)
                },
                url: '<?= route('simpansupplier') ?>',
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
                        $('#modal_add_supplier').modal('toggle');
                        document.getElementById("formaddsupplier").reset();
                        location.reload()
                    }
                }
            });
        }

        function editmastersupplier() {
            Swal.fire({
                title: "Anda yakin ?",
                text: "data supplier akan diubah !",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "simpan edit data supplier ..."
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: "simpan perubahan data supplier ?",
                        showDenyButton: true,
                        showCancelButton: false,
                        confirmButtonText: "Simpan",
                        denyButtonText: `Batal`
                    }).then((result) => {
                        if (result.isConfirmed) {
                            simpaneditsupplier()
                        } else if (result.isDenied) {
                            Swal.fire("Changes are not saved", "", "info");
                        }
                    });
                }
            });
        }

        function simpaneditsupplier() {
            spinner_on()
            var data = $('.formeditsupplier').serializeArray();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    _token: "{{ csrf_token() }}",
                    data: JSON.stringify(data)
                },
                url: '<?= route('simpaneditsupplier') ?>',
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
                        $('#modaleditsupplier').modal('toggle');
                        document.getElementById("formeditsupplier").reset();
                        location.reload()
                    }
                }
            });
        }
          function hapussupplier(idsupplier) {
            spinner_on()
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    _token: "{{ csrf_token() }}",
                    idsupplier
                },
                url: '<?= route('hapussupplier') ?>',
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
