@if ($stok_sekarang > 0)
    <div id="alert-stok-kontainer" style="display: ;" class="mt-2">
        <div class="alert alert-success d-flex align-items-center py-2" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <div id="text-alert-stok">Stok {{ $databarang->nama_dagang }} = {{ $stok_sekarang }}
                {{ $databarang->satuan_kecil }} </div>
        </div>
    </div>
@else
    <div id="alert-stok-kontainer" style="display: ;" class="mt-2">
        <div class="alert alert-danger d-flex align-items-center py-2" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <div id="text-alert-stok"> Stok {{ $databarang->nama_dagang }} Tidak ditemukan ! ( Silahkan isi stok yang tersedia ... )</div>
        </div>
    </div>
@endif
