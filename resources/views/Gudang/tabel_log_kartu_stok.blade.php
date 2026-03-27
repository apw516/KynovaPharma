<table class="table table-sm table-bordered table-hover" id="tabelstok">
    <thead>
        <th>Tanggal Stok</th>
        <th>Nama Barang</th>
        <th>Keterangan</th>
        <th>Stok Awal</th>
        <th>Stok IN</th>
        <th>Stok OUT</th>
        <th>Stok Akhir</th>
    </thead>
    <tbody>
        @foreach ($data as $d)
            <tr>
                <td>{{ $d->tgl_input }}</td>
                <td>{{ $d->nama_dagang }} ( {{ $d->nama_obat }} , {{ $d->produsen }} )</td>
                <td>{{ $d->keterangan }}</td>
                <td>{{ number_format($d->stok_last, 0, ',', '.') }}</td>
                <td>{{ number_format($d->stok_in, 0, ',', '.') }}</td>
                <td>{{ number_format($d->stok_out, 0, ',', '.') }}</td>
                <td>{{ number_format($d->stok_now, 0, ',', '.') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
<script>
    $(function() {
        $("#tabelstok").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "pageLength": 12,
            "searching": true,
            "ordering": false,
        })
    });
</script>
