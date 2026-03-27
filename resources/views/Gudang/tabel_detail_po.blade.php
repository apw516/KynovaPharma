<table class="table table-sm">
    <thead>
        <th>Nama Barang</th>
        <th>Tanggal ED</th>
        <th>Kode Batch</th>
        <th>QTY</th>
        <th>Harga Modal</th>
        <th>Grand Total</th>
    </thead>
    <tbody>
        @php
            $gtt = 0;
        @endphp
        @foreach ($data_po as $d)
            @php
                $pajak = $data_header[0]->pajak_persen;
                $pajak_rupiah = ($d->harga_beli * $pajak) / 100;
                $hm = $d->harga_beli;
                $hmm = $hm * $d->qty;
                $gtt = $gtt + $hmm;
            @endphp
            <tr>
                <td>{{ $d->nama_barang }}</td>
                <td>{{ $d->tgl_expired ? \Carbon\Carbon::parse($d->tgl_expired)->translatedFormat('d F Y') : '-' }} </td>
                <td>{{ $d->no_batch }}</td>
                <td>{{ $d->qty }} {{ $d->satuan }}</td>
                <td>{{ number_format($d->harga_beli, 0, ',', '.') }}</td>
                <td>{{ number_format($d->harga_beli * $d->qty, 0, '.', '.') }}</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="5" class="text-center fw-bold">Grandtotal</td>
            <td class="fw-bold">{{ number_format($gtt, 0, ',', '.') }}</td>
        </tr>
    </tbody>
</table>
