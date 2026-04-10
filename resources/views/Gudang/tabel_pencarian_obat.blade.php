<table class="table table-sm table-borderer table-hover" id="tabelbarang">
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
                data-dagang="{{ $d->nama_dagang }}" data-satuan="{{ $d->satuan_besar }}"
                data-satuan_sedang="{{ $d->satuan_sedang }}" data-satuan_kecil="{{ $d->satuan_kecil }}"
                data-rasio_sedang = "{{ $d->rasio_sedang }}" data-rasio_kecil="{{ $d->rasio_kecil }}">
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
<div style="display: none;">
    <select id="master-sediaan-list">
        <option value="">-- Pilih Sediaan --</option>
        @foreach ($mt_sediaan as $sediaan)
            <option value="{{ $sediaan->kode_satuan }}">{{ $sediaan->nama_satuan }}</option>
        @endforeach
    </select>
</div>
{{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}

<script>
    $(document).ready(function() {
        // 1. Ketika baris tabel diklik

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
    $(function() {
        $("#tabelbarang").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "pageLength": 12,
            "searching": true,
            "ordering": false,
        })
    });
    $(document).on('click', '.pilih-obat', function() {
        $(document).on('click', '.pilih-obat', function() {
            let kode = $(this).data('kode');
            let nama = $(this).data('nama');
            let dagang = $(this).data('dagang');

            // Ambil Data Satuan & Rasio dari tombol
            let sbesar = $(this).data('satuan');
            let ssedang = $(this).data('satuan_sedang');
            let skecil = $(this).data('satuan_kecil');
            let rsedang = $(this).data('rasio_sedang') || 1;
            let rkecil = $(this).data('rasio_kecil') || 1;

            // Cek apakah obat sudah ada di list
            if ($(`#row-${kode}`).length > 0) {
                return;
            }
            // Pindahkan master HTML ke variabel agar tidak dipanggil berulang
            let masterSediaanHTML = $('#master-sediaan-list').html();

            // Fungsi pembantu (Didefinisikan sekali saja)
            const renderSelect = (name, currentValue) => {
                return `
        <select name="${name}" class="form-select form-select-sm select-sediaan" data-selected="${currentValue || ''}">
            ${masterSediaanHTML}
        </select>`;
            };

            // Susun template HTML
            let html = `
    <tr id="row-${kode}">
        <td>
            <strong>${dagang}</strong><br>
            <small class="text-muted">${nama}</small>
            <input type="hidden" name="kode_barang" value="${kode}">
            <input type="hidden" name="nama_barang" value="${dagang}">
        </td>
        <td>
            <input type="number" name="qty" class="form-control form-control-sm" value="0" required>
        </td>
        <td>
            <label class="small text-primary">Satuan Besar</label>
            ${renderSelect('satuan_besar', sbesar)}
            <label class="small text-success">Satuan Sedang</label>
            ${renderSelect('satuan_sedang', ssedang)}
            <label class="small text-success">Rasio Sedang</label>
            <input type="number" name="rasio_sedang" class="form-control form-control-sm mt-1" value="${rsedang}" placeholder="Rasio...">
            <label class="small text-info">Satuan Kecil</label>
            ${renderSelect('satuan_kecil', skecil)}
            <label class="small text-success">Rasio Kecil</label>
            <input type="number" name="rasio_kecil" class="form-control form-control-sm mt-1" value="${rkecil}" placeholder="Rasio...">
        </td>
        <td>
            <input type="text" name="hargabeli" class="form-control form-control-sm input-mask-uang" value="0">
            <input type="hidden" name="hargabeliasli" class="nilai-asli" value="0">
        </td>
        <td>
            <input type="text" name="diskonpersen" class="form-control form-control-sm" value="0">
            </td>
            <td>
                <input type="text" name="diskonrupiah" class="form-control form-control-sm input-mask-uang" value="0">
                <input type="text" name="diskonrupiahasli" class="form-control form-control-sm nilai-asli" value="0">
        </td>
        <td>
            <input type="text" name="kodebatch" class="form-control form-control-sm" placeholder="Batch...">
        </td>
        <td>
            <input type="date" name="expireddate" class="form-control form-control-sm">
        </td>
        <td>
            <button type="button" class="btn btn-danger btn-sm btn-hapus"><i class="bi bi-x-circle"></i></button>
        </td>
    </tr>`;

            // Tambahkan ke DOM
            let $newRow = $(html);
            $('#wrapper-form-obat').append($newRow);

            // Logic Auto-Select Sediaan
            $newRow.find('.select-sediaan').each(function() {
                let valToSelect = $(this).data('selected');
                if (valToSelect) {
                    $(this).val(valToSelect);

                    // Fallback: Jika value tidak ketemu, cari berdasarkan teks
                    if ($(this).val() === null || $(this).val() === "") {
                        $(this).find('option').filter(function() {
                            return $(this).text().trim() === valToSelect.toString()
                                .trim();
                        }).prop('selected', true);
                    }
                }
            });
        });
    })
</script>
