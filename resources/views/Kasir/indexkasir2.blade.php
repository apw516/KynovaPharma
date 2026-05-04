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
        <div class="container-fluid mt-3 mb-3">
            <div class="row g-3">
                {{-- Alert Stok Expired (Tetap seperti sebelumnya) --}}
                @if ($notif_ed > 0)
                    <div class="col-md-6">
                        <div class="alert alert-danger border-0 shadow-sm d-flex align-items-center py-3 mb-0"
                            role="alert">
                            <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                style="width: 45px; height: 45px;">
                                <i class="bi bi-exclamation-octagon-fill fs-4"></i>
                            </div>
                            <div>
                                <h6 class="alert-heading fw-bold mb-1">Perhatian: Sediaan Kadaluwarsa</h6>
                                <p class="mb-0 small">
                                    Terdapat <strong>{{ $notif_ed }} item</strong> yang akan expired dalam waktu dekat (
                                    < 90 hari). <a href="{{ route('indexdatastokpersediaan') }}"
                                        class="fw-bold text-danger text-decoration-underline ms-1">Lihat Detail</a>
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
                {{-- Alert Hutang Gabungan --}}
                @if ($notif_hutang > 0)
                    <div class="col-md-6">
                        <div class="alert {{ $overdue_count > 0 ? 'alert-danger' : 'alert-warning' }} border-0 shadow-sm d-flex align-items-center py-3 mb-0"
                            role="alert">
                            <div class="{{ $overdue_count > 0 ? 'bg-danger' : 'bg-warning' }} text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                style="width: 45px; height: 45px;">
                                <i
                                    class="bi {{ $overdue_count > 0 ? 'bi-exclamation-triangle-fill' : 'bi-cash-stack' }} fs-4"></i>
                            </div>
                            <div>
                                <h6 class="alert-heading fw-bold mb-1">
                                    {{ $overdue_count > 0 ? 'Peringatan: Hutang Menunggak!' : 'Peringatan: Tagihan PO' }}
                                </h6>
                                <p class="mb-0 small">
                                    Terdapat <strong>{{ $notif_hutang }} tagihan</strong> belum lunas.
                                    @if ($overdue_count > 0)
                                        <span class="badge bg-white text-danger ms-1">{{ $overdue_count }} Lewat Jatuh
                                            Tempo</span>
                                    @endif
                                    <a href="{{ route('indexpurchaseorder') }}"
                                        class="fw-bold text-decoration-underline ms-1 text-dark">Selesaikan Pembayaran</a>
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        @if (count($get_sesi) == 0)
            <div class="container-fluid">
                Tidak ada sesi kasir yang dimulai ... <br>
                <button class="btn btn-success mt-2" data-bs-toggle="modal" data-bs-target="#modalsesikasir"><i
                        class="bi bi-node-plus" style="margin-right:4px"></i> Mulai Sesi Kasir</button>
            </div>
        @else
            <div class="container-fluid">
                <input hidden type="text" class="form-control" value="{{ $get_sesi[0]->id }}" id="id_sesi_kasir">
                ID Sesi Kasir : {{ $get_sesi[0]->id }} <br>
                Tanggal Sesi Kasi : {{ $get_sesi[0]->tgl_sesi_kasir }} <button style="margin-left:10px"
                    class="btn btn-sm btn-secondary" onclick="tutupsesikasir()">Tutup</button>
            </div>
        @endif
        <div @if (count($get_sesi) == 0) hidden @endif class="container-fluid">
            <div class="card mt-3">
                <div class="card-header">Silahkan Pilih Obat</div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Scan Barcode / Input Nomor Batch</label>
                        <input type="text" id="scanBatch" class="form-control" placeholder="Arahkan scanner ke sini..."
                            autofocus>
                    </div>
                    <table class="table table-bordered mt-4" id="tablePembelian">
                        <thead>
                            <tr>
                                <th>Nama Barang</th>
                                <th>Nomor Batch</th>
                                <th>Satuan</th>
                                <th width="150px">Qty</th>
                                <th>Satuan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    <div id="pembayaranSection" class="mt-4" style="display:none;">
                        <div class="card border-primary shadow">
                            <div class="card-header bg-primary text-white font-weight-bold">Ringkasan Pembayaran</div>
                            <div class="card-body">
                                <table class="table table-sm">
                                    <tbody id="detailHitung"></tbody>
                                </table>
                                <hr>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h5 class="mb-0">TOTAL AKHIR :</h5>
                                        <h3 class="text-primary fw-bold" id="totalAkhirText">Rp 0</h3>
                                        <input type="hidden" id="totalAkhirValue">
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="fw-bold">Uang Bayar (Tunai)</label>
                                            <input type="text" id="uangBayar"
                                                class="form-control form-control-lg input-mask-uang text-end"
                                                placeholder="0">
                                            <input type="hidden" id="uangBayarAsli" class="nilai-asli">
                                        </div>
                                        <div class="mt-2 text-end">
                                            <span class="text-muted">Kembalian:</span>
                                            <h4 class="fw-bold text-success" id="kembalianText">Rp 0</h4>
                                        </div>
                                    </div>
                                </div>
                                <button class="btn btn-success btn-lg w-100 mt-3" id="btnSimpanTransaksi">
                                    <i class="bi bi-printer me-2"></i> Simpan & Cetak Struk
                                </button>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3 hitung">Hitung Pembelian</button>
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
    <script>
        function formatRupiah2(angka) {
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
            $(this).val(formatRupiah2(nilai));

            // 2. Ambil angka bersih (tanpa titik)
            let angkaBersih = nilai.replace(/\./g, '');

            // 3. Masukkan ke hidden input di sebelahnya agar terkirim ke server
            $(this).siblings('.nilai-asli').val(angkaBersih);

            let total = parseFloat($('#totalAkhirValue').val()) || 0;

            let bayar = parseFloat($('#uangBayarAsli').val()) || 0;

            let kembalian = bayar - total;


            if (kembalian < 0) {

                $('#kembalianText').removeClass('text-success').addClass('text-danger').text('Rp -' + formatRupiah(

                    Math.abs(kembalian).toString()));

            } else {

                $('#kembalianText').removeClass('text-danger').addClass('text-success').text('Rp ' + formatRupiah(

                    kembalian.toString()));

            }
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

        function formatRupiah(angka) {
            return new Intl.NumberFormat('id-ID').format(angka);
        }

        // Fungsi untuk menghilangkan titik sebelum dikirim ke server
        function cleanRupiah(rupiah) {
            return rupiah.replace(/\./g, '');
        }
    </script>
    <script>
        $(document).ready(function() {
            // 1. Event saat Scan Barcode (Enter key biasanya dikirim oleh scanner)
            $('#scanBatch').on('keypress', function(e) {
                if (e.which == 13) { // Tombol Enter
                    e.preventDefault();
                    let batchValue = $(this).val();

                    if (batchValue != "") {
                        cariBarang(batchValue);
                    }
                    $(this).val(""); // Kosongkan input scan setelah pencarian
                }
            });

            // 2. Fungsi AJAX mencari barang
            function cariBarang(batch) {
                $.ajax({
                    url: "{{ route('pembelian.searchBatch') }}", // Sesuaikan route Anda
                    type: "GET",
                    data: {
                        batch: batch
                    },
                    success: function(response) {
                        if (response.success) {
                            tambahBaris(response.data);
                        } else {
                            alert(response.message);
                        }
                    }
                });
            }



            function tambahBaris(item) {
                // 1. Cek duplikat
                let exists = false;
                $('.row-kode').each(function() {
                    if ($(this).val() == item.kode_barang) {
                        exists = true;
                        let qtyInput = $(this).closest('tr').find('.row-qty');
                        qtyInput.val(parseInt(qtyInput.val()) + 1);
                    }
                });

                if (!exists) {
                    let opsiSatuan = '';
                    // Simpan rasio di sini: Besar, Sedang, Kecil
                    if (item.satuan_besar) opsiSatuan +=
                        `<option value="besar" data-rasio="${item.rasio_sedang}">${item.satuan_besar}</option>`;
                    if (item.satuan_sedang) opsiSatuan +=
                        `<option value="sedang" data-rasio="${item.rasio_kecil}">${item.satuan_sedang}</option>`;
                    if (item.satuan_kecil) opsiSatuan +=
                        `<option value="kecil" data-rasio="1" selected>${item.satuan_kecil}</option>`;

                    let row = `
            <tr>
                <td>${item.nama_dagang}</td>
                <td>  <input readonly type="text" name="nomorbatch[]" value="${item.kode_batch}" class="form-control form-control-sm row-batch text-right">
                    <input hidden type="text" name="kodebarang[]" value="${item.kode_barang}" class="form-control form-control-sm row-kode_barang text-right">
                    <input hidden type="text" name="namabarang[]" value="${item.nama_dagang}" class="form-control form-control-sm row-nama text-right">
                    </td>
                <td>
                    <select name="satuan[]" class="form-control form-control-sm select-satuan" data-hargadasar="${item.harga_jual}">
                        ${opsiSatuan}
                    </select>
                </td>
                <td><input type="text" name="qty[]" value="1" class="form-control form-control-sm row-qty text-right"></td>
                <td>
                    <input readonly type="text" name="harga[]" value="${formatRupiah(item.harga_jual.toString())}" class="form-control form-control-sm row-harga text-right">
                </td>
                <td class="align-middle text-center">
                <button type="button" class="btn btn-danger btn-sm btn-hapus"><i class="bi bi-trash3"></i></button>
            </td>
            </tr>`;
                    $('#tablePembelian tbody').append(row);
                }
            }

            // 4. Fungsi Hapus Baris
            $(document).on('click', '.btn-hapus', function() {
                $(this).closest('tr').remove();
            });
        });
        $(document).on('change', '.select-satuan', function() {
            let row = $(this).closest('tr');

            // 1. Ambil harga dasar (satuan terkecil) dari atribut select
            let hargaDasar = parseFloat($(this).data('hargadasar')) || 0;

            // 2. Ambil rasio dari option yang sedang dipilih
            let rasio = parseFloat($(this).find(':selected').data('rasio')) || 1;

            // 3. Hitung harga baru (Harga Kecil * Rasio)
            // Misal: Harga 1 tablet 1.000, pilih Box isi 100, maka harga jadi 100.000
            let hargaBaru = hargaDasar * rasio;

            // 4. Update field harga di baris tersebut
            row.find('.row-harga').val(formatRupiah(hargaBaru.toString()));

            // (Opsional) Jika Anda punya tombol hitung otomatis, panggil di sini
            // hitungTotalKeseluruhan(); 
        });
        $(document).on('click', '.hitung', function() {
            let totalKeseluruhan = 0;
            let htmlDetail = '';
            let hasData = false;

            $('#tablePembelian tbody tr').each(function() {
                hasData = true;
                let row = $(this);
                let nama = row.find('td:first').text();
                let qty = parseFloat(row.find('.row-qty').val()) || 0;
                let hargaText = row.find('.row-harga').val();
                let harga = parseFloat(cleanRupiah(hargaText)) || 0;
                let satuan = row.find('.select-satuan').val();

                let subtotal = qty * harga;
                totalKeseluruhan += subtotal;

                htmlDetail += `
                    <tr>
                        <td><strong>${nama}</strong> <br> <small class="text-muted">${qty} ${satuan} x Rp ${formatRupiah(harga.toString())}</small></td>
                        <td class="text-right align-middle font-weight-bold">Rp ${formatRupiah(subtotal.toString())}</td>
                    </tr>
                `;
            });

            if (!hasData) {
                Swal.fire('Opps', 'Pilih minimal satu obat dulu!', 'warning');
                return;
            }

            // Tampilkan Section Pembayaran
            $('#detailHitung').html(htmlDetail);
            $('#totalAkhirText').text('Rp ' + formatRupiah(totalKeseluruhan.toString()));
            $('#totalAkhirValue').val(totalKeseluruhan);
            $('#pembayaranSection').fadeIn();

            // Scroll otomatis ke section pembayaran
            $('html, body').animate({
                scrollTop: $("#pembayaranSection").offset().top
            }, 500);
        });
    </script>
    <script>
        $(document).on('click', '#btnSimpanTransaksi', function() {
            let total = parseFloat($('#totalAkhirValue').val()) || 0;
            let bayar = parseFloat($('#uangBayarAsli').val()) || 0;

            if (bayar < total) {
                Swal.fire('Oopss', 'Uang pembayaran kurang!', 'error');
                return;
            }

            // Ambil semua data dari tabel pembelian
            let items = [];
            $('#tablePembelian tbody tr').each(function() {
                let row = $(this);
                items.push({
                    nama_barang: row.find('.row-nama').val(),
                    kode_batch: row.find('.row-batch').val(),
                    kode_barang: row.find('.row-kode_barang').val(),
                    satuan: row.find('.select-satuan').val(),
                    qty: row.find('.row-qty').val(),
                    harga: cleanRupiah(row.find('.row-harga').val()),
                });
            });

            Swal.fire({
                title: 'Konfirmasi',
                text: "Simpan transaksi ini?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Simpan!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: "{{ route('simpan.penjualan') }}", // Ganti dengan route simpan Anda
                        data: {
                            _token: "{{ csrf_token() }}",
                            id_sesi: $('#id_sesi_kasir').val(),
                            total_akhir: total,
                            uang_bayar: bayar,
                            items: items // Data array barang
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('Berhasil!', 'Transaksi disimpan.', 'success').then(
                                    () => {
                                        location.reload(); // Atau arahkan ke print struk
                                    });
                            } else {
                                Swal.fire('Gagal', response.message, 'error');
                            }
                        }
                    });
                }
            });
        });
    </script>
@endsection
