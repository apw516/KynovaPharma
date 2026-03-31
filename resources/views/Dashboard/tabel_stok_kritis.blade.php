<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h6 class="m-0 fw-bold text-danger">
            <i class="bi bi-exclamation-octagon-fill me-2"></i>Daftar Stok Kritis & Re-Order
        </h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="tabelStokKritis">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4">Nama Produk</th>
                        <th class="text-center">Ketersediaan</th>
                        <th class="text-center">Titik Aman (SS)</th>
                        <th class="text-center">Status Urgensi</th>
                        {{-- <th class="text-end pe-4">Saran Re-Order</th> --}}
                    </tr>
                </thead>
                <tbody>
                    @foreach($stokKritis as $s)
                        @php
                            // Hitung persentase stok terhadap safety stock
                            $persen = $s->safety_stock > 0 ? ($s->stok_sekarang / $s->safety_stock) * 100 : 0;
                            
                            // Tentukan warna berdasarkan tingkat bahaya
                            $warna = 'bg-success';
                            if($s->stok_sekarang == 0) $warna = 'bg-danger';
                            elseif($persen <= 50) $warna = 'bg-warning';
                        @endphp
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark">{{ $s->nama_dagang }}</div>
                                <code class="small text-muted">{{ $s->kode_barang }}</code>
                            </td>
                            <td class="text-center" style="width: 200px;">
                                <div class="d-flex align-items-center justify-content-center">
                                    <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                        <div class="progress-bar {{ $warna }}" role="progressbar" style="width: {{ $persen > 100 ? 100 : $persen }}%"></div>
                                    </div>
                                    <span class="small fw-bold">{{ number_format($s->stok_sekarang, 0) }}</span>
                                </div>
                                <small class="text-muted" style="font-size: 0.7rem;">{{ $s->satuan_kecil }} tersedia</small>
                            </td>
                            <td class="text-center text-muted small">
                                {{ number_format($s->safety_stock, 0) }} {{ $s->satuan_kecil }}
                            </td>
                            <td class="text-center">
                                @if($s->stok_sekarang == 0)
                                    <span class="badge rounded-pill bg-danger px-3">Kosong Total</span>
                                @elseif($persen <= 50)
                                    <span class="badge rounded-pill bg-warning text-dark px-3">Sangat Kritis</span>
                                @else
                                    <span class="badge rounded-pill bg-info text-white px-3">Dibawah Limit</span>
                                @endif
                            </td>
                            {{-- <td class="text-end pe-4">
                                <button class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                    <i class="bi bi-cart-plus me-1"></i>
                                    Order {{ ceil($s->kecepatan_harian * 7) }}
                                </button>
                            </td> --}}
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    $(function() {
    $("#tabelStokKritis").DataTable({
        "dom": "t", // Hanya tampilkan tabel, tanpa search/paging karena ini widget dashboard
        "ordering": true,
        "order": [[1, "asc"]], // Urutkan stok paling sedikit di atas
        "responsive": true,
        "language": {
            "emptyTable": "Tidak ada produk yang dibawah limit stok."
        }
    });
});
</script>