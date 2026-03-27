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
use App\Models\model_ts_header;
use App\Models\model_ts_sesi_kasir;
use App\Models\po_detail;
use App\Models\po_header;
use App\Models\Supplier;

class KasirController extends Controller
{
    public function Index()
    {
        $now = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $date_start = $now->format('Y-m-d');
        $date_end = $end->format('Y-m-d');
        $menu = 'kasir';
        $date = $this->get_date2();
        $get_sesi = db::select('select * from ts_sesi_kasir where date(tgl_sesi_kasir) = ? and status = ?', [$date, 1]);
        return view('Kasir.index', compact([
            'menu',
            'date_start',
            'date_end',
            'get_sesi'
        ]));
    }
    public function prosesbarang(Request $request)
    {
        $data = json_decode($_POST['data'], true);
        foreach ($data as $nama2) {
            $index2 = $nama2['name'];
            $value2 = $nama2['value'];
            $dataSet2[$index2] = $value2;
            if ($index2 == 'harga') {
                $arrayobat[] = $dataSet2;
            }
        }
        if (empty($arrayobat)) {
            $response = [
                'code' => 500,
                'message' => 'Belum ada obat yang dipilih atau data tidak valid!'
            ];
            echo json_encode($response);
            die;
        }
        $gt = 0;
        foreach ($arrayobat as $d) {
            $mt_barang = db::select('select * from mt_barang where kode_barang = ?', [$d['kode_barang']]);
            $satuan = $d['satuan'];
            if ($satuan == 'besar') {
                $harga_sedang = $d['harga'] * $mt_barang[0]->rasio_kecil;
                $harganya = $harga_sedang * $mt_barang[0]->rasio_sedang;
            } elseif ($satuan == 'sedang') {
                $harganya = $d['harga'] * $mt_barang[0]->rasio_kecil;
            } else {
                $harganya = $d['harga'];
            }

            $subtotal = $d['qty'] * $harganya - $d['diskon'];
            $gt = $gt + $subtotal;
        }
        $v_gt = number_format($gt, 0, ',', '.');
        $html = view('Kasir.hasil_proses', compact(['gt', 'v_gt']))->render();
        $response = [
            'code' => 200,
            'html' => $html,
            'message' => 'sukses'
        ];
        echo json_encode($response);
        die;
    }
    public function get_now()
    {
        $dt = Carbon::now()->timezone('Asia/Jakarta');
        $date = $dt->toDateString();
        $time = $dt->toTimeString();
        $now = $date . ' ' . $time;
        return $now;
    }
    public function get_date2()
    {
        $dt = Carbon::now()->timezone('Asia/Jakarta');
        $date = $dt->toDateString();
        $time = $dt->toTimeString();
        $now = $date;
        return $now;
    }
    public function get_date()
    {
        $dt = Carbon::now()->timezone('Asia/Jakarta');
        $date = $dt->toDateString();
        $time = $dt->toTimeString();
        $now = $date;
        $now = $dt->format('Ymd'); // Hasil: 20260327
        return $now;
    }
    public function prosesbarangfinal(Request $request)
    {
        $gt = $request->gt;
        $uang = $request->uang;
        $id_sesi_kasir = $request->id_sesi_kasir;
        if ($uang < $gt) {
            return response()->json([
                'code' => 500,
                'message' => 'Uang yang dibayar tidak mencukupi!'
            ]);
        }
        $data = json_decode($_POST['data'], true);
        $data2 = json_decode($_POST['data2'], true);
        foreach ($data2 as $nama2) {
            $index2 = $nama2['name'];
            $value2 = $nama2['value'];
            $dataSet2[$index2] = $value2;
            if ($index2 == 'harga') {
                $arrayobat[] = $dataSet2;
            }
        }
        DB::beginTransaction();
        try {
            $no_invoice = $this->get_inv();
            $data_header = [
                'no_invoice' => $no_invoice,
                'tgl_transaksi' => $this->get_now(),
                'id_sesi_kasir' => $id_sesi_kasir,
                'id_user' => auth()->user()->id,
                'total_harga' => $gt,
                'diskon' => 0,
                'pajak_ppn' => 0,
                'total_bayar' => $gt,
                'nominal_terima' => $uang,
                'nominal_kembali' => $uang - $gt,
                'status' => '0',
            ];
            $header = model_ts_header::create($data_header);
            foreach ($arrayobat as $d) {
                $kode_barang = $d['kode_barang'];
                $mt_barang = Medicine::where('kode_barang', $kode_barang)->first();
                $satuan = $d['satuan'];
                if ($satuan == 'besar') {
                    $qtybesar = $mt_barang['rasio_sedang'] * $mt_barang['rasio_kecil'];
                    $qty = $qtybesar * $d['qty'];
                } elseif ($satuan == 'sedang') {
                    $besarnya = $mt_barang['rasio_sedang'] * $mt_barang['rasio_kecil'];
                    $qtysedang = $besarnya / $mt_barang['rasio_sedang'];
                    $qty = $qtysedang * $d['qty'];
                } else {
                    $qty = $d['qty'];
                }
                $diskon = $d['diskon'];
                $nama_barang = $d['nama_barang'];
                $kode_barang = $kode_barang;
                $qty_dibutuhkan = $qty; // Misal: 10
                // Ambil semua sediaan yang stoknya > 0, urutkan dari ED terdekat (FEFO)
                $semua_sediaan = model_sediaan_barang::where('kode_barang', $kode_barang)
                    ->where('stok_sekarang', '>', 0)
                    ->orderBy('tgl_expired', 'asc')
                    ->get();
                foreach ($semua_sediaan as $sediaan) {
                    if ($qty_dibutuhkan <= 0) break; // Jika sudah terpenuhi, berhenti
                    if ($sediaan->stok_sekarang >= $qty_dibutuhkan) {
                        // Jika stok di batch ini cukup untuk menutupi sisa kebutuhan
                        $sediaan->stok_sekarang -= $qty_dibutuhkan;
                        $sediaan->save();
                        // Catat log transaksi (ambil $qty_dibutuhkan)
                        $this->catatLog($sediaan->id, $qty_dibutuhkan, $header->id,$diskon);
                        $qty_dibutuhkan = 0; // Kebutuhan terpenuhi
                    } else {
                        // Jika stok di batch ini tidak cukup, ambil semua yang ada
                        $ambil = $sediaan->stok_sekarang;
                        $qty_dibutuhkan -= $ambil; // Kurangi sisa kebutuhan
                        $sediaan->stok_sekarang = 0; // Habiskan stok batch ini
                        $sediaan->save();
                        // Catat log transaksi (ambil sebesar $ambil)
                        $this->catatLog($sediaan->id, $ambil, $header->id,$diskon);
                    }
                }
                if ($qty_dibutuhkan > 0) {
                    // Jika setelah semua batch dicek stok masih kurang
                    // return response()->json(['message' => 'Stok total tidak mencukupi!'], 400);
                    // $html = view('Kasir.view_kembalian', compact(['gt', 'uang']))->render();
                    throw new \Exception('Stok ' . $nama_barang . ' tidak mencukupi!');
                    $response = [
                        'code' => 500,
                        'html' => '',
                        'message' => 'Stok ' . $nama_barang . ' total tidak mencukupi!'
                    ];
                    echo json_encode($response);
                    die;
                }
            }
            model_ts_header::where('id', $header->id)->update(['status' => 0]);
            $html = view('Kasir.view_kembalian', compact(['gt', 'uang']))->render();
            DB::commit();
            $response = [
                'code' => 200,
                'html' => $html,
                'message' => 'sukses'
            ];
            echo json_encode($response);
            die;
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'code' => 500,
                'message' => $e->getMessage()
            ]);
        }
    }
    public function catatLog($idsediaan, $qty_dibutuhkan, $id,$diskon)
    {
        $get_sediaan = model_sediaan_barang::where('id', $idsediaan)->first();
        $log_terakhir = DB::table('log_transaksi_stok')
            ->where('kode_barang', $get_sediaan->kode_barang)
            ->orderBy('id', 'desc')
            ->first();
        $mt_barang = Medicine::where('kode_barang', $get_sediaan->kode_barang)->first();
        // Tentukan stok_last (jika log belum ada, ambil dari stok_now di mt_sediaan)
        $stok_sebelumnya = $log_terakhir ? $log_terakhir->stok_now : $get_sediaan->stok_now;
        $subtotalnya = $qty_dibutuhkan * $mt_barang->harga_jual;
        $ts_detail = [
            'id_header' => $id,
            'id_sediaan' => $idsediaan,
            'kode_barang' => $get_sediaan->kode_barang,
            'qty' => $qty_dibutuhkan,
            'harga_modal' => $get_sediaan->harga_modal_satuan_kecil,
            'harga_jual' => $mt_barang->harga_jual,
            'diskon' => $diskon,
            'subtotal' => $subtotalnya,
            'grandtotal' => $subtotalnya - $diskon
        ];
        $detail = model_ts_detail::create($ts_detail);
        $data_log = [
            'id_dokumen' => $detail->id,
            'kode_barang' => $get_sediaan->kode_barang,
            'stok_in' => '0',
            'stok_out' => $qty_dibutuhkan,
            'stok_last' => $stok_sebelumnya,
            'stok_now' => $stok_sebelumnya - $qty_dibutuhkan,
            'tgl_input' => $this->get_now(),
            'keterangan' => 'Pembelian',
            'id_sediaan' => $idsediaan,
            'harga_jual' => $mt_barang->harga_jual,
            'harga_modal' => $get_sediaan->harga_modal_satuan_kecil
        ];
        model_log_transaksi_stok::create($data_log);
    }
    public function get_inv()
    {
        $q = DB::connection('mysql')->select('SELECT id,RIGHT(no_invoice,3) AS kd_max  FROM ts_penjualan_header ORDER BY id DESC LIMIT 1');
        $kd = "";
        if (count($q) > 0) {
            foreach ($q as $k) {
                $tmp = ((int) $k->kd_max) + 1;
                $kd = sprintf("%03s", $tmp);
            }
        } else {
            $kd = "001";
        }
        $DATE = $this->get_date();
        date_default_timezone_set('Asia/Jakarta');
        return 'INV/' . $DATE . '/' . $kd;
    }
    public function simpansesikasir(Request $request)
    {
        $saldo = $request->saldo;
        $data = [
            'id_user' => auth()->user()->id,
            'saldo_awal' => $saldo,
            'tgl_sesi_kasir' => $this->get_now()
        ];
        model_ts_sesi_kasir::create($data);
        $response = [
            'code' => 200,
            'message' => 'sukses'
        ];
        echo json_encode($response);
        die;
    }
    public function tutupsesikasir(Request $request)
    {
        $id = $request->id;
        $ts_header = db::select('select sum(total_harga ) as total from ts_penjualan_header where id_sesi_kasir = ?', [$id]);
        $data = [
            'saldo_akhir' => $ts_header[0]->total,
            'status' => 2
        ];
        model_ts_sesi_kasir::where('id', $id)->update($data);
        $response = [
            'code' => 200,
            'message' => 'sukses'
        ];
        echo json_encode($response);
        die;
    }
}
