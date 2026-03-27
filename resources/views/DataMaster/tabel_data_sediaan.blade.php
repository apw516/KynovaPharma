<table class="table table-sm">
    <thead>
        <th>Tanggal Input</th>
        <th>Supplier</th>
        <th>No Batch</th>
        <th>Expired Date</th>
        <th>Stok Awal</th>
        <th>Stok Sekarang</th>
        <th>Harga Modal</th>
    </thead>
    <tbody>
        @foreach($data_sediaan as $d)
            <tr>
                <td>{{ $d->tgl_input ? \Carbon\Carbon::parse($d->tgl_input)->translatedFormat('d F Y') : '-' }}</td>
                <td>{{ $d->supplier->nama_supplier }}</td>
                <td>{{ $d->kode_batch}}</td>
                <td>{{ $d->tgl_expired ? \Carbon\Carbon::parse($d->tgl_expired)->translatedFormat('d F Y') : '-' }}</td>
                <td>{{ $d->stok_awal}}</td>
                <td>{{ $d->stok_sekarang}} {{ $data_master[0]->satuan_kecil }}</td>
                <td>{{ number_format($d->harga_modal_satuan_kecil, 0, ',', '.') }} / {{ $data_master[0]->satuan_kecil }}</td>
            </tr>
        @endforeach
    </tbody>
</table>