<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Cetak Struk #{{ $header->no_invoice }}</title>
    <style>
        @page {
            size: 58mm auto;
            /* Ukuran kertas thermal umum */
            margin: 0;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            width: 58mm;
            margin: 0;
            padding: 5px;
            font-size: 11px;
            color: #000;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }

        .brand {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .info {
            font-size: 10px;
            margin-bottom: 5px;
            line-height: 1.2;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            border-bottom: 1px dashed #000;
            padding: 3px 0;
            text-align: left;
        }

        td {
            padding: 3px 0;
            vertical-align: top;
        }

        .totals {
            margin-top: 5px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
        }

        .grand-total {
            font-weight: bold;
            font-size: 12px;
            border-top: 1px solid #000;
            padding-top: 3px;
        }

        .footer {
            margin-top: 10px;
            font-size: 9px;
            line-height: 1.2;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="no-print" style="background: #eee; padding: 10px; text-align: center; margin-bottom: 10px;">
        <button onclick="window.print()">KLIK CETAK</button>
    </div>

    <div class="ticket">
        <div class="text-center">
            <div class="brand">APOTEK FALIH FARMA</div>
            <div class="info" style="font-weight:bold">
                Jl. Once RT 001 RW 002 DESA KUBANGKARANG KEC. KARANGSEMBUNG KAB.CIREBON<br>
            </div>
        </div>

        <div class="divider"></div>

        <div class="info" style="font-weight:bold">
            No : {{ $header->no_invoice }}<br>
            Tgl : {{ date('d/m/Y H:i', strtotime($header->tgl_transaksi)) }}<br>
            Kasir: {{ Auth::user()->nama ?? 'Admin' }}<br>
            {{-- Pasien: {{ $header->nama_pasien ?? 'Umum' }} --}}
        </div>

        <div class="divider"></div>

        <table>
            <thead>
                <tr>
                    <th style="width: 50%;">Item</th>
                    <th style="width: 15%; text-align: center;">Qty</th>
                    <th style="width: 35%; text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($detail as $item)
                    @php
                        $sisa = $item->qty;
                        $tampilan_qty = [];

                        // 1. Hitung Satuan Besar (Contoh: Box)
                        // Rasio Besar ke Kecil = rasio_sedang * rasio_kecil
                        $konversi_besar = ($item->rasio_sedang ?? 1) * ($item->rasio_kecil ?? 1);
                        $jml_besar = floor($sisa / $konversi_besar);
                        if ($jml_besar > 0) {
                            $tampilan_qty[] = $jml_besar . ' ' . ($item->satuan_besar ?? 'Box');
                            $sisa %= $konversi_besar;
                        }

                        // 2. Hitung Satuan Sedang (Contoh: Strip)
                        $jml_sedang = floor($sisa / ($item->rasio_kecil ?? 1));
                        if ($jml_sedang > 0) {
                            $tampilan_qty[] = $jml_sedang . ' ' . ($item->satuan_sedang ?? 'Strip');
                            $sisa %= $item->rasio_kecil ?? 1;
                        }

                        // 3. Sisa Satuan Kecil (Contoh: Tablet/Pcs)
                        if ($sisa > 0 || empty($tampilan_qty)) {
                            $tampilan_qty[] = $sisa . ' ' . ($item->satuan_kecil ?? 'Pcs');
                        }

                        // Gabungkan array menjadi string (Contoh: 1 Box 2 Strip 5 Tablet)
                        $teks_qty = implode(' ', $tampilan_qty);
                    @endphp
                <tr>
            <td colspan="3" style="padding-top: 4px;">
                <strong>{{ $item->nama_dagang }}</strong>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="font-size: 10px; color: #333; padding-bottom: 4px;" style="font-weight:bold">
                <strong>{{ $teks_qty }}</strong>
            </td>
            <td class="text-right" style="vertical-align: bottom; padding-bottom: 4px;" style="font-weight:bold">
                {{ number_format($item->subtotal, 0, ',', '.') }}
            </td>
        </tr>
                @endforeach
            </tbody>
        </table>

        <div class="divider"></div>

        <div class="totals">
            <div class="total-row">
                <span style="font-weight:bold">Subtotal</span>
                <span style="font-weight:bold">{{ number_format($header->total_harga, 0, ',', '.') }}</span>
            </div>
            @if ($header->diskon > 0)
                <div class="total-row" style="font-weight:bold">
                    <span style="font-weight:bold">Potongan</span>
                    <span style="font-weight:bold">-{{ number_format($header->diskon, 0, ',', '.') }}</span>
                </div>
            @endif
            <div class="total-row grand-total">
                <span>TOTAL</span>
                <span style="font-weight:bold">{{ number_format($header->total_bayar, 0, ',', '.') }}</span>
            </div>
            <div class="total-row">
                <span style="font-weight:bold">Tunai</span>
                <span style="font-weight:bold">{{ number_format($header->nominal_terima, 0, ',', '.') }}</span>
            </div>
            <div class="total-row">
                <span style="font-weight:bold">Kembali</span>
                <span style="font-weight:bold">{{ number_format($header->nominal_kembali, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="divider"></div>

        <div class="footer text-center" style="font-weight:bold">
            Terima Kasih Atas Kunjungan Anda<br>
            <strong>Semoga Lekas Sembuh</strong><br>
            Simpan struk ini sebagai bukti pembayaran sah.
        </div>
    </div>

    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Berikan sedikit jeda agar CSS termuat sempurna sebelum dialog print muncul
            setTimeout(function() {
                window.print();
            }, 500);

            // Menutup tab otomatis setelah selesai (opsional)
            window.onafterprint = function() {
                window.close();
            };
        });
    </script>
</body>

</html>
