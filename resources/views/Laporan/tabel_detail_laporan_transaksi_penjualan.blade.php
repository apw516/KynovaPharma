<style>
    #tabeldetail thead th {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6c757d;
        padding: 12px 10px;
        border-top: none;
    }

    #tabeldetail tbody td {
        font-size: 0.9rem;
        padding: 12px 10px;
    }

    /* Styling angka agar sejajar secara vertikal */
    .text-end {
        font-family: 'Inter', sans-serif;
    }

    /* Warna lembut untuk baris retur */
    .bg-danger-subtle {
        background-color: #fff5f5 !important;
    }

    /* Baris hover yang halus */
    #tabeldetail tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.01);
        transition: 0.2s;
    }
</style>
<div class="table-responsive">
    <table id="tabeldetail" class="table table-hover align-middle border-bottom" style="width:100%">
        <thead class="table-light">
            <tr>
                <th style="width: 35%">Nama Barang</th>
                <th class="text-center">Qty</th>
                {{-- <th class="text-end">Harga Satuan</th> --}}
                <th class="text-end">Subtotal</th>
                <th class="text-end">Potongan (Disc)</th>
                <th class="text-end fw-bold">Grand Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $d)
                <tr class="{{ $d->status_retur == 2 ? 'bg-danger-subtle opacity-75' : '' }}">
                    <td>
                        <div class="fw-bold text-dark">{{ $d->nama_dagang }}</div>
                        @if ($d->status_retur == 2)
                            <span class="badge rounded-pill bg-danger px-2" style="font-size: 0.65rem;">
                                <i class="bi bi-arrow-return-left me-1"></i>ITEM RETUR
                            </span>
                        @endif
                    </td>
                    {{-- <td class="text-center">
                        <span class="badge bg-secondary-subtle text-secondary px-3">{{ $d->qty }}</span>
                    </td> --}}
                    <td>
                        @php
                            $sisa_qty = $d->qty;
                            $harga_satuan_terkecil = $d->harga_jual; // Harga per 1 Tablet

                            // 1. Hitung Rasio
                            $rasio_ke_bok = $d->rasio_sedang * $d->rasio_kecil; // Misal: 10 * 10 = 100
                            $rasio_ke_strip = $d->rasio_kecil; // Misal: 10

                            // 2. Hitung Harga Satuan per Unit
                            $harga_per_bok = $rasio_ke_bok * $harga_satuan_terkecil;
                            $harga_per_strip = $rasio_ke_strip * $harga_satuan_terkecil;

                            // 3. Logika Pecahan Qty (seperti sebelumnya)
                            $jml_bok = floor($sisa_qty / $rasio_ke_bok);
                            $sisa_setelah_bok = $sisa_qty % $rasio_ke_bok;
                            $jml_strip = floor($sisa_setelah_bok / $rasio_ke_strip);
                            $jml_tablet = $sisa_setelah_bok % $rasio_ke_strip;

                            $result = [];

                            // Tampilan: [Jumlah] [Satuan] (@Harga Satuan Unit)
                            if ($jml_bok > 0) {
                                $result[] =
                                    $jml_bok .
                                    ' ' .
                                    $d->satuan_besar .
                                    ' (@Rp ' .
                                    number_format($harga_per_bok, 0, ',', '.') .
                                    ')';
                            }

                            if ($jml_strip > 0) {
                                $result[] =
                                    $jml_strip .
                                    ' ' .
                                    $d->satuan_sedang .
                                    ' (@Rp ' .
                                    number_format($harga_per_strip, 0, ',', '.') .
                                    ')';
                            }

                            if ($jml_tablet > 0) {
                                $result[] =
                                    $jml_tablet .
                                    ' ' .
                                    $d->satuan_kecil .
                                    ' (@Rp ' .
                                    number_format($harga_satuan_terkecil, 0, ',', '.') .
                                    ')';
                            }
                        @endphp

                        {{-- Tampilkan hasil dengan pemisah koma atau spasi --}}
                        @if (empty($result))
                            0 Tablet
                        @else
                            {{ implode(', ', $result) }}
                        @endif
                    </td>
                    {{-- <td></td> --}}
                    {{-- <td class="text-end text-muted small">
                        Rp {{ number_format($d->harga_jual, 0, ',', '.') }}
                    </td> --}}
                    <td class="text-end">
                        Rp {{ number_format($d->subtotal, 0, ',', '.') }}
                    </td>
                    <td class="text-end text-danger small">
                        @if ($d->diskon > 0)
                            -Rp {{ number_format($d->diskon, 0, ',', '.') }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-end fw-bold text-primary">
                        Rp {{ number_format($d->grandtotal, 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<script>
    $(function() {
        $("#tabeldetail").DataTable({
            "responsive": true,
            "paging": false, // Biasanya detail tidak perlu paging jika item sedikit
            "searching": false, // Fokus pada pembacaan data
            "info": false,
            "ordering": false,
            "columnDefs": [{
                    "className": "dt-head-center",
                    "targets": [1]
                },
                {
                    "className": "dt-head-right",
                    "targets": [2, 3, 4, 5]
                }
            ]
        });
    });
</script>
