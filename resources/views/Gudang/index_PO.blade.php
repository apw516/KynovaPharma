@extends('Template.Main')
@section('container')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Data Purchase Order</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Data Purchase Order</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="app-content">
        <div class="container-fluid">
            <div class="v_1">
                <button type="button" class="btn btn-success mb-2" onclick="getform()"><i class="bi bi-box-arrow-in-right"
                        style="margin-right:4px"></i> Buat PO</button>
                <div class="card mt-3">
                    <div class="card-header">Riwayat Purchase Order</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="exampleInputEmail1" class="form-label">Tanggal Awal</label>
                                    <input type="date" class="form-control" id="tanggalawal" aria-describedby="emailHelp"
                                        value="{{ $date_start }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="exampleInputEmail1" class="form-label">Tanggal Akhir</label>
                                    <input type="date" class="form-control" id="tanggalakhir"
                                        aria-describedby="emailHelp" value="{{ $date_end }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-success" style="margin-top:32px" onclick="ambildatapo()"><i
                                        class="bi bi-search" style="margin-right:12px"></i> Tampilkan Riwayat</button>
                            </div>
                        </div>
                        <div class="v_d_t_r" class="mt-2">
                        </div>
                    </div>
                </div>
            </div>
            <div hidden class="v_2">
                <button type="button" class="btn btn-danger mb-3" onclick="location.reload()"><i class="bi bi-backspace"
                        style="margin-right:4px"></i> Kembali</button>
                <div class="card">
                    <div class="card-header">Form Purchase Order</div>
                    <div class="card-body">
                        <form action="" class="formsupplier">
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="row g-3 align-items-center">
                                        <div class="col-sm-3">
                                            <label for="inputPassword6" class="col-form-label">Nama Supplier</label>
                                        </div>
                                        <div class="col-md-5">
                                            <input type="text" id="namasupplier" name="namasupplier" class="form-control"
                                                aria-describedby="passwordHelpInline" placeholder="Pilih Supplier">
                                        </div>
                                        <div class="col-md-2">
                                            <input readonly type="text" id="kodesupplier" name="kodesupplier"
                                                class="form-control" aria-describedby="passwordHelpInline" placeholder="-">
                                        </div>
                                    </div>
                                    <div class="row g-3 align-items-center mt-1">
                                        <div class="col-sm-3">
                                            <label for="inputPassword6" class="col-form-label">Nomor Telepon</label>
                                        </div>
                                        <div class="col-md-7">
                                            <input type="text" id="telepon" name="telepon" class="form-control"
                                                aria-describedby="passwordHelpInline"
                                                placeholder="Masukan nomor telpon supplier ...">
                                        </div>
                                    </div>
                                    <div class="row g-3 align-items-center mt-1">
                                        <div class="col-sm-3">
                                            <label for="inputPassword6" class="col-form-label">Email</label>
                                        </div>
                                        <div class="col-md-5">
                                            <input type="text" id="email" name="email" class="form-control"
                                                aria-describedby="passwordHelpInline"
                                                placeholder="Masukan email supplier ...">
                                        </div>
                                    </div>
                                    <div class="row g-3 align-items-center mt-1">
                                        <div class="col-sm-3">
                                            <label for="inputPassword6" class="col-form-label">Alamat</label>
                                        </div>
                                        <div class="col-md-7">
                                            <textarea type="text" id="alamat" name="alamat" class="form-control" aria-describedby="passwordHelpInline"
                                                placeholder="Masukan Alamat supplier ..."></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row g-3 align-items-center">
                                        <div class="col-sm-4">
                                            <label for="inputPassword6" class="col-form-label float-end">Tanggal Faktur /
                                                Nomor Faktur</label>
                                        </div>
                                        <div class="col-md-3">
                                            <input type="date" id="tanggalfaktur" name="tanggalfaktur"
                                                class="form-control" aria-describedby="passwordHelpInline"
                                                placeholder="Pilih Supplier">
                                        </div>
                                        <div class="col-md-5">
                                            <input type="text" id="nomorfaktur" name="nomorfaktur"
                                                class="form-control" aria-describedby="passwordHelpInline"
                                                placeholder="Masukan Nomor Faktur ...">
                                        </div>
                                    </div>
                                    <div class="row g-1 align-items-center mt-2">
                                        <div class="col-sm-4">
                                            <label for="inputPassword6" class="col-form-label float-end">Tanggal
                                                Pembelian</label>
                                        </div>
                                        <div class="col-md-3">
                                            <input type="date" id="tanggalpembelian" name="tanggalpembelian"
                                                class="form-control" aria-describedby="passwordHelpInline"
                                                placeholder="Pilih Supplier">
                                        </div>
                                        <div class="col-md-5">

                                        </div>
                                    </div>
                                    <div class="row g-3 align-items-center mt-1">
                                        <div class="col-sm-4">
                                            <label for="inputPassword6" class="col-form-label float-end">Jenis
                                                Pembayaran</label>
                                        </div>
                                        <div class="col-md-4">
                                            <select class="form-select" aria-label="Default select example"
                                                id="jenispembayaran" name="jenispembayaran">
                                                <option selected>- Pilih Jenis Pembayaran -</option>
                                                <option value="Tunai">Tunai</option>
                                                <option value="Kredit">Kredit</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <input type="date" id="tanggalpembayaran" name="tanggalpembayaran"
                                                class="form-control" aria-describedby="passwordHelpInline"
                                                placeholder="Masukan Nomor Faktur ..." style="margin-top:24px">
                                            <div id="emailHelp" class="form-text text-muted fw-bold">Tentukan tanggal
                                                pembayaran ...</div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </form>
                        <div class="btn-group mt-4" role="group" aria-label="Basic outlined example">
                            <button type="button" id="btnproses" class="btn btn-primary" onclick="prosespo()"><i
                                    class="bi bi-file-arrow-up" style="margin-right: 3px"></i> Proses</button>
                            <button type="button" id="btnreset" class="btn btn-warning" onclick="reset()"><i
                                    class="bi bi-arrow-clockwise" style="margin-right: 3px"></i> Reset</button>
                            <button disabled id="btnsimpan" type="button" class="btn btn-success"
                                onclick="simpanpo()"><i class="bi bi-floppy" style="margin-right: 3px"></i>
                                Simpan</button>
                        </div>
                        <form action="" class="formheader" id="formheader">
                            <div class="row mt-1">
                                <div class="col-md-5 mt-3">
                                    <div class="row g-3 align-items-center">
                                        <div class="col-sm-3">
                                            <label for="inputPassword6" class="col-form-label">Total</label>
                                        </div>
                                        <div class="col-md-7">
                                            <input readonly type="text" id="totalproses" name="totalproses"
                                                class="form-control" aria-describedby="passwordHelpInline"
                                                placeholder="Total ...">
                                            <input hidden readonly type="text" id="totalprosesasli"
                                                name="totalprosesasli" class="form-control"
                                                aria-describedby="passwordHelpInline" placeholder="Total ...">
                                        </div>
                                    </div>
                                    <div class="row g-3 align-items-center mt-1">
                                        <div class="col-sm-3">
                                            <label for="inputPassword6" class="col-form-label">Diskon</label>
                                        </div>
                                        <div class="col-md-3">
                                            <input type="text" id="diskonlobalpersen" name="diskonlobalpersen"
                                                class="form-control" aria-describedby="passwordHelpInline"
                                                placeholder="0.00 %" value="0">
                                        </div>
                                        <div class="col-md-3">
                                            <input type="text" id="diskonglobalrupiah" name="diskonglobalrupiah"
                                                class="form-control" aria-describedby="passwordHelpInline"
                                                placeholder="0.00" value="0.00">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row g-3 align-items-center mt-1">
                                        <div class="col-sm-1">
                                            <label for="inputPassword6" class="col-form-label float-end">Pajak</label>
                                        </div>
                                        <div class="col-md-3">
                                            <input type="text" id="pajakglobalpersen" name="pajakglobalpersen"
                                                class="form-control" aria-describedby="passwordHelpInline"
                                                placeholder="0.00 %" value="0">
                                        </div>
                                        <div hidden class="col-md-3">
                                            <input type="text" id="pajakglobalrupiah" name="pajakglobalrupiah"
                                                class="form-control" aria-describedby="passwordHelpInline"
                                                placeholder="0.00" value="0.00">
                                        </div>
                                    </div>
                                    <div class="row g-3 align-items-center mt-1">
                                        <div class="col-sm-1">
                                            <label for="inputPassword6" class="col-form-label float-end">Biaya</label>
                                        </div>
                                        <div class="col-md-6">
                                            <input readonly type="text" id="totalbiaya" name="totalbiaya"
                                                class="form-control" aria-describedby="passwordHelpInline"
                                                placeholder="0.00">
                                            <input hidden readonly type="text" id="totalbiayaasli"
                                                name="totalbiayaasli" class="form-control"
                                                aria-describedby="passwordHelpInline" placeholder="0.00">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="col-md-12 mt-2">
                            <div class="card border-primary mb-3" style="max-width: 100%;">
                                <div class="card-header bg-transparent border-primary">
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" placeholder="Ketik Nama Obat ..."
                                            aria-label="Recipient’s username" aria-describedby="basic-addon2"
                                            id="carinamaobat" name="carinamaobat">
                                        <button class="input-group-text btn btn-primary btncariobat" id="btncariobat"
                                            data-bs-toggle="modal" data-bs-target="#modalhasilpencarianobat"
                                            onclick="cariobat()"><i class="bi bi-search" style="margin-right: 3px"></i>
                                            Cari Obat</button>
                                    </div>
                                </div>
                                <div class="card-body text-primary">
                                    <form action="/simpan-transaksi" method="POST" class="formobat" id="formobat">
                                        @csrf
                                        <h5>Daftar Obat Terpilih</h5>
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Nama Obat</th>
                                                    <th>Jumlah</th>
                                                    <th>Satuan</th>
                                                    <th>Hrg Beli</th>
                                                    <th>Diskon ( % )</th>
                                                    <th>Diskon ( 0.00)</th>
                                                    <th>No. Batch</th>
                                                    <th>Expired Date</th>
                                                    <th>-</th>
                                                </tr>
                                            </thead>
                                            <tbody id="wrapper-form-obat">
                                            </tbody>
                                        </table>
                                        {{-- <button type="submit" class="btn btn-primary">Simpan Semua</button> --}}
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="modalhasilpencarianobat" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Silahkan Pilih Obat</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="v_tb_obat">

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        function getform() {
            $('.v_2').removeAttr('Hidden', true)
            $('.v_1').attr('Hidden', true)
        }
        $(document).ready(function() {
            ambildatapo()
            $("#namasupplier").autocomplete({
                source: function(request, response) {
                    $.ajax({
                        url: "{{ route('cari-supplier') }}", // Ganti dengan route Anda
                        data: {
                            term: request.term
                        },
                        dataType: "json",
                        success: function(data) {
                            response(data);
                        }
                    });
                },
                minLength: 2, // Minimal ketik 2 huruf baru mencari
                select: function(event, ui) {
                    // Saat obat dipilih, masukkan label ke input dan ID ke hidden input
                    $("#namasupplier").val(ui.item.label);
                    $("#kodesupplier").val(ui.item.id);
                    $("#telepon").val(ui.item.telp);
                    $("#email").val(ui.item.email);
                    $("#alamat").val(ui.item.alamat);
                    return false;
                }
            });
        });

        function cariobat() {
            spinner_on()
            nama = $('#carinamaobat').val()
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    _token: "{{ csrf_token() }}",
                    nama
                },
                url: '<?= route('cariobat') ?>',
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
                        $('.v_tb_obat').html(data.html);
                    }
                }
            });
        }

        function prosespo() {
            spinner_on()
            var data = $('.formobat').serializeArray();
            var data2 = $('.formheader').serializeArray();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    _token: "{{ csrf_token() }}",
                    data: JSON.stringify(data),
                    data2: JSON.stringify(data2)
                },
                url: '<?= route('prosespo') ?>',
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
                        $('#totalproses').val(data.view)
                        $('#totalprosesasli').val(data.grandtotal)
                        $('#totalbiaya').val(data.total_biaya_disp)
                        $('#totalbiayaasli').val(data.total_biaya)
                        offday()
                    }
                }
            });
        }

        function reset() {
            $('#diskonlobalpersen').removeAttr('readonly', true)
            $('#diskonglobalrupiah').removeAttr('readonly', true)
            $('#pajakglobalpersen').removeAttr('readonly', true)
            $('#pajakglobalrupiah').removeAttr('readonly', true)
            $('#btncariobat').removeAttr('disabled', true)
            $('#btnsimpan').attr('disabled', true)
            $('#formobat').find('input, select, textarea').removeAttr('readonly', true);
            $('#formobat').find('button').removeAttr('disabled', true)
            Swal.fire({
                icon: 'success',
                title: 'OK',
                text: "Form berhasil direset ...",
                footer: ''
            })
        }

        function offday() {
            $('#diskonlobalpersen').attr('readonly', true)
            $('#diskonglobalrupiah').attr('readonly', true)
            $('#pajakglobalpersen').attr('readonly', true)
            $('#pajakglobalrupiah').attr('readonly', true)
            $('#btncariobat').attr('disabled', true)
            $('#btnsimpan').removeAttr('disabled', true)
            $('#formobat').find('input, select, textarea').prop('readonly', true);
            $('#formobat').find('button').prop('disabled', true)
        }

        function simpanpo() {
            Swal.fire({
                title: "Data PO akan disimpan ?",
                text: "Pastikan data sudah diisi dengan benar ...",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Simpan"
            }).then((result) => {
                if (result.isConfirmed) {
                    save()
                }
            });
        }

        function save() {
            spinner_on()
            var data = $('.formobat').serializeArray();
            var data2 = $('.formheader').serializeArray();
            var data3 = $('.formsupplier').serializeArray();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    _token: "{{ csrf_token() }}",
                    data: JSON.stringify(data),
                    data2: JSON.stringify(data2),
                    data3: JSON.stringify(data3)
                },
                url: '<?= route('savepo') ?>',
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
                        $('.formobat')[0].reset();
                        $('.formheader')[0].reset();
                        $('.formsupplier')[0].reset();
                        location.reload()
                    }
                }
            });
        }

        function ambildatapo() {
            tglawal = $('#tanggalawal').val()
            tglakhir = $('#tanggalakhir').val()
            spinner_on()
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    _token: "{{ csrf_token() }}",
                    tglawal,
                    tglakhir
                },
                url: '<?= route('ambildatapo') ?>',
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
                        $('.v_d_t_r').html(data.view);
                    }
                }
            });
        }
    </script>
@endsection
