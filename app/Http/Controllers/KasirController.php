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
        $get_sesi = db::select('select * from ts_sesi_kasir where date(tgl_sesi_kasir) = ? and status = ? and id_user = ?', [$date, 1, auth()->user()->id]);

        $notif_ed = DB::table('mt_sediaan_obat')
            ->whereBetween(DB::raw('DATE(tgl_expired)'), [now(), now()->addDays(90)])
            ->count();

        // Ambil semua PO yang belum Lunas (Tanpa batas tanggal awal)
        // $hutang_data = DB::table('ts_po_header')
        //     ->where('status_bayar', '!=', '1')
        //     ->where('status', 1) // 1 = PO Aktif/Disetujui
        //     ->get();

        // // Hitung total notif untuk Alert
        // $notif_hutang = $hutang_data->count();

        // // Pisahkan mana yang sudah lewat (Overdue) dan mana yang mendekati (Jatuh Tempo)
        // $overdue_count = $hutang_data->where('tanggal_pembayaran', '<', now()->format('Y-m-d'))->count();
        // $upcoming_count = $hutang_data->where('tanggal_pembayaran', '>=', now()->format('Y-m-d'))->count();

        // $total_notif = $notif_ed + $notif_hutang;

        // Tentukan batas tanggal: Hari ini + 5 hari
        $batasTanggal = now()->addDays(35)->format('Y-m-d');
        $hariIni = now()->format('Y-m-d');

        // Query hanya mengambil data yang tanggal pembayarannya <= (Hari ini + 5 hari)
        $hutang_data = DB::table('ts_po_header')
            ->where('status_bayar', '!=', '1')
            ->where('nama_supplier','!=','STOK OPNAME')
            ->where('status', 1)
            ->whereDate('tanggal_pembayaran', '<=', $batasTanggal) // Mengunci batas maksimal +5 hari
            ->get();

        // Hitung total notif untuk Alert (Otomatis hanya menghitung yang <= 5 hari atau overdue)
        $notif_hutang = $hutang_data->count();

        // Pisahkan mana yang sudah lewat (Overdue) dan mana yang mendekati (Jatuh Tempo 0 s.d 5 hari lagi)
        $overdue_count  = $hutang_data->where('tanggal_pembayaran', '<', $hariIni)->count();
        $upcoming_count = $hutang_data->where('tanggal_pembayaran', '>=', $hariIni)->count();

        // Total akumulasi alert internal sistem
        $total_notif = $notif_ed + $notif_hutang;
        $satuan = db::select('select * from mt_satuan');

        return view('Kasir.index', compact([
            'menu',
            'date_start',
            'date_end',
            'get_sesi',
            'notif_ed',
            'notif_hutang',
            'total_notif',
            'overdue_count',
            'upcoming_count',
            'satuan',
            'hutang_data'
        ]));
    }
    public function createkodebarang()
    {
        $q = DB::connection('mysql')->select('SELECT id,RIGHT(kode_barang,6) AS kd_max  FROM mt_barang ORDER BY id DESC LIMIT 1');
        $kd = "";
        if (count($q) > 0) {
            foreach ($q as $k) {
                $tmp = ((int) $k->kd_max) + 1;
                $kd = sprintf("%06s", $tmp);
            }
        } else {
            $kd = "000001";
        }
        date_default_timezone_set('Asia/Jakarta');

        return 'B' . $kd;
    }
    public function simpanstokauto(Request $Request)
    {
        DB::beginTransaction();
        try {
            $data = json_decode($_POST['data'], true);
            foreach ($data as $nama2) {
                $index2 = $nama2['name'];
                $value2 = $nama2['value'];
                $dataSet[$index2] = $value2;
            }
            if ($dataSet['kode_barang'] == 0) {
                $dataSet['tgl_entry'] = $this->get_now();
                $databarang = [
                    'kode_barang' => $this->createkodebarang(),
                    'produsen' => strtoupper($dataSet['produsen']),
                    'nama_dagang' => strtoupper($dataSet['merkdagang']),
                    'nama_obat' => strtoupper($dataSet['kategori']),
                    'satuan_besar' => $dataSet['satuan_besar'],
                    'satuan_sedang' => $dataSet['satuan_sedang'],
                    'satuan_kecil' => $dataSet['satuan_kecil'],
                    'sediaan' => $dataSet['sediaan'],
                    'rasio_sedang' => $dataSet['rasio_sedang'],
                    'rasio_kecil' => $dataSet['rasio_kecil'],
                    'aturan_pakai' => $dataSet['aturanpakai'],
                    'pic' => auth()->user()->id,
                    'tgl_entry' => $this->get_now(),
                    'tgl_update' => $this->get_now(),
                ];
                $baranngbaru =  medicine::create($databarang);
                $mt_barang = Medicine::where('id', $baranngbaru->id)->get()->first();
            } else {
                $mt_barang = Medicine::where('kode_barang', $dataSet['kode_barang'])->get()->first();
            }
            //input data stok
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

            $harga = $dataSet['harga_jual_asli'];
            $pajak = 0;
            // $harga_modal = $harga - 2000;
            $pajak_rupiah = $dataSet['harga_modal_asli'] * $pajak / 100;
            // $pajak_rupiah = $harga_modal * $pajak / 100;
            // $harganya = $harga_modal + $pajak_rupiah;
            $harganya = $dataSet['harga_modal_asli'] + $pajak_rupiah;
            // $mt_barang = Medicine::where('id', $dataSet['idbarang'])->get()->first();
            $rasio_sedang = $dataSet['rasio_sedang'];
            $rasio_kecil = $dataSet['rasio_kecil'];
            // $rasio_sedang = 1;
            // $rasio_kecil = 1;
            $satuan_besar = $dataSet['satuan_besar'];
            $satuan_sedang = $dataSet['satuan_sedang'];
            $satuan_kecil = $dataSet['satuan_kecil'];
            $stok_besar = $dataSet['stok_besar'];
            $stok_sedang = $dataSet['stok_sedang'];
            $stok_kecil = $dataSet['stok_kecil'];
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
                'satuan' => $dataSet['satuan_besar'],
                'harga_beli' => $harganya,
                //harga satu box
                'diskon_persen' => 0,
                'diskon_rupiah' => 0,
                // 'no_batch' => $mt_barang['kode_barang'],
                'no_batch' => $dataSet['batch'],
                'tgl_expired' => $dataSet['ed']
            ];
            $po_detail = po_detail::create($data_detail);
            if ($harga > 0) {
                $harga_jual = $harga / $rasio_kecil;
                Medicine::where('kode_barang', $mt_barang['kode_barang'])->update([
                    'rasio_sedang' => $dataSet['rasio_sedang'],
                    'rasio_kecil' => $dataSet['rasio_kecil'],
                    'satuan_besar' => $dataSet['satuan_besar'],
                    'satuan_sedang' => $dataSet['satuan_sedang'],
                    'satuan_kecil' => $dataSet['satuan_kecil'],
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
                    'rasio_sedang' => $dataSet['rasio_sedang'],
                    'rasio_kecil' => $dataSet['rasio_kecil'],
                    'satuan_besar' => $dataSet['satuan_besar'],
                    'satuan_sedang' => $dataSet['satuan_sedang'],
                    'satuan_kecil' => $dataSet['satuan_kecil'],
                    'sediaan' => $dataSet['satuan_kecil']
                ]);
            }
            $harga_sedang = $harganya / $rasio_sedang;
            $harga_kecil = $harga_sedang / $rasio_kecil;
            //konversi_kesatuan_kecil
            // $stok_masuk =$dataSet['qty'] * $rasio_sedang * $rasio_kecil;
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
                'tgl_expired' => $dataSet['ed'],
                'harga_modal_satuan_besar' => $harganya,
                //harga_satuan_besar
                'harga_modal_satuan_sedang' => $harga_sedang,
                //harga_satuan_sedang
                'harga_modal_satuan_kecil' => $harga_kecil,
                //harga_satuan_kecil
                'kode_batch' => $dataSet['batch'],
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
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Data master stok baru berhasil disimpan!'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal input database: ' . $e->getMessage()
            ], 500);
        }
    }
    public function cekstokbarangauto(Request $request)
    {
        $log_terakhir = DB::table('log_transaksi_stok')
            ->where('kode_barang', $request->kode_barang)
            ->orderBy('id', 'desc')
            ->first(); // Mengambil 1 data terakhir saja (mengembalikan objek / null)
        if ($log_terakhir) {
            $stok_sekarang = $log_terakhir->stok_now; // sesuaikan nama kolom stok Anda
        } else {
            // Log belum ada atau barang baru
            $stok_sekarang = 0;
        }
        $databarang = Medicine::where('kode_barang', $request->kode_barang)->get()->first();
        $html = view('Kasir.info_stok_auto', compact(['stok_sekarang', 'databarang']))->render();
        $response = [
            'code' => 200,
            'view' => $html,
            'message' => 'sukses'
        ];
        echo json_encode($response);
        die;
    }
    public function barangcariauto(Request $request)
    {
        $term = $request->get('term');

        // Cari barang berdasarkan nama_barang atau merk dagang
        $data = DB::table('mt_barang') // Sesuaikan nama tabel master barang Anda
            ->where('nama_dagang', 'LIKE', '%' . $term . '%')
            ->limit(10) // Batasi maksimal 10 data yang muncul agar query cepat
            ->get();
        // Kembalikan dalam bentuk JSON agar dibaca oleh AJAX JQuery
        return response()->json($data);
    }
    public function indexkasir2()
    {
        $now = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $date_start = $now->format('Y-m-d');
        $date_end = $end->format('Y-m-d');
        $menu = 'kasir2';
        $date = $this->get_date2();
        $get_sesi = db::select('select * from ts_sesi_kasir where date(tgl_sesi_kasir) = ? and status = ? and id_user = ?', [$date, 1, auth()->user()->id]);
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
        return view('Kasir.indexkasir2', compact([
            'menu',
            'date_start',
            'date_end',
            'get_sesi',
            'notif_ed',
            'notif_hutang',
            'total_notif',
            'overdue_count',
            'upcoming_count'
        ]));
    }
    public function searchByBatch(Request $request)
    {
        $batch = $request->batch;

        $data = DB::connection('mysql')->table('mt_sediaan_obat as p')
            ->join('mt_barang as b', 'p.kode_barang', '=', 'b.kode_barang')
            ->select('b.rasio_sedang', 'b.rasio_kecil', 'p.kode_barang', 'p.kode_batch', 'b.nama_dagang', 'b.satuan_besar', 'b.satuan_sedang', 'b.satuan_kecil', 'b.harga_jual')
            ->where('p.kode_batch', $batch)
            ->first();

        if ($data) {
            return response()->json(['success' => true, 'data' => $data]);
        }

        return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
    }
    public function simpanpenjualan(Request $request)
    {
        // 1. Validasi Request
        if (!$request->items || count($request->items) == 0) {
            return response()->json(['success' => false, 'message' => 'Tidak ada item untuk dibeli.']);
        }
        // Gunakan Transaction agar data aman
        DB::beginTransaction();
        try {
            $no_invoice = $this->get_inv();
            $kembalian = $request->uang_bayar - $request->total_akhir;
            $gt = $request->total_akhir;
            $uang = $request->uang_bayar;
            $data_header = [
                'no_invoice' => $no_invoice,
                'tgl_transaksi' => $this->get_now(),
                'id_sesi_kasir' => $request->id_sesi,
                'id_user' => auth()->user()->id,
                'total_harga' => $request->total_akhir,
                'diskon' => 0,
                'pajak_ppn' => 0,
                'total_bayar' => $request->total_akhir,
                'nominal_terima' => $request->uang_bayar,
                'nominal_kembali' => $kembalian,
                'status' => '0',
            ];
            $header = model_ts_header::create($data_header);
            $headerid = $header->id;
            foreach ($request->items as $item) {
                $kode_barang = $item['kode_barang'];
                $mt_barang = Medicine::where('kode_barang', $kode_barang)->first();
                $satuan = $item['satuan'];
                if ($satuan == 'besar') {
                    $qtybesar = $mt_barang['rasio_sedang'] * $mt_barang['rasio_kecil'];
                    $qty = $qtybesar * $item['qty'];
                } elseif ($satuan == 'sedang') {
                    $besarnya = $mt_barang['rasio_sedang'] * $mt_barang['rasio_kecil'];
                    $qtysedang = $besarnya / $mt_barang['rasio_sedang'];
                    $qty = $qtysedang * $item['qty'];
                } else {
                    $qty = $item['qty'];
                }
                $diskon = 0;
                $nama_barang = $item['nama_barang'];
                $kode_barang = $kode_barang;
                $qty_dibutuhkan = $qty; // Misal: 10
                // Ambil semua sediaan yang stoknya > 0, urutkan dari ED terdekat (FEFO)
                $semua_sediaan = model_sediaan_barang::where('kode_barang', $kode_barang)
                    ->where('stok_sekarang', '>', 0)
                    ->where('kode_batch', $item['kode_batch'])
                    ->orderBy('tgl_expired', 'asc')
                    ->get();
                foreach ($semua_sediaan as $sediaan) {
                    if ($qty_dibutuhkan <= 0) break; // Jika sudah terpenuhi, berhenti
                    if ($sediaan->stok_sekarang >= $qty_dibutuhkan) {
                        // Jika stok di batch ini cukup untuk menutupi sisa kebutuhan
                        $sediaan->stok_sekarang -= $qty_dibutuhkan;
                        $sediaan->save();
                        // Catat log transaksi (ambil $qty_dibutuhkan)
                        $this->catatLog($sediaan->id, $qty_dibutuhkan, $header->id, $diskon);
                        $qty_dibutuhkan = 0; // Kebutuhan terpenuhi
                    } else {
                        // Jika stok di batch ini tidak cukup, ambil semua yang ada
                        $ambil = $sediaan->stok_sekarang;
                        $qty_dibutuhkan -= $ambil; // Kurangi sisa kebutuhan
                        $sediaan->stok_sekarang = 0; // Habiskan stok batch ini
                        $sediaan->save();
                        // Catat log transaksi (ambil sebesar $ambil)
                        $this->catatLog($sediaan->id, $ambil, $header->id, $diskon);
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

            // Jika semua lancar, komit data

            model_ts_header::where('id', $header->id)->update(['status' => 1]);
            $headerid = $header->id;
            // $html = view('Kasir.view_kembalian', compact(['gt', 'uang', 'headerid']))->render();
            DB::commit();
            $id_header = $header->id;
            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil disimpan!',
                'id_penjualan' => $id_header // Bisa digunakan untuk print struk
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }
    public function indexlogsesikasir()
    {
        $now = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $date_start = $now->format('Y-m-d');
        $date_end = $end->format('Y-m-d');
        $menu = 'logsesikasir';
        $date = $this->get_date2();
        $get_sesi = db::select('select * from ts_sesi_kasir where date(tgl_sesi_kasir) = ? and status = ?', [$date, 1]);
        $skrg = $this->get_date2();
        return view('Kasir.indexlogsesikasir', compact([
            'menu',
            'date_start',
            'date_end',
            'get_sesi',
            'skrg'
        ]));
    }
    public function logtransaksikasir()
    {
        $now = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $date_start = $now->format('Y-m-d');
        $date_end = $end->format('Y-m-d');
        $menu = 'logtransaksikasir';
        $date = $this->get_date2();
        $get_sesi = db::select('select * from ts_sesi_kasir where date(tgl_sesi_kasir) = ? and status = ?', [$date, 1]);
        return view('Kasir.indexlogtransaksikasir', compact([
            'menu',
            'date_start',
            'date_end',
            'get_sesi',
            'date'
        ]));
    }
    public function indexriwayatpenjualan()
    {
        $now = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $date_start = $now->format('Y-m-d');
        $date_end = $end->format('Y-m-d');
        $menu = 'riwayatpenjualan';
        $date = $this->get_date2();
        $get_sesi = db::select('select * from ts_sesi_kasir where date(tgl_sesi_kasir) = ? and status = ?', [$date, 1]);
        return view('Kasir.indexriwayatpenjualan', compact([
            'menu',
            'date_start',
            'date_end',
            'get_sesi',
            'date'
        ]));
    }
    public function indexlogtransaksistok()
    {
        $now = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $date_start = $now->format('Y-m-d');
        $date_end = $end->format('Y-m-d');
        $menu = 'logtransaksistok';
        $date = $this->get_date2();
        $get_sesi = db::select('select * from ts_sesi_kasir where date(tgl_sesi_kasir) = ? and status = ?', [$date, 1]);
        return view('Kasir.index_log_transaksi_stok', compact([
            'menu',
            'date_start',
            'date_end',
            'get_sesi',
            'date'
        ]));
    }
    public function ambildatalogsesi(Request $request)
    {
        $awal = $request->tglawal;
        $tglakhir = $request->tglakhir;
        $data = db::select('select a.*,b.nama from ts_sesi_kasir a inner join user b on a.id_user = b.id where date(a.tgl_sesi_kasir) BETWEEN ? and ? ORDER BY a.id DESC', [$awal, $tglakhir]);
        $html = view('Kasir.tabel_log_kasir', compact(['data', 'awal', 'tglakhir']))->render();
        $response = [
            'code' => 200,
            'view' => $html,
            'message' => 'sukses'
        ];
        echo json_encode($response);
        die;
    }
    public function ambillogtransaksi(Request $request)
    {
        $awal = $request->tglawal;
        $tglakhir = $request->tglakhir;
        $data = db::select('select a.*,d.`nama` from ts_penjualan_header a
        inner join user d on a.`id_user` = d.id where date(a.`tgl_transaksi`) between ? AND ? ORDER BY a.id DESC', [$awal, $tglakhir]);
        $html = view('Kasir.tabel_log_transaksi_kasir', compact(['data', 'awal', 'tglakhir']))->render();
        $response = [
            'code' => 200,
            'view' => $html,
            'message' => 'sukses'
        ];
        echo json_encode($response);
        die;
    }
    public function ambilriwayatkartustok(Request $request)
    {
        $awal = $request->tglawal;
        $tglakhir = $request->tglakhir;
        $data = DB::table('log_transaksi_stok as a')
            // 1. Join dengan subquery untuk mengambil ID terakhir per kode_barang
            ->join(DB::raw('(SELECT MAX(id) as max_id FROM log_transaksi_stok GROUP BY kode_barang) as b_latest'), function ($join) {
                $join->on('a.id', '=', 'b_latest.max_id');
            })
            // 2. Join dengan master barang untuk ambil nama/detail barang
            ->join('mt_barang as b', 'a.kode_barang', '=', 'b.kode_barang')
            ->select(
                'b.nama_dagang',
                'b.nama_obat',
                'b.produsen',
                'b.satuan_besar',
                'b.satuan_sedang',
                'b.satuan_kecil',
                'b.rasio_sedang',
                'b.rasio_kecil',
                'a.*'
            )
            // 3. Filter tanggal (opsional jika ingin membatasi rentang log tertentu)
            ->whereBetween(DB::raw('DATE(a.tgl_input)'), [$awal, $tglakhir])
            ->orderBy('a.id', 'DESC')
            ->get();
        $html = view('Kasir.tabel_riwayat_stok', compact(['data', 'awal', 'tglakhir']))->render();
        $response = [
            'code' => 200,
            'view' => $html,
            'message' => 'sukses'
        ];
        echo json_encode($response);
        die;
    }
    public function ambilriwayatpenjualan(Request $request)
    {
        $awal = $request->tglawal;
        $tglakhir = $request->tglakhir;
        $data = db::select('SELECT 
        a.`no_invoice`
        ,a.`tgl_transaksi`
        ,c.`nama_dagang`
        ,d.`nama`
        ,b.`qty`
        ,b.`subtotal`
        ,b.`grandtotal`
        ,b.`harga_jual`
        ,b.`diskon` 
        ,b.`status_retur`
        ,c.`rasio_sedang`
        ,c.`rasio_kecil`
        ,c.`satuan_besar`
        ,c.`satuan_sedang`
        ,c.`satuan_kecil`
        FROM ts_penjualan_header a
        INNER JOIN ts_penjualan_detail b ON a.id = b.`id_header`
        INNER JOIN mt_barang c ON b.`kode_barang` = c.`kode_barang`
        INNER JOIN USER d ON a.`id_user` = d.id where date(a.`tgl_transaksi`) between ? AND ? ORDER BY a.id DESC', [$awal, $tglakhir]);
        $html = view('Kasir.tabel_riwayat_penjualan', compact(['data', 'awal', 'tglakhir']))->render();
        $response = [
            'code' => 200,
            'view' => $html,
            'message' => 'sukses'
        ];
        echo json_encode($response);
        die;
    }
    public function ambildetailtransaksi(Request $request)
    {
        $id = $request->id;
        $tglakhir = $request->tglakhir;
        $data = db::select('select b.id as iddetail ,c.`nama_dagang`,d.`nama`,b.`qty`,b.`subtotal`,b.`grandtotal`,b.`harga_jual`,b.`diskon`,b.status_retur,c.satuan_besar,c.satuan_sedang,c.satuan_kecil,c.rasio_sedang,c.rasio_kecil from ts_penjualan_header a
        inner join ts_penjualan_detail b on a.id = b.`id_header`
        inner join mt_barang c on b.`kode_barang` = c.`kode_barang`
        inner join user d on a.`id_user` = d.id where a.id = ? ORDER BY a.id DESC', [$id]);
        $html = view('Kasir.tabel_detail_transaksi_kasir', compact(['data']))->render();
        $response = [
            'code' => 200,
            'view' => $html,
            'message' => 'sukses'
        ];
        echo json_encode($response);
        die;
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
        $hasil_transaksi = [];
        $gt = 0;
        foreach ($arrayobat as $d) {
            $mt_barang = db::select('select * from mt_barang where kode_barang = ?', [$d['kode_barang']]);
            $satuan = $d['satuan'];
            if ($satuan == 'besar') {
                $harga_sedang = $d['harga'] * $mt_barang[0]->rasio_kecil;
                $harganya = $harga_sedang * $mt_barang[0]->rasio_sedang;
                $satuan_terpilih = $mt_barang[0]->satuan_besar;
            } elseif ($satuan == 'sedang') {
                $harganya = $d['harga'] * $mt_barang[0]->rasio_kecil;
                $satuan_terpilih = $mt_barang[0]->satuan_sedang;
            } else {
                $harganya = $d['harga'];
                $satuan_terpilih = $mt_barang[0]->satuan_kecil;
            }

            $subtotal = $d['qty'] * $harganya - $d['diskon'];
            $gt = $gt + $subtotal;


            // Masukkan ke dalam array hasil
            $hasil_transaksi[] = [
                'nama_obat'     => $mt_barang[0]->nama_dagang, // Mengambil dari Master
                'satuan_pilih'  => $satuan_terpilih,      // Nama asli satuan (Box/Strip/Tablet)
                'qty'           => $d['qty'],
                'harga_satuan'  => $harganya,
                'diskon'        => $d['diskon'] ?? 0,
                'subtotal'      => $subtotal
            ];
        }
        $v_gt = number_format($gt, 0, ',', '.');
        $html = view('Kasir.hasil_proses', compact(['gt', 'v_gt', 'hasil_transaksi']))->render();
        $response = [
            'code' => 200,
            'html' => $html,
            'message' => 'sukses'
        ];
        echo json_encode($response);
        die;
    }
    public function returheader(Request $request)
    {
        DB::beginTransaction();
        try {
            $detail = model_ts_detail::where('id_header', $request->id)->where('status_retur', 1)->get();
            $header = model_ts_header::where('id', $request->id)->first();
            model_ts_header::where('id', $request->id)->update(['status' => 2, 'total_retur' => $header['total_harga']]);
            model_ts_detail::where('id_header', $request->id)->update(['status_retur' => 2]);
            $sesi_kasir = model_ts_sesi_kasir::where('id', $header->id_sesi_kasir)->first();
            if ($sesi_kasir->status == 2) {
                $total_hh = $header['total_harga'] - $header['total_retur'];
                $saldo_akhir = $sesi_kasir->saldo_akhir - $total_hh;
                model_ts_sesi_kasir::where('id', $header->id_sesi_kasir)->update(['saldo_akhir' => $saldo_akhir]);
            }
            foreach ($detail as $dd) {
                $log_terakhir = DB::table('log_transaksi_stok')
                    ->where('kode_barang', $dd->kode_barang)
                    ->orderBy('id', 'desc')
                    ->first();
                $mt_barang = Medicine::where('kode_barang', $dd->kode_barang)->first();
                // Tentukan stok_last (jika log belum ada, ambil dari stok_now di mt_sediaan)
                $stok_last = $log_terakhir->stok_now;
                $stok_now = $stok_last + $dd->qty;
                $data_log = [
                    'id_dokumen' => $dd->id,
                    'kode_barang' => $dd->kode_barang,
                    'stok_in' => $dd->qty,
                    'stok_out' => '0',
                    'stok_last' => $stok_last,
                    'stok_now' => $stok_now,
                    'tgl_input' => $this->get_now(),
                    'keterangan' => 'Retur Pembelian',
                    'id_sediaan' => $dd->id_sediaan,
                    'harga_jual' => $dd->harga_jual,
                    'harga_modal' => $dd->harga_modal
                ];
                model_log_transaksi_stok::create($data_log);
                $ss = model_sediaan_barang::where('id', $dd->id_sediaan)->first();
                $stok_sediaan = $ss->stok_sekarang + $dd->qty;
                model_sediaan_barang::where('id', $dd->id_sediaan)->update(['stok_sekarang' => $stok_sediaan]);
            }
            DB::commit();
            $response = [
                'code' => 200,
                'view' => '',
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
    public function returdetail(Request $request)
    {
        DB::beginTransaction();
        try {
            $iddetail = $request->id;
            $detail = model_ts_detail::where('id',  $iddetail)->first();
            model_ts_detail::where('id', $iddetail)->update(['status_retur' => 2]);
            $header = model_ts_header::where('id', $detail->id_header)->first();
            $total_retur = $header->total_retur;
            $log_terakhir = DB::table('log_transaksi_stok')
                ->where('kode_barang', $detail->kode_barang)
                ->orderBy('id', 'desc')
                ->first();
            // Tentukan stok_last (jika log belum ada, ambil dari stok_now di mt_sediaan)
            $stok_last = $log_terakhir->stok_now;
            $stok_now = $stok_last + $detail->qty;
            $data_log = [
                'id_dokumen' => $detail->id,
                'kode_barang' => $detail->kode_barang,
                'stok_in' => $detail->qty,
                'stok_out' => '0',
                'stok_last' => $stok_last,
                'stok_now' => $stok_now,
                'tgl_input' => $this->get_now(),
                'keterangan' => 'Retur Pembelian',
                'id_sediaan' => $detail->id_sediaan,
                'harga_jual' => $detail->harga_jual,
                'harga_modal' => $detail->harga_modal
            ];
            $ss = model_sediaan_barang::where('id', $detail->id_sediaan)->first();
            $stok_sediaan = $ss->stok_sekarang + $detail->qty;
            model_log_transaksi_stok::create($data_log);
            model_sediaan_barang::where('id', $detail->id_sediaan)->update(['stok_sekarang' => $stok_sediaan]);
            model_ts_header::where('id', $header->id)->update(['total_retur' => $total_retur + $detail->grandtotal]);
            $header2 = model_ts_header::where('id', $detail->id_header)->first();
            if ($header2->total_retur == $header->total_bayar) {
                model_ts_header::where('id', $header->id)->update(['status' => 2]);
            }
            $sesi_kasir = model_ts_sesi_kasir::where('id', $header2->id_sesi_kasir)->first();
            if ($sesi_kasir->status == 2) {
                $saldo_akhir = $sesi_kasir->saldo_akhir - $detail->grandtotal;
                model_ts_sesi_kasir::where('id', $header->id_sesi_kasir)->update(['saldo_akhir' => $saldo_akhir]);
            }
            DB::commit();
            $response = [
                'code' => 200,
                'view' => '',
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
                        $this->catatLog($sediaan->id, $qty_dibutuhkan, $header->id, $diskon);
                        $qty_dibutuhkan = 0; // Kebutuhan terpenuhi
                    } else {
                        // Jika stok di batch ini tidak cukup, ambil semua yang ada
                        $ambil = $sediaan->stok_sekarang;
                        $qty_dibutuhkan -= $ambil; // Kurangi sisa kebutuhan
                        $sediaan->stok_sekarang = 0; // Habiskan stok batch ini
                        $sediaan->save();
                        // Catat log transaksi (ambil sebesar $ambil)
                        $this->catatLog($sediaan->id, $ambil, $header->id, $diskon);
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
            model_ts_header::where('id', $header->id)->update(['status' => 1]);
            $headerid = $header->id;
            $html = view('Kasir.view_kembalian', compact(['gt', 'uang', 'headerid']))->render();
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
    public function catatLog($idsediaan, $qty_dibutuhkan, $id, $diskon)
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
        $ts_header = db::select('select sum(total_harga ) as total,sum(total_retur) as total_retur from ts_penjualan_header where id_sesi_kasir = ?', [$id]);
        $awal = model_ts_sesi_kasir::where('id', $id)->first();
        $saldo_awal = $awal->saldo_awal;
        $data = [
            'saldo_akhir' => $ts_header[0]->total + $saldo_awal - $ts_header[0]->total_retur,
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
    public function cetakStruk($id)
    {
        $header = model_ts_header::where('id', $id)->first();
        // $detail = model_ts_detail::where('id_header',$id)->get();
        $detail = model_ts_detail::where('ts_penjualan_detail.id_header', $id)
            ->join('mt_barang', 'ts_penjualan_detail.kode_barang', '=', 'mt_barang.kode_barang')
            ->select(
                'ts_penjualan_detail.*',
                'mt_barang.nama_dagang',
                'mt_barang.satuan_besar',
                'mt_barang.satuan_sedang',
                'mt_barang.satuan_kecil',
                'mt_barang.rasio_sedang',
                'mt_barang.rasio_kecil',
                'ts_penjualan_detail.harga_jual'
            )
            ->get();
        return view('Laporan.nota_kynova', compact([
            'header',
            'detail'
        ]));
    }
}
