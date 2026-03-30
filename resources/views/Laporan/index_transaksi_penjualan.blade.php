@extends('Template.Main')
@section('container')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Laporan Transaksi Penjualan</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Laporan Transaksi Penjualan</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="app-content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">Tentukan Range Tanggal</div>
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
                                <input type="date" class="form-control" id="tanggalakhir" aria-describedby="emailHelp"
                                    value="{{ $date_end }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-success" style="margin-top:32px" onclick="ambildatalog()"><i
                                    class="bi bi-search" style="margin-right:12px"></i> Tampilkan Riwayat</button>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">Tabel Transaksi Penjualan</div>
                        <div class="card-body">
                            <div id="vtr" class="vtr">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function() {
            ambildatalog()
        })

        function ambildatalog() {
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
                url: '<?= route('ambillaporantransaksipenjualan') ?>',
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
                            timer: 1500, // Agar Swal menutup otomatis
                            showConfirmButton: false
                        })

                        // Gunakan .html() bukan .val() untuk merender HTML di dalam elemen
                        // Pastikan .v_d_t_r adalah class milik <tbody> tabel Anda
                        $('.vtr').html(data.view);
                    }
                }
            });
        }
    </script>
@endsection
