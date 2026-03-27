<div class="card border-0 shadow-sm text-center">
    <div class="card-body p-5">
        <div class="mb-4">
            <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
        </div>

        <h2 class="fw-bold text-dark">Terima Kasih!</h2>
        <p class="text-muted mb-4">Transaksi telah berhasil diproses.</p>

        <div class="bg-light p-4 rounded-3 mb-4">
            <div class="row mb-2">
                <div class="col-6 text-start text-secondary">Total Belanja</div>
                <div class="col-6 text-end fw-bold">Rp {{ number_format($gt, 0, ',', '.') }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-6 text-start text-secondary">Tunai / Bayar</div>
                <div class="col-6 text-end fw-bold">Rp {{ number_format($uang, 0, ',', '.') }}</div>
            </div>
            <hr>
            <div class="row">
                <div class="col-6 text-start d-flex align-items-center">
                    <span class="h5 mb-0 fw-bold text-primary">Kembalian</span>
                </div>
                <div class="col-6 text-end">
                    <span class="h4 mb-0 fw-bold text-success">
                        Rp {{ number_format($uang - $gt, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>

        <div class="d-grid gap-2 d-md-flex justify-content-md-center">
            <button type="button" class="btn btn-outline-primary px-4" onclick="location.reload();">
                <i class="bi bi-plus-lg me-2"></i>Transaksi Baru
            </button>
            <button type="button" class="btn btn-success px-4" onclick="window.print();">
                <i class="bi bi-printer me-2"></i>Cetak Struk
            </button>
        </div>
    </div>
</div>

<style>
    @media print {

        .btn,
        .navbar,
        .sidebar {
            display: none !important;
        }

        .card {
            border: none !important;
            shadow: none !important;
        }
    }
</style>
