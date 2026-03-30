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
                                {{-- <th>Aksi</th> --}}
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
                // 'B' adalah indikator untuk Buttons, 'f' untuk filter/search, 'l' untuk length
                dom: "<'row mb-3'<'col-md-4'l><'col-md-8 d-flex justify-content-end align-items-center'f B>>" +
                    "<'row'<'col-md-12'tr>>" +
                    "<'row mt-3'<'col-md-5'i><'col-md-7'p>>",
                buttons: [{
                        extend: 'excelHtml5',
                        text: '<i class="bi bi-file-earmark-excel me-1"></i> Excel',
                        className: 'btn btn-success btn-sm border-0 shadow-sm mx-1',
                        title: 'Data Master Barang KynovaPharma',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        text: '<i class="bi bi-file-earmark-pdf me-1"></i> PDF',
                        className: 'btn btn-danger btn-sm border-0 shadow-sm mx-1',
                        orientation: 'landscape', // Landscape karena kolom sangat banyak
                        pageSize: 'A4',
                        title: 'Data Master Barang KynovaPharma',
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
                        searchable: false,
                        orderable: false,
                        render: function(data) {
                            return data ? data : '0';
                        }
                    },
                    {
                        data: 'harga_jual',
                        name: 'harga_jual',
                        render: function(data, type, row) {
                            if (data == null) return 'Rp 0';
                            return 'Rp ' + parseFloat(data).toFixed(0).replace(
                                /(\d)(?=(\d{3})+(?!\d))/g, '$1.');
                        }
                    }
                ],
                language: {
                    search: "",
                    searchPlaceholder: "Cari barang...",
                    lengthMenu: "_MENU_ data per halaman"
                }
            });

            // Merapikan input search agar sesuai tema
            $('.dataTables_filter input').addClass('form-control border-0 bg-light shadow-none px-3 py-2');
        });
    </script>
@endsection
