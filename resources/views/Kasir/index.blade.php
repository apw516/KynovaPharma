@extends('Template.Main')
@section('container')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Kasir</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Kasir</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="app-content">
        @if (count($get_sesi) == 0)
            <div class="container-fluid">
                Tidak ada sesi kasir yang dimulai ... <br>
                <button class="btn btn-success mt-2" data-bs-toggle="modal" data-bs-target="#modalsesikasir"><i
                        class="bi bi-node-plus" style="margin-right:4px"></i> Mulai Sesi Kasir</button>
            </div>
        @else
            <input hidden type="text" class="form-control" value="{{ $get_sesi[0]->id }}" id="id_sesi_kasir">
            ID Sesi Kasir : {{ $get_sesi[0]->id }} <br>
            Tanggal Sesi Kasi : {{ $get_sesi[0]->tgl_sesi_kasir }} <button style="margin-left:10px"
                class="btn btn-sm btn-secondary" onclick="tutupsesikasir()">Tutup</button>
        @endif
        <div @if (count($get_sesi) == 0) hidden @endif class="container-fluid">
            <div class="card mt-3">
                <div class="card-header">Silahkan Pilih Obat</div>
                <div class="card-body">
                    <table class="table table-bordered table-hover table-sm" id="table-barang">
                        <thead>
                            <tr>
                                <th>Kode Barang</th>
                                <th>Nama Barang</th>
                                {{-- <th>Merk Dagang</th>
                                <th>Produsen</th> --}}
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
                    <div class="card mt-2">
                        <div class="card-header">List Obat dipilih</div>
                        <div class="card-body">
                            <form class="formbarang" id="form-transaksi">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Nama Barang</th>
                                            <th>Satuan</th>
                                            <th>Qty</th>
                                            <th>Diskon ( Dalam rupiah )</th>
                                            <th>Harga ( Satuan terkecil )</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="wrapper-input-obat">
                                    </tbody>
                                </table>
                            </form>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-success prosesbarang"><i class="bi bi-box-arrow-in-down"
                                    style="margin-right:4px"></i> Proses</button>
                            <button class="btn btn-warning resetbarang"><i class="bi bi-arrow-clockwise"
                                    style="margin-right:4px"></i> Reset</button>
                            <div hidden class="v_proses mt-3"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="modalsesikasir" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Mulai Sesi Kasir</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="exampleFormControlInput1" class="form-label">Saldo Awal</label>
                        <input type="text" class="form-control input-mask-uang" id="saldoawal"
                            placeholder="name@example.com">
                        <input hidden type="text" class="form-control nilai-asli" id="saldoawalasli"
                            placeholder="name@example.com">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" onclick="simpansesikasir()">Simpan</button>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(function() {
            var table = $('#table-barang').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('getdatabarang3') }}",
                columns: [{
                        data: 'kode_barang',
                        name: 'kode_barang',
                        orderable: false,
                        searchable: false
                    },
                    {
                        // Kolom Gabungan: Nama, Merk, dan Produsen
                        data: 'nama_obat',
                        name: 'nama_obat',
                        render: function(data, type, row) {
                            return `
                        <div>
                            <strong class="text-primary">${row.nama_dagang}</strong><br>
                            <small class="text-muted">Generik: ${row.nama_obat ?? '-'}</small><br>
                            <small class="badge bg-light text-dark" style="font-size: 0.75rem;">${row.produsen ?? 'Tanpa Produsen'}</small>
                        </div>
                    `;
                        }
                    },
                    {
                        data: 'satuan_besar',
                        name: 'satuan_besar',
                        render: function(data, type, row) {
                            return `
                        <div>
                            <strong class="text-dark">${row.satuan_besar}</strong><br>
                            <small class="text-muted">ISI: ${row.rasio_sedang ?? '-'} ${row.satuan_sedang}</small><br>
                        
                        </div>
                    `;
                        }
                    },
                    {
                        data: 'satuan_sedang',
                        name: 'satuan_sedang',
                        render: function(data, type, row) {
                            return `
                        <div>
                            <strong class="text-dark">${row.satuan_sedang}</strong><br>
                            <small class="text-muted">ISI: ${row.rasio_kecil ?? '-'} ${row.satuan_kecil}</small><br>
                        </div>
                    `;
                        }
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
                        orderable: false, // Tambahkan ini agar tidak error saat di-klik judul kolomnya
                        render: function(data, type, row) {
                            let total = parseInt(data) || 0;
                            let rSedang = parseInt(row.rasio_sedang) || 1;
                            let rKecil = parseInt(row.rasio_kecil) || 1;

                            // Kapasitas 1 Box dalam satuan terkecil (Tablet)
                            let isiPerBox = rSedang * rKecil;
                            // Kapasitas 1 Strip dalam satuan terkecil (Tablet)
                            let isiPerStrip = rKecil;

                            // 1. Hitung Jumlah Box
                            let box = Math.floor(total / isiPerBox);
                            let sisaSetelahBox = total % isiPerBox;

                            // 2. Hitung Jumlah Strip
                            let strip = Math.floor(sisaSetelahBox / isiPerStrip);
                            let tablet = sisaSetelahBox % isiPerStrip;

                            // 3. Susun Tampilan
                            let hasil = [];
                            if (box > 0) hasil.push(
                                `<span class="badge bg-primary">${box} ${row.satuan_besar}</span>`
                            );
                            if (strip > 0) hasil.push(
                                `<span class="badge bg-info text-dark">${strip} ${row.satuan_sedang}</span>`
                            );
                            if (tablet > 0 || (box === 0 && strip === 0)) {
                                hasil.push(
                                    `<span class="badge bg-secondary">${tablet} ${row.satuan_kecil}</span>`
                                );
                            }

                            // Gabungkan dengan spasi atau koma
                            return `
            <div style="line-height: 1.6;">
                <small class="text-dark fw-bold d-block" style="font-size: 14px;">Total: ${total} ${row.satuan_kecil}</small>
                ${hasil.join(' ')}
            </div>
        `;
                        }
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
        $(document).ready(function() {
            // 1. Event Klik Tombol Pilih Barang
            $('#table-barang').on('click', '.pilihbarang', function() {
                // Ambil data dari atribut tombol
                let idBarang = $(this).attr('idbarang');
                let namaBarang = $(this).attr('namabarang');
                let kodebarang = $(this).attr('kodebarang');
                let harga = $(this).attr('harga');
                let aturanpakai = $(this).attr('aturanpakai');
                let generik = $(this).attr('generik');
                let merk = $(this).attr('merk');
                let produsen = $(this).attr('produsen');
                let daftarSatuan = $(this).attr('satuan') ? $(this).attr('satuan').split(',') : ['Pcs'];
                // Cek apakah barang sudah ada di list (agar tidak double)
                if ($(`#row-${idBarang}`).length > 0) {
                    Swal.fire('Info', 'Barang ini sudah dipilih', 'info');
                    return;
                }
                let opsiSatuan = '';
                daftarSatuan.forEach(item => {
                    // Memecah "besar:Box" menjadi ["besar", "Box"]
                    let part = item.split(':');
                    let kategori = part[0]; // besar / sedang / kecil
                    let namaSatuan = part[1]; // Box / Strip / Tablet

                    if (namaSatuan) {
                        // Value berisi kategori, teks tampil berisi nama satuan
                        opsiSatuan += `<option value="${kategori}">${namaSatuan}</option>`;
                    }
                });
                // 2. Bentuk Baris Inputan Baru
                let html = `
            <tr id="row-${idBarang}">
                <td>
                    <input type="hidden" name="id_obat" value="${idBarang}">
                    <input type="hidden" name="kode_barang" value="${kodebarang}">
                    <input type="hidden" name="nama_barang" value="${namaBarang}">
                    <input type="text" class="form-control form-control-sm" value="${merk} | ${generik} | ${produsen}" readonly>
                </td>
                <td width="15%">
                    <select name="satuan" class="form-select form-select-sm satuan">
                        ${opsiSatuan}
                    </select>
                </td>
                <td width="5%">
                    <input type="number" name="qty" class="form-control form-control-sm qty" value="1" min="1">
                </td>
                 <td width="15%">
                    <input type="number" name="diskontampilan" class="form-control form-control-sm input-mask-uang diskontampilan" value="0" min="0">
                    <input hidden type="number" name="diskon" class="form-control form-control-sm nilai-asli" value="0" min="0">
                </td>
                <td width="15%">
                    <input type="number" name="harga" class="form-control form-control-sm" placeholder="Harga..." value="${harga}" readonly>
                </td>
                
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-danger hapus-baris">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `;
                // 3. Masukkan ke dalam wrapper
                $('#wrapper-input-obat').append(html);

                // Tutup modal jika tabel barang berada di dalam modal
                $('#modalsediaan').modal('hide');
            });

            // 4. Fungsi Hapus Baris
            $('#wrapper-input-obat').on('click', '.hapus-baris', function() {
                $(this).closest('tr').remove();
            });
        });
        $('.prosesbarang').on('click', function() {
            Swal.fire({
                title: "Anda yakin ?",
                text: "Pastikan data sudah terisi dengan benar ... !",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, Proses!"
            }).then((result) => {
                if (result.isConfirmed) {
                    prosesbarang()
                }
            });
        })
        $('.resetbarang').on('click', function() {
            $('.prosesbarang').removeAttr('disabled', true);
            $('.v_proses').prop('hidden', true);
            $('.hapus-baris').removeAttr('disabled', true);
            $('.pilihbarang').removeAttr('disabled', true);
            $('select[name="satuan"]').css({
                "pointer-events": "auto",
                "background-color": "#ffffff", // Warna abu-abu seperti disabled
                "appearance": "none" // Menghilangkan panah dropdown (opsional)
            });
            $('.qty').removeAttr('readonly', true);
            $('.diskontampilan').removeAttr('readonly', true);
            // 4. Matikan tombol proses itu sendiri agar tidak diklik dua kali
            $(this).removeAttr('disabled', true);
        })

        function prosesbarang() {
            spinner_on()
            var data = $('.formbarang').serializeArray();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    _token: "{{ csrf_token() }}",
                    data: JSON.stringify(data)
                },
                url: '<?= route('prosesbarang') ?>',
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
                        $('.prosesbarang').prop('disabled', true);
                        $('.v_proses').removeAttr('hidden', true);
                        $('.hapus-baris').prop('disabled', true);
                        $('.pilihbarang').prop('disabled', true);
                        $('select[name="satuan"]').css({
                            "pointer-events": "none",
                            "background-color": "#e9ecef", // Warna abu-abu seperti disabled
                            "appearance": "none" // Menghilangkan panah dropdown (opsional)
                        });
                        $('.qty').prop('readonly', true);
                        $('.diskontampilan').prop('readonly', true);
                        // 4. Matikan tombol proses itu sendiri agar tidak diklik dua kali
                        $(this).prop('disabled', true);
                        $(this).html('<span class="spinner-border spinner-border-sm"></span> Memproses...');
                        spinner_off()
                        $('.v_proses').html(data.html);
                    }
                }
            });
        }
    </script>
    <script>
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
    </script>
    <script>
        function simpansesikasir() {
            saldo = $('#saldoawalasli').val()
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    _token: "{{ csrf_token() }}",
                    saldo
                },
                url: '<?= route('simpansesikasir') ?>',
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

        function tutupsesikasir() {
            Swal.fire({
                title: "Anda yakin ?",
                text: "Sesi Kasir akan ditutup...",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, tutup ..!"
            }).then((result) => {
                if (result.isConfirmed) {
                    tutup()
                }
            });
        }

        function tutup() {
            id = $('#id_sesi_kasir').val()
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    _token: "{{ csrf_token() }}",
                    id
                },
                url: '<?= route('tutupsesikasir') ?>',
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
@endsection
