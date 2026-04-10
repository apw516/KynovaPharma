@extends('Template.Main')
@section('container')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Stok Opname</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Stok Opname</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="app-content">
        <div class="container-fluid">
            <div class="col-md-6">
                <div class="alert alert-info border-0 shadow-sm d-flex align-items-center py-3 mb-0 animate-pulse-red"
                    role="alert">
                    <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                        style="width: 45px; height: 45px;">
                        <i class="bi bi-exclamation-octagon-fill fs-4"></i>
                    </div>
                    <div>
                        <h6 class="alert-heading fw-bold mb-1">Perhatian: Stok Opname</h6>
                        <p class="mb-0 small"> Menu ini digunakan untuk koreksi stok live dengan stok didalam sistem, atau
                            untuk memasukan stok tanpa melalui PO ...</a>
                        </p>
                    </div>
                </div>
            </div>
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
            <div class="card mt-3">
                <div class="card-header bg-primary text-white">Daftar Penyesuaian Stok</div>
                <div class="card-body">
                    <form id="formTransaksiStok" action="" method="POST" class="formTransaksiStok">
                        @csrf
                        <table class="table table-bordered" style="font-size: 12px" id="tabel-input-stok">
                            <thead>
                                <tr>
                                    <th>Barang</th>
                                    <th width="100px">Satuan besar</th>
                                    <th width="100px">Satuan sedang</th>
                                    <th width="100px">Satuan kecil</th>
                                    <th width="100px">Rasio Sedang</th>
                                    <th width="100px">Rasio Kecil</th>
                                    <th width="100px">Harga Modal</th>
                                    <th width="100px">Harga Jual</th>
                                    <th width="100px">NO BATCH</th>
                                    <th width="110px">ED</th>
                                    <th width="100px">Stok (B)</th>
                                    <th width="100px">Stok (S)</th>
                                    <th width="100px">Stok (K)</th>
                                    <th width="80px">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="list-input-barang">
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-success float-end" onclick="simpandata()">Simpan Semua
                            Data</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div style="display: none;">
        <select id="master-sediaan-list">
            <option value="">-- Pilih Sediaan --</option>
            @foreach ($mt_sediaan as $sediaan)
                <option value="{{ $sediaan->kode_satuan }}">{{ $sediaan->nama_satuan }}</option>
            @endforeach
        </select>
    </div>
    <script type="text/javascript">
        function simpandata() {
            spinner_on()
            var data = $('.formTransaksiStok').serializeArray();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    _token: "{{ csrf_token() }}",
                    data: JSON.stringify(data)
                },
                url: '<?= route('simpandatainject') ?>',
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
        $(function() {
            var table = $('#table-barang').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 5, // Mengatur jumlah baris default menjadi 5
                lengthMenu: [5, 10, 25, 50],
                ajax: "{{ route('getdatabarang_opname') }}",
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
        $(document).on('click', '.pilihbarang', function() {
            let id = $(this).attr('idbarang');
            let kode = $(this).data('kode');
            let nama = $(this).data('nama');
            let rsedang = $(this).data('rsedang');
            let rkecil = $(this).data('rkecil');

            // Data Satuan
            let sbesar = $(this).data('sbesar');
            let ssedang = $(this).data('ssedang');
            let skecil = $(this).data('skecil');

            let masterSediaanHTML = $('#master-sediaan-list').html();

            // Fungsi pembantu untuk membuat template select
            const createSelect = (name, currentValue) => {
                return `
            <select name="${name}" class="form-select form-select-sm select-sediaan" data-value="${currentValue || ''}" required>
                ${masterSediaanHTML}
            </select>`;
            };

            let barisBaru = `
        <tr id="row-${id}">
            <td>
                <input type="hidden" name="idbarang" value="${id}">
                <strong>${nama}</strong><br>
                <small class="text-muted">${kode}</small>
            </td>
            <td>${createSelect(`satuanbesar`, sbesar)}</td>
            <td>${createSelect(`satuansedang`, ssedang)}</td>
            <td>${createSelect(`satuankecil`, skecil)}</td> 
            <td><input type="text" name="rasiosedang" class="form-control form-control-sm" value="${rsedang}"></td> 
            <td><input type="text" name="rasiokecil" class="form-control form-control-sm" value="${rkecil}"></td> 
            <td>
                <input type="text" name="hargamodal" class="form-control form-control-sm input-mask-uang" value="">
                <input hidden type="text" name="hargamodalasli" class="form-control form-control-sm nilai-asli" value="">                
            </td> 
            <td>
                <input type="text" name="hargajual" class="form-control form-control-sm input-mask-uang" value="">
                <input hidden type="text" name="hargajualasli" class="form-control form-control-sm nilai-asli" value="">
            </td> 
            <td><input type="text" name="nobatch" class="form-control form-control-sm" value=""></td> 
            <td><input type="date" name="ed" class="form-control form-control-sm" value=""></td> 
            <td><input type="text" name="stokb" class="form-control form-control-sm" value="0"></td> 
            <td><input type="text" name="stoks" class="form-control form-control-sm" value="0"></td> 
            <td><input type="text" name="stokk" class="form-control form-control-sm" value="0"></td> 
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm btn-hapus-baris">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        </tr>
    `;

            // 1. Tambahkan ke tabel
            let $row = $(barisBaru);
            $('#list-input-barang').append($row);

            // 2. Automatis pilih (set selected) berdasarkan data-value
            $row.find('.select-sediaan').each(function() {
                let val = $(this).data('value');
                if (val) {
                    $(this).val(val);
                }
            });
        });

        $(document).ready(function() {
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
        })

        // 4. Fungsi Hapus Baris
        $(document).on('click', '.btn-hapus-baris', function() {
            $(this).closest('tr').remove();
        });
    </script>
@endsection
