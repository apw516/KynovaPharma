<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #444; padding-bottom: 10px; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .table th { background-color: #f2f2f2; font-size: 10px; text-transform: uppercase; }
        .badge { padding: 3px 8px; border-radius: 10px; font-size: 10px; }
        .text-danger { color: #d9534f; }
        .summary-box { background: #f9f9f9; padding: 15px; margin-bottom: 20px; border-left: 5px solid #0275d8; }
    </style>
</head>
<body>
    <div class="header">
        <h2>KYNOVAPHARMA</h2>
        <p>Laporan Ringkasan Mingguan Sistem Apotek<br>Tanggal Cetak: {{ $date }}</p>
    </div>

    <div class="summary-box">
        <strong>Ringkasan Performa:</strong><br>
        Total Omzet 7 Hari Terakhir: <strong>Rp {{ number_format($omzet, 0, ',', '.') }}</strong>
    </div>

    <h4>1. Daftar Stok Harus Re-Order</h4>
    <table class="table">
        <thead>
            <tr>
                <th>Nama Barang</th>
                <th>Stok Saat Ini</th>
                <th>Saran Order</th>
            </tr>
        </thead>
        <tbody>
            @foreach($stokKritis as $s)
            <tr>
                <td>{{ $s->nama_dagang }}</td>
                <td style="color: red;">{{ $s->stok_sekarang }} {{ $s->satuan_kecil }}</td>
                <td>{{ ceil($s->kecepatan_harian * 7) }} {{ $s->satuan_kecil }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h4>2. Monitoring Kadaluwarsa (ED)</h4>
    <table class="table">
        <thead>
            <tr>
                <th>Produk</th>
                <th>No. Batch</th>
                <th>Tanggal ED</th>
                <th>Sisa Hari</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dataED as $ed)
            <tr>
                <td>{{ $ed->nama_dagang }}</td>
                <td>{{ $ed->kode_batch }}</td>
                <td>{{ date('d/m/Y', strtotime($ed->tgl_expired)) }}</td>
                <td class="{{ $ed->sisa_hari <= 90 ? 'text-danger' : '' }}">
                    {{ $ed->sisa_hari }} Hari
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>