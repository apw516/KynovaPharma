<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Medicine;
use App\Models\model_log_transaksi_stok;
use App\Models\model_sediaan_barang;
use App\Models\model_ts_detail;
use App\Models\model_ts_retur_sediaan;
use App\Models\po_detail;
use App\Models\po_header;
use App\Models\Supplier;

class LaporanController extends Controller
{
    public function Index()
    {
        $now = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $date_start = $now->format('Y-m-d');
        $date_end = $end->format('Y-m-d');
        $menu = 'transaksipenjualan';
        $satuan = db::select('select * from mt_satuan');
        return view('Laporan.index_transaksi_penjualan', compact([
            'menu',
            'satuan',
            'date_start',
            'date_end'
        ]));
    }
    public function indexlaporandatapenjualan()
    {
        $now = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $date_start = $now->format('Y-m-d');
        $date_end = $end->format('Y-m-d');
        $menu = 'datapenjualan';
        $satuan = db::select('select * from mt_satuan');
        return view('Laporan.index_laporan_penjualan', compact([
            'menu',
            'satuan',
            'date_start',
            'date_end'
        ]));
    }
    public function indexlaporanpo()
    {
        $now = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $date_start = $now->format('Y-m-d');
        $date_end = $end->format('Y-m-d');
        $menu = 'laporanpurchaseorder';
        $satuan = db::select('select * from mt_satuan');
        return view('Laporan.index_laporan_po', compact([
            'menu',
            'satuan',
            'date_start',
            'date_end'
        ]));
    }
    public function ambillaporantransaksipenjualan(Request $request)
    {
        $awal = $request->tglawal;
        $tglakhir = $request->tglakhir;
        $data = db::select('select a.*,d.`nama` from ts_penjualan_header a
        inner join user d on a.`id_user` = d.id where date(a.`tgl_transaksi`) between ? AND ? ORDER BY a.id DESC', [$awal, $tglakhir]);
        $html = view('Laporan.tabel_laporan_penjualan', compact(['data', 'awal', 'tglakhir']))->render();
        $response = [
            'code' => 200,
            'view' => $html,
            'message' => 'sukses'
        ];
        echo json_encode($response);
        die;
    }
    public function ambildetaillaporantransaksi(Request $request)
    {
        $id = $request->id;
        $tglakhir = $request->tglakhir;
        $data = db::select('select b.id as iddetail ,c.`nama_dagang`,d.`nama`,b.`qty`,b.`subtotal`,b.`grandtotal`,b.`harga_jual`,b.`diskon`,b.status_retur,c.satuan_besar,c.satuan_sedang,c.satuan_kecil,c.rasio_sedang,c.rasio_kecil from ts_penjualan_header a
        inner join ts_penjualan_detail b on a.id = b.`id_header`
        inner join mt_barang c on b.`kode_barang` = c.`kode_barang`
        inner join user d on a.`id_user` = d.id where a.id = ? ORDER BY a.id DESC', [$id]);
        //  $html = view('Kasir.tabel_detail_transaksi_kasir', compact(['data']))->render();
        // $response = [
        //     'code' => 200,
        //     'view' => $html,
        //     'message' => 'sukses'
        // ];
        // echo json_encode($response);
        // die;
        $html = view('Laporan.tabel_detail_laporan_transaksi_penjualan', compact(['data']))->render();
        $response = [
            'code' => 200,
            'view' => $html,
            'message' => 'sukses'
        ];
        echo json_encode($response);
        die;
    }
    public function ambildatapurchaseorder(Request $request)
    {
        try {
            // 1. Ambil input tanggal
            $tgl_awal = $request->tglawal;
            $tgl_akhir = $request->tglakhir;

            // 2. Query data PO berdasarkan range tanggal
            // Menggunakan whereBetween untuk performa lebih baik
            $data_po = po_header::whereBetween('tanggal_pembelian', [$tgl_awal, $tgl_akhir])
                ->orderBy('tanggal_pembelian', 'desc')
                ->get();

            // 3. Cek apakah data ada
            if ($data_po->isEmpty()) {
                return response()->json([
                    'kode' => 500,
                    'message' => 'Tidak ada data ditemukan pada periode tersebut.'
                ]);
            }

            // 4. Render partial view menjadi string HTML
            // Kita kirim data ke file blade khusus untuk baris tabel
            $view = view('Laporan.tabel_data_po', compact('data_po'))->render();

            return response()->json([
                'kode' => 200,
                'message' => 'Data berhasil dimuat.',
                'view' => $view
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'kode' => 500,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }
    public function indexlaporanlogkartustok(Request $request)
    {
        $now = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $date_start = $now->format('Y-m-d');
        $date_end = $end->format('Y-m-d');
        $menu = 'laporanlogkartustok';
        $satuan = db::select('select * from mt_satuan');
        return view('Gudang.index_log_stok', compact([
            'menu',
            'satuan',
            'date_start',
            'date_end'
        ]));
    }
    public function indexlaporanstokretur(Request $request)
    {
        $now = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $date_start = $now->format('Y-m-d');
        $date_end = $end->format('Y-m-d');
        $menu = 'laporanstokretur';
        $satuan = db::select('select * from mt_satuan');
        return view('Gudang.index_stok_retur', compact([
            'menu',
            'satuan',
            'date_start',
            'date_end'
        ]));
    }
    public function indexlaporanstokpersediaan(Request $request)
    {
        $now = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $date_start = $now->format('Y-m-d');
        $date_end = $end->format('Y-m-d');
        $menu = 'laporanstokpersediaan';
        $satuan = db::select('select * from mt_satuan');
        return view('Laporan.index_stok_sediaan', compact([
            'menu',
            'satuan',
            'date_start',
            'date_end'
        ]));
    }
    public function indexlaporansesikasir(Request $request)
    {
        $now = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $date_start = $now->format('Y-m-d');
        $date_end = $end->format('Y-m-d');
        $menu = 'laporansesikasir';
        $date = $this->get_date2();
        $get_sesi = db::select('select * from ts_sesi_kasir where date(tgl_sesi_kasir) = ? and status = ?', [$date, 1]);
        return view('Laporan.indexlogsesikasir', compact([
            'menu',
            'date_start',
            'date_end',
            'get_sesi'
        ]));
    }
    public function ambildatalaporansesikasir(Request $request)
    {
        $awal = $request->tglawal;
        $tglakhir = $request->tglakhir;
        $data = db::select('select a.*,b.nama from ts_sesi_kasir a inner join user b on a.id_user = b.id where date(a.tgl_sesi_kasir) BETWEEN ? and ? ORDER BY a.id DESC', [$awal, $tglakhir]);
        $html = view('Laporan.tabel_log_kasir', compact(['data', 'awal', 'tglakhir']))->render();
        $response = [
            'code' => 200,
            'view' => $html,
            'message' => 'sukses'
        ];
        echo json_encode($response);
        die;
    }
    public function get_date2()
    {
        $dt = Carbon::now()->timezone('Asia/Jakarta');
        $date = $dt->toDateString();
        $time = $dt->toTimeString();
        $now = $date;
        return $now;
    }
}
