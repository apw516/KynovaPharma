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

class GudangController extends Controller
{
    public function Index()
    {
        $now = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $date_start = $now->format('Y-m-d');
        $date_end = $end->format('Y-m-d');
        $menu = 'datapurchaseorder';
        $satuan = db::select('select * from mt_satuan');
        return view('Gudang.index_PO', compact([
            'menu',
            'satuan',
            'date_start',
            'date_end'
        ]));
    }
    public function indexstokinject()
    {
        $now = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $date_start = $now->format('Y-m-d');
        $date_end = $end->format('Y-m-d');
        $menu = 'indexstokinject';
        $mt_sediaan = db::select('select * from mt_satuan');
        return view('Gudang.index_stok_inject', compact([
            'menu',
            'mt_sediaan',
            'date_start',
            'date_end'
        ]));
    }
    public function indexstokretur()
    {
        $now = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $date_start = $now->format('Y-m-d');
        $date_end = $end->format('Y-m-d');
        $menu = 'stokretur';
        $satuan = db::select('select * from mt_satuan');
        return view('Gudang.index_stok_retur', compact([
            'menu',
            'satuan',
            'date_start',
            'date_end'
        ]));
    }
    public function indexlogkartustok()
    {
        $now = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $date_start = $now->format('Y-m-d');
        $date_end = $end->format('Y-m-d');
        $menu = 'logkartustok';
        $satuan = db::select('select * from mt_satuan');
        return view('Gudang.index_log_stok', compact([
            'menu',
            'satuan',
            'date_start',
            'date_end'
        ]));
    }
    public function indexstoksediaan()
    {
        $now = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $date_start = $now->format('Y-m-d');
        $date_end = $end->format('Y-m-d');
        $menu = 'stokpersediaan';
        $satuan = db::select('select * from mt_satuan');
        return view('Gudang.index_stok_sediaan', compact([
            'menu',
            'satuan',
            'date_start',
            'date_end'
        ]));
    }
    public function indexdatastokpersediaan()
    {
        $now = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $date_start = $now->format('Y-m-d');
        $date_end = $end->format('Y-m-d');
        $menu = 'indexstokpersediaan';
        $satuan = db::select('select * from mt_satuan');
        return view('Gudang.index_data_persediaan', compact([
            'menu',
            'satuan',
            'date_start',
            'date_end'
        ]));
    }
    public function ambildatastokretur(Request $request)
    {
        try {
            $tgl_awal = $request->tglawal;
            $tgl_akhir = $request->tglakhir;
            $data = DB::table('ts_retur_sediaan as a')
                ->join('mt_barang as b', 'a.kode_barang', '=', 'b.kode_barang')
                ->join('mt_supplier as c', 'a.id_supplier', '=', 'c.kode_supplier')
                ->select('b.nama_dagang', 'b.nama_obat', 'b.produsen', 'a.*', 'b.satuan_besar', 'b.satuan_sedang', 'b.satuan_kecil', 'b.rasio_sedang', 'b.rasio_kecil', 'c.nama_supplier')
                ->whereBetween(DB::raw('DATE(a.tgl_retur)'), [$tgl_awal, $tgl_akhir])
                ->orderBy('a.id', 'DESC')
                ->get();
            // 3. Cek apakah data ada
            if ($data->isEmpty()) {
                return response()->json([
                    'kode' => 500,
                    'message' => 'Tidak ada data ditemukan pada periode tersebut.'
                ]);
            }
            // 4. Render partial view menjadi string HTML
            // Kita kirim data ke file blade khusus untuk baris tabel
            $view = view('Gudang.tabel_stok_retur', compact('data'))->render();
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
    public function ambilstokpersediaanbarang(Request $request)
    {
        try {
            $data = DB::table('mt_sediaan_obat as a')
                ->join('mt_barang as b', 'a.kode_barang', '=', 'b.kode_barang')
                ->join('mt_supplier as c', 'a.kode_supplier', '=', 'c.kode_supplier')
                ->join('ts_po_detail as d', 'a.id_po_detail', '=', 'd.id')
                ->join('ts_po_header as e', 'd.id_header', '=', 'e.id')
                ->select(
                    'e.nomor_faktur',
                    'e.tanggal_pembelian',
                    'e.tanggal_faktur',
                    'a.*',
                    'b.nama_dagang',
                    'b.nama_obat',
                    'b.harga_modal',
                    'c.nama_supplier',
                    'b.produsen',
                    'b.satuan_besar',
                    'b.satuan_sedang',
                    'b.satuan_kecil',
                    'b.rasio_sedang',
                    'b.rasio_kecil'
                )
                ->where('a.stok_sekarang', '>', 0)
                ->get(); // Di sini baru gunakan ->get()
            // 3. Cek apakah data ada
            if ($data->isEmpty()) {
                return response()->json([
                    'kode' => 500,
                    'message' => 'Tidak ada data ditemukan pada periode tersebut.'
                ]);
            }
            // 4. Render partial view menjadi string HTML
            // Kita kirim data ke file blade khusus untuk baris tabel
            $supplier = Supplier::get();
            $view = view('Gudang.tabel_stok_persediaan_barang', compact('data', 'supplier'))->render();
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
    public function ambildatalog(Request $request)
    {
        try {
            $tgl_awal = $request->tglawal;
            $tgl_akhir = $request->tglakhir;
            $data = DB::table('log_transaksi_stok as a')
                ->join('mt_barang as b', 'a.kode_barang', '=', 'b.kode_barang')
                ->select('b.nama_dagang', 'b.nama_obat', 'b.produsen', 'a.*', 'b.satuan_besar', 'b.satuan_sedang', 'b.satuan_kecil', 'b.rasio_sedang', 'b.rasio_kecil')
                ->whereBetween(DB::raw('DATE(a.tgl_input)'), [$tgl_awal, $tgl_akhir])
                ->orderBy('a.id', 'DESC')
                ->get();
            // 3. Cek apakah data ada
            if ($data->isEmpty()) {
                return response()->json([
                    'kode' => 500,
                    'message' => 'Tidak ada data ditemukan pada periode tersebut.'
                ]);
            }

            // 4. Render partial view menjadi string HTML
            // Kita kirim data ke file blade khusus untuk baris tabel
            $view = view('Gudang.tabel_log_kartu_stok', compact('data'))->render();

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
    public function ambildatapo(Request $request)
    {
        try {
            // 1. Ambil input tanggal
            $tgl_awal = $request->tglawal;
            $tgl_akhir = $request->tglakhir;

            // 2. Query data PO berdasarkan range tanggal
            // Menggunakan whereBetween untuk performa lebih baik
            $data_po = po_header::whereBetween('tanggal_pembelian', [$tgl_awal, $tgl_akhir])
                ->orderBy('id', 'desc')
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
            $view = view('Gudang.tabel_data_po', compact('data_po'))->render();

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
    public function ambildetailpo(Request $request)
    {
        try {
            $id = $request->id;
            // 2. Query data PO berdasarkan range tanggal
            // Menggunakan whereBetween untuk performa lebih baik
            $data_header = po_header::where('id', [$id])
                ->get();
            $data_po = po_detail::where('id_header', [$id])
                ->get();
            // dd($data_po);
            // 4. Render partial view menjadi string HTML
            // Kita kirim data ke file blade khusus untuk baris tabel
            $view = view('Gudang.tabel_detail_po', compact([
                'data_header',
                'data_po'
            ]))->render();
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
    public function returpo(Request $request)
    {
        DB::beginTransaction();
        try {
            $id = $request->id;
            $header = po_header::where('id', $id)->get();
            $detail = po_detail::where('id_header', $id)->get();
            foreach ($detail as $dd) {
                $id_po_detail = $dd['id'];
                $sediaan = model_sediaan_barang::where('id', $id_po_detail)->get();
                foreach ($sediaan as $ss) {
                    $penjualan_detail = model_ts_detail::where('id_sediaan', $ss['id'])->get();
                    if (count($penjualan_detail) > 0) {
                        $response = [
                            'code' => 500,
                            'message' => 'Retur Semua Gagagl ,Ada penjualan menggunakan barang dari PO ini !'
                        ];
                        echo json_encode($response);
                        die;
                    }
                }
            }
            if ($header[0]->status == 2) {
                $response = [
                    'code' => 500,
                    'message' => 'Data PO Sudah diretur !'
                ];
                echo json_encode($response);
                die;
            }
            po_header::where('id', $id)->update(['status' => 2]);
            foreach ($detail as $d) {
                $kode_barang = $d->kode_barang;
                $kode_batch = $d->no_batch;
                $harga_beli = $d->harga_beli;
                $kode_supplier = $header[0]->kode_supplier;
                $tgl_ed = $d->tgl_expired;
                $qty = $d->qty;
                $mt_barang = db::select('select rasio_sedang,rasio_kecil from mt_barang where kode_barang = ?', [$kode_barang]);
                $rasio_sedang = $mt_barang[0]->rasio_sedang;
                $rasio_kecil = $mt_barang[0]->rasio_kecil;
                $stok_retur = $qty * $rasio_sedang * $rasio_kecil;
                $cek_sediaan = db::select('select id,stok_sekarang   from mt_sediaan_obat where kode_barang = ? and kode_supplier = ? and tgl_expired = ? and kode_batch = ? and harga_modal_satuan_besar = ?', [$kode_barang, $kode_supplier, $tgl_ed, $kode_batch, $harga_beli]);
                $data_update_sediaan = [
                    'stok_sekarang' =>  $cek_sediaan[0]->stok_sekarang - $stok_retur
                ];
                model_sediaan_barang::where('id', $cek_sediaan[0]->id)->update($data_update_sediaan);

                $last_log = db::table('log_transaksi_stok')
                    ->where('kode_barang', $kode_barang)
                    ->orderBy('id', 'desc')
                    ->first();
                $stok_awal_log = $last_log ? $last_log->stok_now : 0;
                // 2. Siapkan data mutasi stok
                $data_log = [
                    'id_dokumen'  => $id, // Menggunakan nomor PO sebagai referensi
                    'kode_barang'   => $kode_barang,
                    'stok_in'         => 0, // Sudah dalam satuan terkecil
                    'stok_out'        => $stok_retur,
                    'stok_last'     => $stok_awal_log,
                    'stok_now'   => $stok_awal_log - $stok_retur,
                    'tgl_input'       => $this->get_now(),
                    'keterangan'    => 'Retur barang dari PO',
                    'id_sediaan'       => $cek_sediaan[0]->id // Mencatat siapa yang melakukan input
                ];
                model_log_transaksi_stok::create($data_log);
            }
            DB::commit();
            $response = [
                'code' => 200,
                'message' => 'sukses'
            ];
            echo json_encode($response);
            die;
        } catch (\Exception $e) {
            // JIKA TERJADI ERROR, BATALKAN SEMUA PROSES
            DB::rollback();
            return response()->json([
                'code' => 500,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }
    public function bayarpo(Request $request)
    {
        DB::beginTransaction();
        try {
            $id = $request->id;
            po_header::where('id', $id)->update(['status_bayar' => 1]);
            DB::commit();
            $response = [
                'code' => 200,
                'message' => 'sukses'
            ];
            echo json_encode($response);
            die;
        } catch (\Exception $e) {
            // JIKA TERJADI ERROR, BATALKAN SEMUA PROSES
            DB::rollback();
            return response()->json([
                'code' => 500,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }
    public function cariSupplier(Request $request)
    {
        $term = $request->get('term'); // Parameter otomatis dari jQuery UI
        $data = DB::table('mt_supplier')
            ->where('nama_supplier', 'LIKE', '%' . $term . '%')
            ->get();

        $results = [];
        foreach ($data as $row) {
            // Label: teks yang muncul di daftar, Value: teks yang masuk ke input setelah diklik
            $results[] = [
                'id' => $row->kode_supplier,
                'label' => $row->nama_supplier,
                'value' => $row->nama_supplier,
                'telp' => $row->telepon,
                'alamat' => $row->alamat,
                'email' => $row->email,
            ];
        }

        return response()->json($results);
    }
    public function cariobat(Request $request)
    {
        $data = DB::table('mt_barang')
            ->where('nama_dagang', 'LIKE', '%' . $request->nama . '%')
            ->orWhere('nama_obat', 'LIKE', '%' . $request->nama . '%')
            ->get();

        $mt_sediaan = db::select('select * from mt_satuan');
        $html = view('Gudang.tabel_pencarian_obat', compact(['data', 'mt_sediaan']))->render();
        $response = [
            'code' => 200,
            'html' => $html,
            'message' => 'sukses'
        ];
        echo json_encode($response);
        die;
    }
    public function prosespo(Request $request)
    {
        $data = json_decode($_POST['data'], true);
        $data2 = json_decode($_POST['data2'], true);
        foreach ($data2 as $nama) {
            $index =  $nama['name'];
            $value =  $nama['value'];
            $dataheader[$index] = $value;
        }
        foreach ($data as $nama2) {
            $index2 = $nama2['name'];
            $value2 = $nama2['value'];
            $dataSet2[$index2] = $value2;
            if ($index2 == 'expireddate') {
                $arrayobat[] = $dataSet2;
            }
        }
        $grandtotal = 0;
        foreach ($arrayobat as $d) {
            // 1. Hitung Gross (Harga x Qty)
            $subtotal = $d['hargabeliasli'] * $d['qty'];

            // 2. Tentukan Potongan
            $potongan = 0;
            if ($d['diskonrupiahasli'] != 0) {
                // Jika diskon rupiah adalah TOTAL per baris
                $potongan = $d['diskonrupiahasli'];
            } elseif ($d['diskonpersen'] != 0) {
                // Diskon persen dikalikan ke TOTAL subtotal (agar qty ikut terhitung)
                $potongan = ($subtotal * $d['diskonpersen']) / 100;
            }

            // 3. Subtotal Netto per baris
            $subtotal_netto = $subtotal - $potongan;

            // 4. Akumulasi
            $grandtotal += $subtotal_netto;
        }
        $vgt = number_format($grandtotal, 0, ',', '.');
        if ($dataheader['diskonlobalpersen'] > 0) {
            $diskonglobal = $grandtotal * $dataheader['diskonlobalpersen'] / 100;
            $grandtotal_baru = $grandtotal - $diskonglobal;
        } else {
            if ($dataheader['diskonglobalrupiah'] > 0) {
                $grandtotal_baru = $grandtotal - $dataheader['diskonglobalrupiah'];
            } else {
                $grandtotal_baru = $grandtotal - 0;
            }
        }
        if ($dataheader['pajakglobalpersen'] > 0) {
            $pajak = $grandtotal_baru * $dataheader['pajakglobalpersen'] / 100;
        } else {
            $pajak = 0;
        }
        $total_biaya = $grandtotal_baru + $pajak;
        $total_biaya_disp = number_format($grandtotal_baru + $pajak, 0, 0);
        $response = [
            'code' => 200,
            'grandtotal' => $grandtotal,
            'view' => $vgt,
            'total_biaya' => $total_biaya,
            'total_biaya_disp' => $total_biaya_disp,
            'message' => 'sukses'
        ];
        echo json_encode($response);
        die;
    }
    public function savepo(Request $request)
    {
        $data = json_decode($_POST['data'], true);
        $data2 = json_decode($_POST['data2'], true);
        $data3 = json_decode($_POST['data3'], true);
        foreach ($data3 as $nama) {
            $index =  $nama['name'];
            $value =  $nama['value'];
            $datasupplier[$index] = $value;
        }
        foreach ($data2 as $nama) {
            $index =  $nama['name'];
            $value =  $nama['value'];
            $dataheader[$index] = $value;
        }
        foreach ($data as $nama2) {
            $index2 = $nama2['name'];
            $value2 = $nama2['value'];
            $dataSet2[$index2] = $value2;
            if ($index2 == 'expireddate') {
                $arrayobat[] = $dataSet2;
            }
        }
        DB::beginTransaction();
        try {
            $data_header = [
                'nomor_faktur' => $datasupplier['nomorfaktur'],
                'tanggal_faktur' => $datasupplier['tanggalfaktur'],
                'tanggal_pembelian' => $datasupplier['tanggalpembelian'],
                'jenis_pembayaran' => $datasupplier['jenispembayaran'],
                'tanggal_pembayaran' => $datasupplier['tanggalpembayaran'],
                'nama_supplier' => $datasupplier['namasupplier'],
                'kode_supplier' => $datasupplier['kodesupplier'],
                'nomor_telp' => $datasupplier['telepon'],
                'diskon_rupiah' => $dataheader['diskonglobalrupiah'],
                'diskon_persen' => $dataheader['diskonlobalpersen'],
                'pajak_persen' => $dataheader['pajakglobalpersen'],
                'pajak_rupiah' => $dataheader['pajakglobalrupiah'],
                'sub_total' => $dataheader['totalprosesasli'],
                'grand_total' => $dataheader['totalbiayaasli'],
                'pic' => auth()->user()->id,
                'tgl_entry' => $this->get_now(),
            ];
            $hh = po_header::create($data_header);
            foreach ($arrayobat as $arr) {
                $pajak = $dataheader['pajakglobalpersen'];
                $pajak_rupiah = $arr['hargabeliasli'] * $pajak / 100;
                $harganya = $arr['hargabeliasli'] + $pajak_rupiah;
                $data_detail = [
                    'id_header' => $hh->id,
                    'kode_barang' => $arr['kode_barang'],
                    'nama_barang' => $arr['nama_barang'],
                    'qty' => $arr['qty'],
                    //jumlahpersatuanbesar
                    'satuan' => $arr['satuan_besar'],
                    'harga_beli' => $harganya,
                    //harga satu box
                    'diskon_persen' => $arr['diskonpersen'],
                    'diskon_rupiah' => $arr['diskonrupiahasli'],
                    'no_batch' => $arr['kodebatch'],
                    'tgl_expired' => $arr['expireddate']
                ];
                $po_detail = po_detail::create($data_detail);
                //save ke tabel sediaan
                // $cek_sediaan = db::select('select id,stok_sekarang   from mt_sediaan_obat where kode_barang = ? and kode_supplier = ? and tgl_expired = ? and kode_batch = ? and harga_modal_satuan_besar = ?', [$arr['kode_barang'], $datasupplier['kodesupplier'], $arr['expireddate'], $arr['kodebatch'], $harganya]);

                $mt_barang = db::select('select rasio_sedang,rasio_kecil from mt_barang where kode_barang = ?', [$arr['kode_barang']]);
                Medicine::where('kode_barang', $arr['kode_barang'])->update([
                    'rasio_sedang' => $arr['rasio_sedang'],
                    'rasio_kecil' => $arr['rasio_kecil'],
                    'satuan_besar' => $arr['satuan_besar'],
                    'satuan_sedang' => $arr['satuan_sedang'],
                    'satuan_kecil' => $arr['satuan_kecil'],
                    'sediaan' => $arr['satuan_kecil']
                ]);
                $rasio_sedang = $arr['rasio_sedang'];
                $rasio_kecil = $arr['rasio_kecil'];
                $harga_sedang = $harganya / $rasio_sedang;
                $harga_kecil = $harga_sedang / $rasio_kecil;
                //konversi_kesatuan_kecil
                $stok_masuk = $arr['qty'] * $rasio_sedang * $rasio_kecil;
                // if (count($cek_sediaan) == 0) {
                $datasediaan = [
                    'kode_barang' => $arr['kode_barang'],
                    'kode_supplier' => $datasupplier['kodesupplier'],
                    'tgl_expired' => $arr['expireddate'],
                    'harga_modal_satuan_besar' => $harganya,
                    //harga_satuan_besar
                    'harga_modal_satuan_sedang' => $harga_sedang,
                    //harga_satuan_sedang
                    'harga_modal_satuan_kecil' => $harga_kecil,
                    //harga_satuan_kecil
                    'kode_batch' => $arr['kodebatch'],
                    'stok_awal' => 0,
                    'stok_sekarang' => $stok_masuk,
                    //stok satuan kecil
                    'tgl_input' => $this->get_now(),
                    'id_po_detail' => $po_detail->id
                ];
                $datass = model_sediaan_barang::create($datasediaan);
                $id_sediaan = $datass->id;
                $last_log = db::table('log_transaksi_stok')
                    ->where('kode_barang', $arr['kode_barang'])
                    ->orderBy('id', 'desc')
                    ->first();
                $stok_awal_log = $last_log ? $last_log->stok_now : 0;
                // 2. Siapkan data mutasi stok
                $data_log = [
                    'id_dokumen'  => $hh->id, // Menggunakan nomor PO sebagai referensi
                    'kode_barang'   => $arr['kode_barang'],
                    'stok_in'         => $stok_masuk, // Sudah dalam satuan terkecil
                    'stok_out'        => 0,
                    'stok_last'     => $stok_awal_log,
                    'stok_now'   => $stok_awal_log + $stok_masuk,
                    'tgl_input'       => $this->get_now(),
                    'keterangan'    => 'Penerimaan barang dari PO',
                    'id_sediaan'       => $id_sediaan // Mencatat siapa yang melakukan input
                ];
                model_log_transaksi_stok::create($data_log);
            }
            DB::commit();
            $response = [
                'code' => 200,
                'message' => 'sukses'
            ];
            echo json_encode($response);
            die;
        } catch (\Exception $e) {
            // JIKA TERJADI ERROR, BATALKAN SEMUA PROSES
            DB::rollback();
            return response()->json([
                'code' => 500,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }
    public function simpanretursediaan(Request $request)
    {
        DB::beginTransaction();
        try {
            $jumlahstoksekarang = $request->jumlahstoksekarang;
            $idsediaan = $request->idsediaan;
            $jumlahstokretur = $request->jumlahstokretur;
            $alasanretur = $request->alasanretur;
            if (strlen($jumlahstokretur) == 0) {
                $response = [
                    'code' => 500,
                    'message' => 'Jumlah stok retur belum diisi ...'
                ];
                echo json_encode($response);
                die;
            }
            $sediaan = model_sediaan_barang::where('id', $idsediaan)->first();
            $id_po_detail = $sediaan->id_po_detail;
            $po_detail = po_detail::where('id', $id_po_detail)->first();
            $po_header = po_header::where('id', $po_detail->id_header)->first();
            $kode_barang = $sediaan->kode_barang;
            $kode_retur = $this->get_kode_retur();
            $data_retur = [
                'kode_retur' => $kode_retur,
                'nomor_faktur' => $po_header->nomor_faktur,
                'no_batch' => $sediaan->kode_batch,
                'kode_barang' => $kode_barang,
                'qty_retur' => $jumlahstokretur,
                'id_sediaan' =>  $idsediaan,
                'id_supplier' => $sediaan->kode_supplier,
                'harga_modal_satuan_besar' => $sediaan->harga_modal_satuan_besar,
                'harga_modal_satuan_sedang' => $sediaan->harga_modal_satuan_sedang,
                'harga_modal_satuan_kecil' => $sediaan->harga_modal_satuan_kecil,
                'pic' => auth()->user()->id,
                'tgl_retur' => $this->get_now(),
                'alasan_retur' => $alasanretur,
                'ed' =>  $sediaan->tgl_expired,
            ];
            model_ts_retur_sediaan::create($data_retur);
            $stok_sebelumnya = $sediaan->stok_sekarang;
            $stok_sekarang = $stok_sebelumnya - $jumlahstokretur;
            model_sediaan_barang::where('id', $idsediaan)->update(['stok_sekarang' => $stok_sekarang]);
            $last_log = db::table('log_transaksi_stok')
                ->where('kode_barang', $kode_barang)
                ->orderBy('id', 'desc')
                ->first();
            $stok_awal_log = $last_log ? $last_log->stok_now : 0;
            // 2. Siapkan data mutasi stok
            $data_log = [
                'id_dokumen'  =>  $kode_retur, // Menggunakan nomor PO sebagai referensi
                'kode_barang'   => $kode_barang,
                'stok_in'         => 0, // Sudah dalam satuan terkecil
                'stok_out'        => $jumlahstokretur,
                'stok_last'     => $stok_awal_log,
                'stok_now'   => $stok_awal_log - $jumlahstokretur,
                'tgl_input'       => $this->get_now(),
                'keterangan'    => 'RETUR ( ' . $alasanretur . ' )',
                'id_sediaan'       => $idsediaan // Mencatat siapa yang melakukan input
            ];
            model_log_transaksi_stok::create($data_log);
            DB::commit();
            $response = [
                'code' => 200,
                'message' => 'sukses'
            ];
            echo json_encode($response);
            die;
        } catch (\Exception $e) {
            // JIKA TERJADI ERROR, BATALKAN SEMUA PROSES
            DB::rollback();
            return response()->json([
                'code' => 500,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }
    public function get_now()
    {
        $dt = Carbon::now()->timezone('Asia/Jakarta');
        $date = $dt->toDateString();
        $time = $dt->toTimeString();
        $now = $date . ' ' . $time;
        return $now;
    }
    public function get_date()
    {
        $dt = Carbon::now()->timezone('Asia/Jakarta');
        $date = $dt->toDateString();
        $time = $dt->toTimeString();
        $now = $date;
        return $now;
    }
    public function get_kode_retur()
    {
        $q = DB::connection('mysql')->select('SELECT id,RIGHT(kode_retur,3) AS kd_max  FROM ts_retur_sediaan ORDER BY id DESC LIMIT 1');
        $kd = "";
        if (count($q) > 0) {
            foreach ($q as $k) {
                $tmp = ((int) $k->kd_max) + 1;
                $kd = sprintf("%03s", $tmp);
            }
        } else {
            $kd = "001";
        }
        date_default_timezone_set('Asia/Jakarta');

        return 'RET' . $kd;
    }
    public function getdatabarang_opname()
    {
        // Query langsung ke tabel mt_barang tanpa join/relasi
        $data = Medicine::select([
            'id',
            'nama_obat',
            'kode_barang',
            'nama_dagang', // Sesuaikan dengan nama kolom asli di tabel mt_barang
            'produsen',
            'satuan_besar',
            'satuan_sedang',
            'rasio_sedang',
            'rasio_kecil',
            'satuan_kecil',
            'harga_jual',
            'aturan_pakai',
            'sediaan'
        ]);
        return DataTables::of($data)
            ->addIndexColumn()
            // Gunakan filterColumn jika Anda ingin kustomisasi pencarian
            ->filterColumn('nama_merk', function ($query, $keyword) {
                $query->where('merk_dagang', 'like', "%{$keyword}%");
            })
            ->filterColumn('produsen', function ($query, $keyword) {
                $query->where('produsen', 'like', "%{$keyword}%");
            })
            ->addColumn('action', function ($row) {
                return '<button class="btn btn-sm btn-success pilihbarang" 
            idbarang="' . $row->id . '" 
            data-kode="' . $row->kode_barang . '"
            data-nama="' . ($row->nama_dagang ?? $row->nama_obat) . '"
            data-sediaan="' . $row->sediaan . '"
            data-rsedang="' . $row->rasio_sedang . '"
            data-rkecil="' . $row->rasio_kecil . '"
            data-harga="' . $row->harga_jual . '"
            data-sbesar="' . $row->satuan_besar . '"
            data-ssedang="' . $row->satuan_sedang . '"
            data-skecil="' . $row->satuan_kecil . '"
            data-bs-toggle="modal" data-bs-target="#modaleditbarang">
            <i class="bi bi-arrow-down-circle"></i></button>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }
    public function simpaneditsediaan(Request $request)
    {
        $data = json_decode($_POST['data'], true);
        foreach ($data as $nama2) {
            $index2 = $nama2['name'];
            $value2 = $nama2['value'];
            $dataSet2[$index2] = $value2;
        }
        DB::beginTransaction();
        try {
            $mt_barang = medicine::where('kode_barang',$dataSet2['kode_barang'])->get()->first();
            $rasio_sedang = $mt_barang->rasio_sedang;
            $rasio_kecil = $mt_barang->rasio_kecil;
            $harga_sedang = $dataSet2['modalasli'] / $rasio_sedang;
            $harga_kecil = $harga_sedang / $rasio_kecil;
            $data_sediaan = [
                'kode_batch' => $dataSet2['nomorbatch'],
                'tgl_expired' => $dataSet2['tanggaled'],
                'kode_supplier' => $dataSet2['kode_supplier'],
                'harga_modal_satuan_besar' => $dataSet2['modalasli'],
                'harga_modal_satuan_sedang' => $harga_sedang,
                'harga_modal_satuan_kecil' => $harga_kecil,
            ];
            model_sediaan_barang::where('id', $dataSet2['idsediaan'])->update($data_sediaan);
            if ($dataSet2['koreksistok'] == 1) {
                $stok_out = $dataSet2['stoksekarang'];
                $stok_in = $dataSet2['stokkoreksi'];
                $kode_barang = $dataSet2['kode_barang'];
                $mt_barang = Medicine::where('kode_barang', $kode_barang)->first();

                $get_sediaan = model_sediaan_barang::where('id', $dataSet2['idsediaan'])->first();
                $log_terakhir = DB::table('log_transaksi_stok')
                    ->where('kode_barang', $kode_barang)
                    ->orderBy('id', 'desc')
                    ->first();
                $stok_sebelumnya = $log_terakhir ? $log_terakhir->stok_now : $get_sediaan->stok_now;
                $data_log = [
                    'id_dokumen' => '',
                    'kode_barang' => $kode_barang,
                    'stok_in' => '0',
                    'stok_out' => $stok_out,
                    'stok_last' => $stok_sebelumnya,
                    'stok_now' => $stok_sebelumnya - $stok_out,
                    'tgl_input' => $this->get_now(),
                    'keterangan' => 'Koreksi Stok',
                    'id_sediaan' => $dataSet2['idsediaan'],
                    'harga_jual' => $mt_barang->harga_jual,
                    'harga_modal' => $get_sediaan->harga_modal_satuan_kecil
                ];
                model_log_transaksi_stok::create($data_log);
                $log_terakhir_2 = DB::table('log_transaksi_stok')
                    ->where('kode_barang', $kode_barang)
                    ->orderBy('id', 'desc')
                    ->first();
                $stok_sebelumnya_2 = $log_terakhir_2 ? $log_terakhir_2->stok_now : $get_sediaan->stok_now;
                $data_log_2 = [
                    'id_dokumen' => '',
                    'kode_barang' => $kode_barang,
                    'stok_in' => $stok_in,
                    'stok_out' => 0,
                    'stok_last' => $stok_sebelumnya_2,
                    'stok_now' => $stok_sebelumnya_2 + $stok_in,
                    'tgl_input' => $this->get_now(),
                    'keterangan' => 'Koreksi Stok',
                    'id_sediaan' => $dataSet2['idsediaan'],
                    'harga_jual' => $mt_barang->harga_jual,
                    'harga_modal' => $get_sediaan->harga_modal_satuan_kecil
                ];
                model_log_transaksi_stok::create($data_log_2);
                model_sediaan_barang::where('id', $dataSet2['idsediaan'])->update(['stok_sekarang' => $stok_in, 'stok_awal' => $stok_sebelumnya]);
            }
            DB::commit();
            $response = [
                'code' => 200,
                'message' => 'sukses'
            ];
            echo json_encode($response);
            die;
        } catch (\Exception $e) {
            // JIKA TERJADI ERROR, BATALKAN SEMUA PROSES
            DB::rollback();
            return response()->json([
                'code' => 500,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }
    public function simpandatainject(Request $requesrt)
    {
        $data = json_decode($_POST['data'], true);
        foreach ($data as $nama2) {
            $index2 = $nama2['name'];
            $value2 = $nama2['value'];
            $dataSet2[$index2] = $value2;
            if ($index2 == 'stokk') {
                $arrayobat[] = $dataSet2;
            }
        }
        foreach ($arrayobat as $a) {
            if ($a['satuanbesar'] == '') {
                $response = [
                    'kode' => 500,
                    'message' => 'Pilih satuan besar !'
                ];
                echo json_encode($response);
                die;
            }
            if ($a['satuansedang'] == '') {
                $response = [
                    'kode' => 500,
                    'message' => 'Pilih satuan sedang !'
                ];
                echo json_encode($response);
                die;
            }
            if ($a['satuankecil'] == '') {
                $response = [
                    'kode' => 500,
                    'message' => 'Pilih satuan kecil !'
                ];
                echo json_encode($response);
                die;
            }
            if ($a['rasiosedang'] == '' || $a['rasiosedang'] == 0) {
                $response = [
                    'kode' => 500,
                    'message' => 'Rasio sedang belum diisi !'
                ];
                echo json_encode($response);
                die;
            }
            if ($a['rasiokecil'] == '' || $a['rasiokecil'] == 0) {
                $response = [
                    'kode' => 500,
                    'message' => 'Rasio kecil belum diisi !'
                ];
                echo json_encode($response);
                die;
            }
        }
        DB::beginTransaction();
        try {
            $data_header = [
                'nomor_faktur' => 'STOK OPNAME',
                'tanggal_faktur' => $this->get_date(),
                'tanggal_pembelian' => $this->get_date(),
                'jenis_pembayaran' => 2,
                'tanggal_pembayaran' => $this->get_date(),
                'nama_supplier' => 'STOK OPNAME',
                'kode_supplier' => 'SUP000',
                'nomor_telp' => '-',
                'diskon_rupiah' => 0,
                'diskon_persen' => 0,
                'pajak_persen' => 0,
                'pajak_rupiah' => 0,
                'sub_total' => 0,
                'grand_total' => 0,
                'status_bayar' => 1,
                'pic' => auth()->user()->id,
                'tgl_entry' => $this->get_now(),
            ];
            $hh = po_header::create($data_header);
            foreach ($arrayobat as $arr) {
                $harga = $arr['hargajualasli'];
                $pajak = 0;
                // $harga_modal = $harga - 2000;
                $pajak_rupiah = $arr['hargamodalasli'] * $pajak / 100;
                // $pajak_rupiah = $harga_modal * $pajak / 100;
                // $harganya = $harga_modal + $pajak_rupiah;
                $harganya = $arr['hargamodalasli'] + $pajak_rupiah;
                $mt_barang = Medicine::where('id', $arr['idbarang'])->get()->first();
                $rasio_sedang = $arr['rasiosedang'];
                $rasio_kecil = $arr['rasiokecil'];
                // $rasio_sedang = 1;
                // $rasio_kecil = 1;
                $satuan_besar = $arr['satuanbesar'];
                $satuan_sedang = $arr['satuansedang'];
                $satuan_kecil = $arr['satuankecil'];
                $stok_besar = $arr['stokb'];
                $stok_sedang = $arr['stoks'];
                $stok_kecil = $arr['stokk'];
                //konversi ke satuan besar 
                $stok_besar_masuk = 0;
                $stok_sedang_masuk = 0;
                $stok_kecil_masuk = 0;
                if ($stok_besar > 0) {
                    $stok_besar_masuk = $stok_besar;
                }
                if ($stok_sedang > 0) {
                    $stok_sedang_masuk = $stok_sedang / $rasio_sedang;
                }
                if ($stok_kecil > 0) {
                    $rr = $rasio_sedang * $rasio_kecil;
                    $stok_kecil_masuk = $stok_kecil / $rr;
                }
                $stok_masuk = $stok_besar_masuk + $stok_sedang_masuk + $stok_kecil_masuk;
                $data_detail = [
                    'id_header' => $hh->id,
                    'kode_barang' => $mt_barang['kode_barang'],
                    'nama_barang' => $mt_barang['nama_dagang'],
                    'qty' => $stok_masuk,
                    //jumlahpersatuanbesar
                    // 'satuan' => 'STR',
                    'satuan' => $arr['satuanbesar'],
                    'harga_beli' => $harganya,
                    //harga satu box
                    'diskon_persen' => 0,
                    'diskon_rupiah' => 0,
                    // 'no_batch' => $mt_barang['kode_barang'],
                    'no_batch' => $arr['nobatch'],
                    'tgl_expired' => $arr['ed']
                ];
                $po_detail = po_detail::create($data_detail);
                //save ke tabel sediaan
                // $cek_sediaan = db::select('select id,stok_sekarang   from mt_sediaan_obat where kode_barang = ? and kode_supplier = ? and tgl_expired = ? and kode_batch = ? and harga_modal_satuan_besar = ?', [$arr['kode_barang'], $datasupplier['kodesupplier'], $arr['expireddate'], $arr['kodebatch'], $harganya]);

                // $mt_barang = db::select('select rasio_sedang,rasio_kecil from mt_barang where id = ?', [$arr['idbarang']]);
                if ($harga > 0) {
                    $harga_jual = $harga / $rasio_kecil;
                    Medicine::where('kode_barang', $mt_barang['kode_barang'])->update([
                        // 'rasio_sedang' => 1,
                        // 'rasio_kecil' => 1,
                        // 'satuan_besar' => 'STR',
                        // 'satuan_sedang' => 'STR',
                        // 'satuan_kecil' => 'STR',
                        'rasio_sedang' => $arr['rasiosedang'],
                        'rasio_kecil' => $arr['rasiokecil'],
                        'satuan_besar' => $arr['satuanbesar'],
                        'satuan_sedang' => $arr['satuansedang'],
                        'satuan_kecil' => $arr['satuankecil'],
                        'sediaan' => 'STR',
                        'harga_jual' => $harga_jual
                    ]);
                } else {
                    Medicine::where('kode_barang', $mt_barang['kode_barang'])->update([
                        // 'rasio_sedang' => 1,
                        // 'rasio_kecil' => 1,
                        // 'satuan_besar' => 'STR',
                        // 'satuan_sedang' => 'STR',
                        // 'satuan_kecil' => 'STR',
                        // 'sediaan' => 'STR',
                        'rasio_sedang' => $arr['rasiosedang'],
                        'rasio_kecil' => $arr['rasiokecil'],
                        'satuan_besar' => $arr['satuanbesar'],
                        'satuan_sedang' => $arr['satuansedang'],
                        'satuan_kecil' => $arr['satuankecil'],
                        'sediaan' => $arr['satuankecil']
                    ]);
                }
                $harga_sedang = $harganya / $rasio_sedang;
                $harga_kecil = $harga_sedang / $rasio_kecil;
                //konversi_kesatuan_kecil
                // $stok_masuk = $arr['qty'] * $rasio_sedang * $rasio_kecil;
                // if (count($cek_sediaan) == 0) {
                $stok_besar_masuk_2 = 0;
                $stok_sedang_masuk_2 = 0;
                $stok_kecil_masuk_2 = 0;
                if ($stok_besar > 0) {
                    $stok_besar_masuk_2 = $stok_besar * $rasio_sedang * $rasio_kecil;
                }
                if ($stok_sedang > 0) {
                    $stok_sedang_masuk_2 = $stok_sedang * $rasio_kecil;
                }
                if ($stok_kecil > 0) {
                    $stok_kecil_masuk_2 = $stok_kecil;
                }
                $stok_in = $stok_besar_masuk_2 + $stok_sedang_masuk_2 + $stok_kecil_masuk_2;
                $datasediaan = [
                    'kode_barang' => $mt_barang['kode_barang'],
                    'kode_supplier' => 'SUP003',
                    'tgl_expired' => $arr['ed'],
                    'harga_modal_satuan_besar' => $harganya,
                    //harga_satuan_besar
                    'harga_modal_satuan_sedang' => $harga_sedang,
                    //harga_satuan_sedang
                    'harga_modal_satuan_kecil' => $harga_kecil,
                    //harga_satuan_kecil
                    'kode_batch' => $arr['nobatch'],
                    // 'kode_batch' => $mt_barang['kode_barang'],
                    'stok_awal' => 0,
                    'stok_sekarang' => $stok_in,
                    //stok satuan kecil
                    'tgl_input' => $this->get_now(),
                    'id_po_detail' => $po_detail->id
                ];
                $datass = model_sediaan_barang::create($datasediaan);
                $id_sediaan = $datass->id;
                $last_log = db::table('log_transaksi_stok')
                    ->where('kode_barang', $mt_barang['kode_barang'])
                    ->orderBy('id', 'desc')
                    ->first();
                $stok_awal_log = $last_log ? $last_log->stok_now : 0;
                // 2. Siapkan data mutasi stok
                $data_log = [
                    'id_dokumen'  => $hh->id, // Menggunakan nomor PO sebagai referensi
                    'kode_barang'   => $mt_barang['kode_barang'],
                    'stok_in'         => $stok_in, // Sudah dalam satuan terkecil
                    'stok_out'        => 0,
                    'stok_last'     => $stok_awal_log,
                    'stok_now'   => $stok_awal_log + $stok_in,
                    'tgl_input'       => $this->get_now(),
                    'keterangan'    => 'Masuk dari stok opname',
                    'id_sediaan'       => $id_sediaan // Mencatat siapa yang melakukan input
                ];
                model_log_transaksi_stok::create($data_log);
            }
            DB::commit();
            $response = [
                'code' => 200,
                'message' => 'sukses'
            ];
            echo json_encode($response);
            die;
        } catch (\Exception $e) {
            // JIKA TERJADI ERROR, BATALKAN SEMUA PROSES
            DB::rollback();
            return response()->json([
                'code' => 500,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }
}
