<table class="table table-sm table-borderer table-hover">
    <thead>
        <th>Kode Barang</th>
        <th>Nama Obat</th>
        <th>Merk Dagang</th>
        <th>Produsen</th>
        {{-- <th>Satuan Besar</th>
        <th>Satuan Sedang</th>
        <th>Satuan Kecil</th> --}}
        <th>Rasio Sedang</th>
        <th>Rasio Kecil</th>
    </thead>
    <tbody>
        @foreach ($data as $d)
            <tr class="pilih-obat" style="cursor:pointer" data-kode="{{ $d->kode_barang }}" data-nama="{{ $d->nama_obat }}"
                data-dagang="{{ $d->nama_dagang }}" data-satuan="{{ $d->satuan_besar }}">
                <td>{{ $d->kode_barang }}</td>
                <td>{{ $d->nama_obat }}</td>
                <td>{{ $d->nama_dagang }}</td>
                <td>{{ $d->produsen }}</td>
                {{-- <td>{{ $d->satuan_besar}}</td>
                <td>{{ $d->satuan_sedang}}</td>
                <td>{{ $d->satuan_kecil}}</td> --}}
                <td>1 {{ $d->satuan_besar }} ( {{ $d->rasio_sedang }} {{ $d->satuan_sedang }} )</td>
                <td>1 {{ $d->satuan_sedang }} ( {{ $d->rasio_kecil }} {{ $d->satuan_kecil }} )</td>
            </tr>
        @endforeach
    </tbody>
</table>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // 1. Ketika baris tabel diklik
        $('.pilih-obat').on('click', function() {
            let kode = $(this).data('kode');
            let nama = $(this).data('nama');
            let dagang = $(this).data('dagang');
            let satuan = $(this).data('satuan');
            // Cek apakah obat sudah ada di list (agar tidak double)
            if ($(`#row-${kode}`).length > 0) {
                alert('Obat ini sudah terpilih!');
                return;
            }
            // 2. Susun template HTML untuk inputan form
            let html = `
            <tr id="row-${kode}">
                <td>
                    <strong>${dagang}</strong><br>
                    <small class="text-muted">${nama}</small>
                    <input type="hidden" name="kode_barang" value="${kode}">
                    <input type="hidden" name="nama_barang" value="${nama}">
                </td>
                <td>
                    <input type="text" name="qty" class="form-control form-control-sm" placeholder="jumlah ..." value="0" required>
                </td>
                <td>
                    <input type="text" name="satuan" class="form-control form-control-sm" placeholder="satuan barang ..." value="${satuan}">
                </td>
                <td>
                    <input type="text" name="hargabeli" class="form-control form-control-sm input-mask-uang" placeholder="Masukan hrga beli ..." value="0">
                    <input hidden type="text" name="hargabeliasli" class="form-control form-control-sm nilai-asli" placeholder="Masukan hrga beli ..." value="0">
                </td>
                <td>
                    <input type="text" name="diskonpersen" class="form-control form-control-sm" placeholder="Masukan diskon dalam persen ..." value="0">
                </td>
                <td>
                    <input type="text" name="diskonrupiah" class="form-control form-control-sm input-mask-uang" placeholder="Masukan diskon dalam rupiah ..." value="0">
                    <input hidden type="text" name="diskonrupiahasli" class="form-control form-control-sm nilai-asli" placeholder="Masukan diskon dalam rupiah ..." value="0">
                </td>
                <td>
                    <input type="text" name="kodebatch" class="form-control form-control-sm" placeholder="Masukan kode batch ..." value="">
                </td>
                <td>
                    <input type="date" name="expireddate" class="form-control form-control-sm" placeholder="Masukan tanggal expired" value="">
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm btn-hapus"><i class="bi bi-x-circle"></i></button>
                </td>
            </tr>
        `;

            // 3. Masukkan ke dalam wrapper
            $('#wrapper-form-obat').append(html);
        });
        // 4. Fungsi Hapus Baris
        $(document).on('click', '.btn-hapus', function() {
            $(this).closest('tr').remove();
        });
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
</script>
