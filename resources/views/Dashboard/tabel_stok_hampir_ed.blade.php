<div class="card border-0 shadow-sm overflow-hidden">
    <div class="card-header bg-white border-0 py-3">
        <div class="d-flex justify-content-between align-items-center px-2">
            <div>
                <h6 class="fw-bold text-dark mb-1">
                    <i class="bi bi-shield-exclamation text-danger me-2"></i>Kontrol Kadaluwarsa
                </h6>
                <p class="text-muted small mb-0">Pemantauan stok berdasarkan batch sediaan</p>
            </div>
            <div class="badge bg-light text-muted border py-2 px-3 rounded-3">
                <i class="bi bi-database-fill-check me-1"></i> Sync: Live
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="tableED">
            <thead class="bg-light text-muted extra-small text-uppercase">
                <tr>
                    <th class="ps-4 border-0">Produk & Batch</th>
                    <th class="text-center border-0">Tanggal ED</th>
                    <th class="text-center border-0">Sisa Stok</th>
                    <th class="text-center border-0">Urgensi</th>
                    <th class="text-end pe-4 border-0">Aksi</th>
                </tr>
            </thead>
            <tbody class="border-top-0">
                @foreach ($dataED as $ed)
                    @php
                        $isExpired = $ed->sisa_hari <= 0;
                        $isCritical = $ed->sisa_hari > 0 && $ed->sisa_hari <= 90;

                        $statusColor = $isExpired ? 'danger' : ($isCritical ? 'warning' : 'info');
                        $statusLabel = $isExpired ? 'EXPIRED' : ($isCritical ? 'KRITIS' : 'WASPADA');
                        $rowClass = $isExpired ? 'bg-danger-subtle' : '';
                    @endphp
                    <tr class="{{ $rowClass }}">
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm me-3 bg-{{ $statusColor }}-subtle text-{{ $statusColor }} d-flex align-items-center justify-content-center rounded-3 fw-bold shadow-sm"
                                    style="width: 38px; height: 38px; font-size: 14px;">
                                    {{ substr($ed->nama_dagang, 0, 1) }}
                                </div>
                                <div>
                                    <div class="fw-bold text-dark mb-0" style="font-size: 0.9rem;">
                                        {{ $ed->nama_dagang }}</div>
                                    <div class="text-muted" style="font-size: 0.75rem;">
                                        <span class="badge bg-white text-secondary border fw-normal shadow-sm">BN:
                                            {{ $ed->kode_batch }}</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="fw-bold {{ $isExpired ? 'text-danger' : 'text-dark' }}"
                                style="font-size: 0.85rem;">
                                {{ date('d M Y', strtotime($ed->tgl_expired)) }}
                            </div>
                            <div class="text-muted extra-small">{{ abs($ed->sisa_hari) }} hari
                                {{ $ed->sisa_hari < 0 ? 'lalu' : 'lagi' }}</div>
                        </td>
                        <td class="text-center">
                            <span class="fw-bold text-dark">{{ number_format($ed->stok_sediaan, 0) }}</span>
                            <span class="text-muted small">{{ $ed->satuan_kecil }}</span>
                        </td>
                        <td class="text-center">
                            <span
                                class="badge bg-{{ $statusColor }}-subtle text-{{ $statusColor }} px-3 py-2 rounded-pill border border-{{ $statusColor }} border-opacity-25 shadow-sm"
                                style="font-size: 0.7rem; min-width: 85px;">
                                <i class="bi bi-dot me-1"></i>{{ $statusLabel }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group shadow-sm rounded-3">
                                @if ($isCritical || $isExpired)
                                    <button class="btn btn-white btn-sm text-danger border px-2" title="Proses Retur">
                                        <i class="bi bi-arrow-return-left"></i>
                                    </button>
                                @else
                                    <button class="btn btn-white btn-sm text-primary border px-2"
                                        title="Prioritas FEFO">
                                        <i class="bi bi-lightning-charge"></i>
                                    </button>
                                @endif
                                <button class="btn btn-white btn-sm text-dark border px-2" title="Detail Batch">
                                    <i class="bi bi-info-circle"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<style>
    .extra-small {
        font-size: 0.7rem;
    }

    .btn-white {
        background: #fff;
    }

    .btn-white:hover {
        background: #f8f9fa;
    }

    #tableED tbody tr {
        transition: all 0.2s ease;
        cursor: default;
    }

    #tableED tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.015);
        transform: translateY(-1px);
    }
</style>
<script>
    $(function() {
        $("#tableED").DataTable({
            "dom": "t",
            "ordering": true,
            "order": [
                [1, "asc"]
            ], // Urutkan dari tanggal terdekat
            "responsive": true,
            "columnDefs": [{
                    "orderable": false,
                    "targets": [0, 3, 4]
                } // Nonaktifkan sorting di kolom info, status, dan aksi
            ],
            "language": {
                "emptyTable": "Semua produk dalam kondisi aman."
            }
        });
    });
</script>
