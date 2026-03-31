<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class DashboardController extends Controller
{
    public function indexdashboardkeuangan()
    {
        $menu = 'Dashboardkeuangan';
        // 1. Total Penjualan (Bulan Ini)
        // 1. Total Penjualan Bulan Ini
        $totalSales = DB::table('ts_penjualan_header')
            ->whereMonth('tgl_transaksi', Carbon::now()->month)
            ->whereYear('tgl_transaksi', Carbon::now()->year)
            ->where('status', '1')
            ->sum('total_bayar');

        // 2. Total Penjualan Bulan Lalu (Gunakan subMonth)
        $lastMonthSales = DB::table('ts_penjualan_header')
            ->whereMonth('tgl_transaksi', Carbon::now()->subMonth()->month)
            ->whereYear('tgl_transaksi', Carbon::now()->subMonth()->year)
            ->where('status', '1')
            ->sum('total_bayar');

        // 3. Hitung Selisih & Persentase
        $salesTrend = 0;
        if ($lastMonthSales > 0) {
            // Rumus: ((Bulan Ini - Bulan Lalu) / Bulan Lalu) * 100
            $salesTrend = (($totalSales - $lastMonthSales) / $lastMonthSales) * 100;
        } else {
            // Jika bulan lalu 0 tapi bulan ini ada penjualan, maka kenaikan dianggap 100%
            $salesTrend = $totalSales > 0 ? 100 : 0;
        }

        // 2. Estimasi Laba Kotor (Total Jual - Total HPP)
        // Logika: Ambil total harga jual dikurangi total harga beli dari detail penjualan
        // $grossProfit = DB::table('ts_penjualan_detail')
        //     ->join('ts_penjualan_header', 'ts_penjualan_detail.id_header', '=', 'ts_penjualan_header.id')
        //     ->whereMonth('ts_penjualan_header.tgl_transaksi', Carbon::now()->month)
        //     ->where('ts_penjualan_header.status', '1')
        //     ->select(DB::raw('SUM(grandtotal - (harga_modal * qty)) as profit'))
        //     ->first()->profit ?? 0;

        $financeData = DB::table('ts_penjualan_detail')
            ->join('ts_penjualan_header', 'ts_penjualan_detail.id_header', '=', 'ts_penjualan_header.id')
            ->whereMonth('ts_penjualan_header.tgl_transaksi', Carbon::now()->month)
            ->whereYear('ts_penjualan_header.tgl_transaksi', Carbon::now()->year)
            ->where('ts_penjualan_header.status', '1')
            ->select(
                DB::raw('SUM(grandtotal) as total_revenue'),
                DB::raw('SUM(grandtotal - (harga_modal * qty)) as total_profit')
            )
            ->first();

        $grossProfit = $financeData->total_profit ?? 0;
        $totalRevenue = $financeData->total_revenue ?? 0;

        // Hitung Margin Rata-rata
        $marginPercentage = ($totalRevenue > 0)
            ? ($grossProfit / $totalRevenue) * 100
            : 0;



        // 3. Hutang Jatuh Tempo (Ke PBF/Supplier)
        // Mengambil tagihan yang belum lunas dan jatuh tempo <= hari ini
        $accountPayable = DB::table('ts_po_header')
            ->where('status_pembayaran', '0') // 0 = Belum Lunas/Kredit
            ->whereDate('tanggal_pembayaran', '<=', Carbon::now())
            ->sum('grand_total');

        // 4. Saldo Kas/Bank (Contoh dari tabel arus kas)
        // Logika: Total Masuk - Total Keluar
        $cashBalance = DB::table('ts_penjualan_header')
            ->select(DB::raw('SUM(nominal_terima) - SUM(nominal_kembali) as saldo'))
            ->where('status', '1') // Anggap 1 adalah Lunas/Sukses
            ->first()->saldo ?? 0;

        // 5. Data Tambahan untuk Tabel Jatuh Tempo (Bawah)
        $pendingPayments = DB::table('ts_po_header as ph')
            ->join('mt_supplier as s', 'ph.kode_supplier', '=', 's.kode_supplier')
            ->select('s.nama_supplier as nama_pbf', 'ph.nomor_faktur', 'ph.tanggal_pembayaran as jatuh_tempo', 'ph.grand_total as total')
            ->where('ph.status_bayar', '0')
            ->orderBy('ph.tanggal_pembayaran', 'asc')
            ->limit(5)
            ->get();

        // Ambil data arus kas 7 hari terakhir
        $cashflow = DB::table('ts_penjualan_header')
            ->select(
                DB::raw('DATE(tgl_transaksi) as tgl_transaksi'),
                DB::raw('SUM(nominal_terima) as nominal_terima'),
                DB::raw('SUM(nominal_kembali) as nominal_kembali')
            )
            ->where('tgl_transaksi', '>=', now()->subDays(6))
            ->where('status', '=', 1)
            ->groupBy('tgl_transaksi')
            ->orderBy('tgl_transaksi', 'asc')
            ->get();

        // Format data untuk Chart.js
        $labels = [];
        $dataMasuk = [];
        $dataKeluar = [];

        // Loop untuk memastikan setiap hari dalam 7 hari terakhir ada (meskipun nol)
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $labels[] = now()->subDays($i)->isoFormat('ddd'); // Format hari: Sen, Sel, dst.

            $row = $cashflow->where('tgl_transaksi', $date)->first();
            $dataMasuk[] = $row ? $row->nominal_terima : 0;
            $dataKeluar[] = $row ? $row->nominal_kembali : 0;
        }
        return view('Dashboard.indexdashboardkeuangan', compact([
            'menu',
            'pendingPayments',
            'cashBalance',
            'accountPayable',
            'grossProfit',
            'totalSales',
            'labels',
            'dataMasuk',
            'dataKeluar',
            'marginPercentage',
            'salesTrend'
        ]));
    }
    public function Index()
    {
        $now = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $date_start = $now->format('Y-m-d');
        $date_end = $end->format('Y-m-d');
        $menu = 'Dashboard';
        //stok ed
        $hari_analisis = 30; // Rentang waktu untuk hitung kecepatan
        $lead_time = 3;      // Waktu tunggu supplier (hari)
        $tanggal_mulai = \Carbon\Carbon::now()->subDays($hari_analisis);

        // 1. Subquery untuk mengambil ID log terakhir per barang
        $latestLogIds = DB::table('log_transaksi_stok')
            ->select(DB::raw('MAX(id) as id'))
            ->groupBy('kode_barang');

        // 2. Query Utama Dashboard
        $stokKritis = DB::table('mt_barang as mb')
            // Join ke saldo terbaru berdasarkan ID terakhir tadi
            ->join('log_transaksi_stok as lks', function ($join) use ($latestLogIds) {
                $join->on('mb.kode_barang', '=', 'lks.kode_barang')
                    ->whereIn('lks.id', $latestLogIds);
            })
            // Join ke penjualan untuk hitung rata-rata kecepatan (Safety Stock)
            ->leftJoin('ts_penjualan_detail as pd', function ($join) use ($tanggal_mulai) {
                $join->on('mb.kode_barang', '=', 'pd.kode_barang')
                    ->where('pd.status_retur', '=', 1);
            })
            ->leftJoin('ts_penjualan_header as ph', function ($join) use ($tanggal_mulai) {
                $join->on('pd.id_header', '=', 'ph.id')
                    ->where('ph.tgl_transaksi', '>=', $tanggal_mulai)
                    ->where('ph.status', '=', '1');
            })
            ->select(
                'mb.kode_barang',
                'mb.nama_dagang',
                'mb.satuan_kecil',
                'lks.stok_now as stok_sekarang',
                // Hitung Kecepatan Harian
                DB::raw('ROUND(SUM(IFNULL(pd.qty, 0)) / ' . $hari_analisis . ', 2) as kecepatan_harian'),
                // Hitung Safety Stock (Min. Stok yang harus ada)
                DB::raw('ROUND((SUM(IFNULL(pd.qty, 0)) / ' . $hari_analisis . ') * ' . $lead_time . ', 0) as safety_stock')
            )
            ->groupBy('mb.kode_barang', 'mb.nama_dagang', 'mb.satuan_kecil', 'lks.stok_now')
            // FILTER UTAMA: Hanya tampilkan jika stok <= safety stock
            ->havingRaw('stok_sekarang <= ROUND((SUM(IFNULL(pd.qty, 0)) / ' . $hari_analisis . ') * ' . $lead_time . ', 0)')
            // Tambahan: tampilkan yang stoknya benar-benar mau habis (misal sisa < 10) meskipun slow moving
            ->orHaving('stok_sekarang', '<=', 10)
            ->orderBy('stok_sekarang', 'asc')
            ->get();


        //hampired
        // Batasan monitoring: 6 bulan (180 hari) ke depan
        $limit_hari = 180;

        $dataED = DB::table('mt_sediaan_obat as s')
            ->join('mt_barang as mb', 's.kode_barang', '=', 'mb.kode_barang')
            ->join('mt_supplier as c', 's.kode_supplier', '=', 'c.kode_supplier')
            ->select(
                'mb.nama_dagang',
                'mb.satuan_kecil',
                's.kode_batch',
                'c.nama_supplier',
                's.tgl_expired',
                's.stok_sekarang as stok_sediaan', // Kolom stok di tabel sediaan Anda
                DB::raw('DATEDIFF(s.tgl_expired, NOW()) as sisa_hari')
            )
            ->where('s.stok_sekarang', '>', 0) // Hanya tampilkan yang stoknya masih ada
            ->whereRaw('DATEDIFF(s.tgl_expired, NOW()) <= ?', [$limit_hari])
            ->orderBy('s.tgl_expired', 'asc')
            ->get();


        $salesData = DB::table('ts_penjualan_header')
            ->select(
                DB::raw('DATE(tgl_transaksi) as tanggal'),
                DB::raw('SUM(total_bayar) as omzet')
            )
            ->where('tgl_transaksi', '>=', now()->subDays(6))
            ->where('status', '1')
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'asc')
            ->get();

        $labels = $salesData->pluck('tanggal')->map(function ($tgl) {
            return date('d M', strtotime($tgl));
        });
        $totals = $salesData->pluck('omzet');


        $topKategori = DB::table('ts_penjualan_detail as pd')
            ->join('mt_barang as mb', 'pd.kode_barang', '=', 'mb.kode_barang')
            // ->join('mt_kategori as mk', 'mb.id_kategori', '=', 'mk.id') // Sesuaikan nama tabel kategori Anda
            ->select('mb.nama_obat', DB::raw('SUM(pd.subtotal) as total'))
            ->where('status_retur', '=', 1)
            ->groupBy('mb.nama_obat')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        $katLabels = $topKategori->pluck('nama_obat');
        $katTotals = $topKategori->pluck('total');



        $notif_ed = DB::table('mt_sediaan_obat')
            ->whereBetween(DB::raw('DATE(tgl_expired)'), [now(), now()->addDays(90)])
            ->count();

        // Ambil semua PO yang belum Lunas (Tanpa batas tanggal awal)
        $hutang_data = DB::table('ts_po_header')
            ->where('status_bayar', '!=', '1')
            ->where('status', 1) // 1 = PO Aktif/Disetujui
            ->get();

        // Hitung total notif untuk Alert
        $notif_hutang = $hutang_data->count();

        // Pisahkan mana yang sudah lewat (Overdue) dan mana yang mendekati (Jatuh Tempo)
        $overdue_count = $hutang_data->where('tanggal_pembayaran', '<', now()->format('Y-m-d'))->count();
        $upcoming_count = $hutang_data->where('tanggal_pembayaran', '>=', now()->format('Y-m-d'))->count();

        $total_notif = $notif_ed + $notif_hutang;

        return view('Dashboard.index', compact([
            'menu',
            'date_start',
            'date_end',
            'stokKritis',
            'dataED',
            'labels',
            'totals',
            'katLabels',
            'katTotals',
            'notif_ed',
            'hutang_data',
            'notif_hutang',
            'overdue_count',
            'upcoming_count',
            'total_notif'
        ]));
    }
    public function indexringkasankeuangan()
    {
        $now = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $date_start = $now->format('Y-m-d');
        $date_end = $end->format('Y-m-d');
        $menu = 'ringkasankeuangan';
        return view('Dashboard.index_ringkasan_keuangan', compact([
            'menu',
            'date_start',
            'date_end',
        ]));
    }
    public function cetakLaporan()
    {
        // 1. Data Stok Kritis
        $stokKritis = $this->getDataStokKritis(); // Gunakan query yang kita buat sebelumnya

        // 2. Data Expired 
        $dataED = $this->getDataED(); // Gunakan query yang kita buat sebelumnya

        // 3. Ringkasan Penjualan 7 Hari Terakhir
        $omzetMingguan = DB::table('ts_penjualan_header')
            ->where('tgl_transaksi', '>=', now()->subDays(7))
            ->where('status', '1')
            ->sum('total_bayar');

        $data = [
            'title' => 'LAPORAN MINGGUAN KYNOVAPHARMA',
            'date' => date('d/m/Y'),
            'stokKritis' => $stokKritis,
            'dataED' => $dataED,
            'omzet' => $omzetMingguan
        ];

        // Load view khusus PDF
        $pdf = Pdf::loadView('Laporan.Dashboard_pdf', $data);

        // Download atau Stream (tampil di browser)
        return $pdf->stream('Laporan_Mingguan_' . date('Ymd') . '.pdf');
    }
    public function getDataStokKritis()
    {
        //stok ed
        $hari_analisis = 30; // Rentang waktu untuk hitung kecepatan
        $lead_time = 3;      // Waktu tunggu supplier (hari)
        $tanggal_mulai = \Carbon\Carbon::now()->subDays($hari_analisis);

        // 1. Subquery untuk mengambil ID log terakhir per barang
        $latestLogIds = DB::table('log_transaksi_stok')
            ->select(DB::raw('MAX(id) as id'))
            ->groupBy('kode_barang');

        // 2. Query Utama Dashboard
        $stokKritis = DB::table('mt_barang as mb')
            // Join ke saldo terbaru berdasarkan ID terakhir tadi
            ->join('log_transaksi_stok as lks', function ($join) use ($latestLogIds) {
                $join->on('mb.kode_barang', '=', 'lks.kode_barang')
                    ->whereIn('lks.id', $latestLogIds);
            })
            // Join ke penjualan untuk hitung rata-rata kecepatan (Safety Stock)
            ->leftJoin('ts_penjualan_detail as pd', function ($join) use ($tanggal_mulai) {
                $join->on('mb.kode_barang', '=', 'pd.kode_barang')
                    ->where('pd.status_retur', '=', 1);
            })
            ->leftJoin('ts_penjualan_header as ph', function ($join) use ($tanggal_mulai) {
                $join->on('pd.id_header', '=', 'ph.id')
                    ->where('ph.tgl_transaksi', '>=', $tanggal_mulai)
                    ->where('ph.status', '=', '1');
            })
            ->select(
                'mb.kode_barang',
                'mb.nama_dagang',
                'mb.satuan_kecil',
                'lks.stok_now as stok_sekarang',
                // Hitung Kecepatan Harian
                DB::raw('ROUND(SUM(IFNULL(pd.qty, 0)) / ' . $hari_analisis . ', 2) as kecepatan_harian'),
                // Hitung Safety Stock (Min. Stok yang harus ada)
                DB::raw('ROUND((SUM(IFNULL(pd.qty, 0)) / ' . $hari_analisis . ') * ' . $lead_time . ', 0) as safety_stock')
            )
            ->groupBy('mb.kode_barang', 'mb.nama_dagang', 'mb.satuan_kecil', 'lks.stok_now')
            // FILTER UTAMA: Hanya tampilkan jika stok <= safety stock
            ->havingRaw('stok_sekarang <= ROUND((SUM(IFNULL(pd.qty, 0)) / ' . $hari_analisis . ') * ' . $lead_time . ', 0)')
            // Tambahan: tampilkan yang stoknya benar-benar mau habis (misal sisa < 10) meskipun slow moving
            ->orHaving('stok_sekarang', '<=', 10)
            ->orderBy('stok_sekarang', 'asc')
            ->get();
        return $stokKritis;
    }
    public function getDataED()
    {
        $limit_hari = 180;

        $dataED = DB::table('mt_sediaan_obat as s')
            ->join('mt_barang as mb', 's.kode_barang', '=', 'mb.kode_barang')
            ->join('mt_supplier as c', 's.kode_supplier', '=', 'c.kode_supplier')
            ->select(
                'mb.nama_dagang',
                'mb.satuan_kecil',
                's.kode_batch',
                'c.nama_supplier',
                's.tgl_expired',
                's.stok_sekarang as stok_sediaan', // Kolom stok di tabel sediaan Anda
                DB::raw('DATEDIFF(s.tgl_expired, NOW()) as sisa_hari')
            )
            ->where('s.stok_sekarang', '>', 0) // Hanya tampilkan yang stoknya masih ada
            ->whereRaw('DATEDIFF(s.tgl_expired, NOW()) <= ?', [$limit_hari])
            ->orderBy('s.tgl_expired', 'asc')
            ->get();

        return $dataED;
    }
    public function ambildatatabelanalisisprodukfastmoving(Request $request)
    {
        $hari_analisis = intval($request->input('tanggal', 30));
        $tanggal_mulai = \Carbon\Carbon::now()->subDays($hari_analisis);
        // Subquery untuk mengambil saldo terakhir dari kartu stok
        $latestStock = DB::table('log_transaksi_stok')
            ->select('kode_barang', 'stok_now')
            ->whereIn('id', function ($query) {
                $query->select(DB::raw('MAX(id)'))
                    ->from('log_transaksi_stok')
                    ->groupBy('kode_barang');
            });

        // $produkFastMoving = DB::table('mt_barang as mb')
        //     // 1. Join ke saldo stok terbaru (Subquery)
        //     ->leftJoinSub($latestStock, 'ls', function ($join) {
        //         $join->on('mb.kode_barang', '=', 'ls.kode_barang');
        //     })
        //     // 2. Join ke Detail Penjualan dengan filter status_retur = 1
        //     ->leftJoin('ts_penjualan_detail as pd', function ($join) {
        //         $join->on('mb.kode_barang', '=', 'pd.kode_barang')
        //             ->where('pd.status_retur', '=', 1); // Hanya yang terjual (bukan retur)
        //     })
        //     // 3. Join ke Header Penjualan untuk cek status transaksi dan tanggal
        //     ->leftJoin('ts_penjualan_header as ph', function ($join) use ($tanggal_mulai) {
        //         $join->on('pd.id_header', '=', 'ph.id')
        //             ->where('ph.tgl_transaksi', '>=', $tanggal_mulai)
        //             ->where('ph.status', '=', '1'); // Pastikan status transaksi sukses/final
        //     })
        //     ->select(
        //         'mb.id',
        //         'mb.kode_barang',
        //         'mb.nama_obat',
        //         'mb.nama_dagang',
        //         'mb.satuan_kecil',
        //         'mb.sediaan',
        //         'mb.harga_modal',
        //         DB::raw('IFNULL(ls.stok_now, 0) as stok_aktual'),
        //         DB::raw('SUM(IFNULL(pd.qty, 0)) as total_terjual'),
        //         DB::raw('ROUND(SUM(IFNULL(pd.qty, 0)) / ' . max(1, $hari_analisis) . ', 2) as kecepatan_harian')
        //     )
        //     ->groupBy(
        //         'mb.id',
        //         'mb.kode_barang',
        //         'mb.nama_obat',
        //         'mb.nama_dagang',
        //         'mb.satuan_kecil',
        //         'mb.sediaan',
        //         'mb.harga_modal',
        //         'ls.stok_now'
        //     )
        //     ->orderBy('total_terjual', 'desc')
        //     ->limit(10)
        //     ->get();
        $lead_time = 3; // Asumsi 3 hari barang sampai setelah dipesan

        $produkFastMoving = DB::table('mt_barang as mb')
            ->leftJoinSub($latestStock, 'ls', function ($join) {
                $join->on('mb.kode_barang', '=', 'ls.kode_barang');
            })
            ->leftJoin('ts_penjualan_detail as pd', function ($join) {
                $join->on('mb.kode_barang', '=', 'pd.kode_barang')
                    ->where('pd.status_retur', '=', 1);
            })
            ->leftJoin('ts_penjualan_header as ph', function ($join) use ($tanggal_mulai) {
                $join->on('pd.id_header', '=', 'ph.id')
                    ->where('ph.tgl_transaksi', '>=', $tanggal_mulai)
                    ->where('ph.status', '=', '1');
            })
            ->select(
                'mb.id',
                'mb.kode_barang',
                'mb.nama_obat',
                'mb.nama_dagang',
                'mb.satuan_kecil',
                'mb.harga_modal',
                DB::raw('IFNULL(ls.stok_now, 0) as stok_aktual'),
                DB::raw('SUM(IFNULL(pd.qty, 0)) as total_terjual'),
                // Hitung Kecepatan
                DB::raw('ROUND(SUM(IFNULL(pd.qty, 0)) / ' . max(1, $hari_analisis) . ', 2) as kecepatan_harian'),
                // Hitung Safety Stock (Kecepatan * Lead Time)
                DB::raw('ROUND((SUM(IFNULL(pd.qty, 0)) / ' . max(1, $hari_analisis) . ') * ' . $lead_time . ', 0) as safety_stock')
            )
            ->groupBy('mb.id', 'mb.kode_barang', 'mb.nama_obat', 'mb.nama_dagang', 'mb.satuan_kecil', 'mb.harga_modal', 'ls.stok_now')
            ->orderBy('total_terjual', 'desc')
            ->limit(50)
            ->get();

        // 2. Ringkasan Widget (Gunakan data dari kartu stok)
        $totalFastMovingItem = $produkFastMoving->where('total_terjual', '>', 50)->count();
        $stokKritis = $produkFastMoving->where('stok_aktual', '<=', 20)->count();
        // DD($produkFastMoving);
        $view =  view('Dashboard.AnalisisProduk', [
            'data' => $produkFastMoving,
            'summary' => [
                'fast_moving_count' => $totalFastMovingItem,
                'stok_kritis' => $stokKritis,
                'estimasi_biaya' => $produkFastMoving->where('stok_aktual', '<=', 20)->sum(fn($i) => $i->harga_modal * 50),
                'periode' => $hari_analisis
            ]
        ])->render();
        return response()->json([
            'kode' => 200,
            'message' => 'Data berhasil dimuat.',
            'view' => $view
        ]);
    }
    public function ambildatabaranghampirhabis()
    {
        $hari_analisis = 30; // Rentang waktu untuk hitung kecepatan
        $lead_time = 3;      // Waktu tunggu supplier (hari)
        $tanggal_mulai = \Carbon\Carbon::now()->subDays($hari_analisis);

        // 1. Subquery untuk mengambil ID log terakhir per barang
        $latestLogIds = DB::table('log_transaksi_stok')
            ->select(DB::raw('MAX(id) as id'))
            ->groupBy('kode_barang');

        // 2. Query Utama Dashboard
        $stokKritis = DB::table('mt_barang as mb')
            // Join ke saldo terbaru berdasarkan ID terakhir tadi
            ->join('log_transaksi_stok as lks', function ($join) use ($latestLogIds) {
                $join->on('mb.kode_barang', '=', 'lks.kode_barang')
                    ->whereIn('lks.id', $latestLogIds);
            })
            // Join ke penjualan untuk hitung rata-rata kecepatan (Safety Stock)
            ->leftJoin('ts_penjualan_detail as pd', function ($join) use ($tanggal_mulai) {
                $join->on('mb.kode_barang', '=', 'pd.kode_barang')
                    ->where('pd.status_retur', '=', 1);
            })
            ->leftJoin('ts_penjualan_header as ph', function ($join) use ($tanggal_mulai) {
                $join->on('pd.id_header', '=', 'ph.id')
                    ->where('ph.tgl_transaksi', '>=', $tanggal_mulai)
                    ->where('ph.status', '=', '1');
            })
            ->select(
                'mb.kode_barang',
                'mb.nama_dagang',
                'mb.satuan_kecil',
                'lks.stok_now as stok_sekarang',
                // Hitung Kecepatan Harian
                DB::raw('ROUND(SUM(IFNULL(pd.qty, 0)) / ' . $hari_analisis . ', 2) as kecepatan_harian'),
                // Hitung Safety Stock (Min. Stok yang harus ada)
                DB::raw('ROUND((SUM(IFNULL(pd.qty, 0)) / ' . $hari_analisis . ') * ' . $lead_time . ', 0) as safety_stock')
            )
            ->groupBy('mb.kode_barang', 'mb.nama_dagang', 'mb.satuan_kecil', 'lks.stok_now')
            // FILTER UTAMA: Hanya tampilkan jika stok <= safety stock
            ->havingRaw('stok_sekarang <= ROUND((SUM(IFNULL(pd.qty, 0)) / ' . $hari_analisis . ') * ' . $lead_time . ', 0)')
            // Tambahan: tampilkan yang stoknya benar-benar mau habis (misal sisa < 10) meskipun slow moving
            ->orHaving('stok_sekarang', '<=', 10)
            ->orderBy('stok_sekarang', 'asc')
            ->get();
        // dd($stokKritis);
        $view =  view('Dashboard.tabel_stok_kritis', compact([
            'stokKritis'
        ]))->render();
        return response()->json([
            'kode' => 200,
            'message' => 'Data berhasil dimuat.',
            'view' => $view
        ]);
    }
    public function ambildatabaranghampired()
    {
        // Batasan monitoring: 6 bulan (180 hari) ke depan
        $limit_hari = 180;

        $dataED = DB::table('mt_sediaan_obat as s')
            ->join('mt_barang as mb', 's.kode_barang', '=', 'mb.kode_barang')
            ->join('mt_supplier as c', 's.kode_supplier', '=', 'c.kode_supplier')
            ->select(
                'mb.nama_dagang',
                'mb.satuan_kecil',
                's.kode_batch',
                'c.nama_supplier',
                's.tgl_expired',
                's.stok_sekarang as stok_sediaan', // Kolom stok di tabel sediaan Anda
                DB::raw('DATEDIFF(s.tgl_expired, NOW()) as sisa_hari')
            )
            ->where('s.stok_sekarang', '>', 0) // Hanya tampilkan yang stoknya masih ada
            ->whereRaw('DATEDIFF(s.tgl_expired, NOW()) <= ?', [$limit_hari])
            ->orderBy('s.tgl_expired', 'asc')
            ->get();
        $view =  view('Dashboard.tabel_stok_hampir_ed', compact([
            'dataED'
        ]))->render();
        return response()->json([
            'kode' => 200,
            'message' => 'Data berhasil dimuat.',
            'view' => $view
        ]);
    }
    public function ambilringkasankeuangan(Request $request)
    {
        $awal = $request->tglawal;
        $akhir = $request->tglakhir;

        // $total_po = db::select('SELECT * FROM ts_po_header where date(tanggal_pembelian) BETWEEN ?  AND ?  AND status = 1', [$awal, $akhir]);
        $total_po = DB::table('ts_po_header')
            ->whereBetween(DB::raw('DATE(tanggal_pembelian)'), [$awal, $akhir])
            ->where('status', 1)
            ->orderBy('tanggal_pembelian', 'desc')
            ->get();
        // $total_transaksi = db::select('SELECT * FROM ts_penjualan_header where date(tgl_transaksi) BETWEEN ?  AND ?  AND status = 1', [$awal, $akhir]);
        $total_transaksi = DB::table('ts_penjualan_header')
            ->whereBetween(DB::raw('DATE(tgl_transaksi)'), [$awal, $akhir])
            ->where('status', 1)
            ->orderBy('tgl_transaksi', 'desc')
            ->get();

        // $data_penjualan = db::select('SELECT b.*,c.no_invoice,c.`tgl_transaksi`,d.`nama_dagang` FROM ts_penjualan_detail b 
        // INNER JOIN ts_penjualan_header c ON b.`id_header` = c.`id`
        // INNER JOIN mt_barang d ON b.`kode_barang` = d.`kode_barang`
        // WHERE DATE(c.`tgl_transaksi`) BETWEEN ?  AND ? AND b.`status_retur` = 1', [$awal, $akhir]);
        $data_penjualan = DB::table('ts_penjualan_detail as b')
            ->join('ts_penjualan_header as c', 'b.id_header', '=', 'c.id')
            ->join('mt_barang as d', 'b.kode_barang', '=', 'd.kode_barang')
            ->select(
                'b.*',
                'c.no_invoice',
                'c.tgl_transaksi',
                'd.nama_dagang'
            )
            ->whereBetween(DB::raw('DATE(c.tgl_transaksi)'), [$awal, $akhir])
            ->where('b.status_retur', 1)
            ->orderBy('c.tgl_transaksi', 'desc')
            ->get();

        // $log_transaksi_kasir = db::select('SELECT * FROM ts_sesi_kasir a inner join user b on a.id_user = b.id where date(tgl_sesi_kasir) BETWEEN ?  AND ?',[$awal,$akhir]);
        $log_transaksi_kasir = DB::table('ts_sesi_kasir as a')
            ->join('user as b', 'a.id_user', '=', 'b.id')
            ->select('a.*', 'b.nama', 'b.username') // Sesuaikan kolom yang ingin diambil dari tabel user
            ->whereBetween(DB::raw('DATE(a.tgl_sesi_kasir)'), [$awal, $akhir])
            ->get();
        $view =  view('Dashboard.data_ringkasan_keuangan', compact([
            'total_po',
            'log_transaksi_kasir',
            'total_transaksi',
            'data_penjualan'
        ]))->render();
        return response()->json([
            'kode' => 200,
            'message' => 'Data berhasil dimuat.',
            'view' => $view
        ]);
    }
}
