<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Medicine;
use App\Models\model_sediaan_barang;
use App\Models\Supplier;

class DataMasterController extends Controller
{
    public function Index()
    {
        $now = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $date_start = $now->format('Y-m-d');
        $date_end = $end->format('Y-m-d');
        $menu = 'masterbarang';
        $satuan = db::select('select * from mt_satuan');
        return view('DataMaster.indexmasterbarang', compact([
            'menu',
            'satuan'
        ]));
    }
    public function getdatabarang()
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
            'satuan_kecil',
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
                return '<button class="btn btn-sm btn-warning editbarang" idbarang="' . $row->id . '" data-bs-toggle="modal" data-bs-target="#modaleditbarang"><i class="bi bi-pencil-square"></i></button> <button style="margin-left:1px" class="btn btn-sm btn-danger hapusbarang" idbarang="' . $row->id . '" namabarang ="' . $row->nama_dagang . '"><i class="bi bi-trash"></i></button>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }
    public function getdatabarang2()
    {
        // Query langsung ke tabel mt_barang tanpa join/relasi
        $data = Medicine::select([
            'mt_barang.id',
            'mt_barang.nama_obat',
            'mt_barang.kode_barang',
            'mt_barang.nama_dagang', // Sesuaikan dengan nama kolom asli di tabel mt_barang
            'mt_barang.produsen',
            'mt_barang.satuan_besar',
            'mt_barang.satuan_sedang',
            'mt_barang.satuan_kecil',
            'mt_barang.aturan_pakai',
            'mt_barang.sediaan',
            'mt_barang.harga_modal',
            'mt_barang.harga_jual',
            'mt_barang.margin_penjualan',
            DB::raw('(SELECT stok_now FROM log_transaksi_stok 
                  WHERE log_transaksi_stok.kode_barang = mt_barang.kode_barang 
                  ORDER BY id DESC LIMIT 1) as stok_terakhir')
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
                return '<button class="btn btn-sm btn-warning editharga" idbarang="' . $row->id . '" hargajual="' . $row->harga_jual . '" hargajual2="' . number_format($row->harga_jual, 0, ',', '.') . '" data-bs-toggle="modal" data-bs-target="#modaledithargajual"><i class="bi bi-pencil-square"></i></button> <button style="margin-left:1px" class="btn btn-sm btn-success infosediaan" idbarang="' . $row->id . '" namabarang ="' . $row->nama_dagang . '" data-bs-toggle="modal" data-bs-target="#modaldetailsediaan"><i class="bi bi-clipboard2-data"></i></button>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }
    public function getdatabarang3()
    {
        // Query langsung ke tabel mt_barang tanpa join/relasi
        $data = Medicine::select([
            'mt_barang.id',
            'mt_barang.nama_obat',
            'mt_barang.kode_barang',
            'mt_barang.nama_dagang', // Sesuaikan dengan nama kolom asli di tabel mt_barang
            'mt_barang.produsen',
            'mt_barang.satuan_besar',
            'mt_barang.satuan_sedang',
            'mt_barang.rasio_sedang',
            'mt_barang.rasio_kecil',
            'mt_barang.satuan_kecil',
            'mt_barang.aturan_pakai',
            'mt_barang.sediaan',
            'mt_barang.harga_modal',
            'mt_barang.harga_jual',
            'mt_barang.margin_penjualan',
            DB::raw('(SELECT stok_now FROM log_transaksi_stok 
                  WHERE log_transaksi_stok.kode_barang = mt_barang.kode_barang 
                  ORDER BY id DESC LIMIT 1) as stok_terakhir')
        ])->havingRaw('stok_terakhir > 0');;

        return DataTables::of($data)
            ->addIndexColumn()
            // Gunakan filterColumn jika Anda ingin kustomisasi pencarian
            ->filterColumn('nama_obat', function ($query, $keyword) {
                $sql = "mt_barang.nama_obat LIKE ? OR mt_barang.nama_dagang LIKE ? OR mt_barang.produsen LIKE ?";
                $query->whereRaw($sql, ["%{$keyword}%", "%{$keyword}%", "%{$keyword}%"]);
            })
            ->addColumn('action', function ($row) {
                $dataSatuan = [];
                if ($row->satuan_besar) $dataSatuan[] = "besar:" . $row->satuan_besar;
                if ($row->satuan_sedang) $dataSatuan[] = "sedang:" . $row->satuan_sedang;
                if ($row->satuan_kecil)  $dataSatuan[] = "kecil:" . $row->satuan_kecil;

                $listSatuan = implode(',', $dataSatuan); // Hasilnya: "besar:Box,sedang:Strip,kecil:Tablet"
                // Berikan atribut data-nama yang lengkap
                return '<button type="button" class="btn btn-sm btn-success pilihbarang" 
                idbarang="' . $row->id . '" 
                kodebarang="' . $row->kode_barang . '" 
                harga="' . $row->harga_jual . '"
                aturanpakai="' . $row->aturan_pakai . '"
                generik="' . $row->nama_obat . '"
                merk="' . $row->nama_dagang . '"
                produsen="' . $row->produsen . '"
                namabarang ="' . ($row->nama_dagang ?? $row->nama_obat) . '"
                satuan ="' . $listSatuan . '">
                <i class="bi bi-check2-square"></i> Pilih
                </button>';
            })
            ->rawColumns(['action', 'nama_obat'])
            ->make(true);
    }
    public function getdatasupplier()
    {
        // Query langsung ke tabel mt_barang tanpa join/relasi
        $data = Supplier::select([
            'id',
            'kode_supplier',
            'nama_supplier',
            'alamat', // Sesuaikan dengan nama kolom asli di tabel mt_barang
            'telepon',
            'email'
        ]);

        return DataTables::of($data)
            ->addIndexColumn()
            // Gunakan filterColumn jika Anda ingin kustomisasi pencarian
            ->filterColumn('nama_supplier', function ($query, $keyword) {
                $query->where('nama_supplier', 'like', "%{$keyword}%");
            })
            ->filterColumn('alamat', function ($query, $keyword) {
                $query->where('alamat', 'like', "%{$keyword}%");
            })
            ->addColumn('action', function ($row) {
                return '<button class="btn btn-sm btn-warning editsupplier" idsupplier="' . $row->id . '" data-bs-toggle="modal" data-bs-target="#modaleditsupplier"><i class="bi bi-pencil-square"></i></button> <button style="margin-left:1px" class="btn btn-sm btn-danger hapussupplier" idsupplier="' . $row->id . '" namasupplier ="' . $row->nama_supplier . '"><i class="bi bi-trash"></i></button>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }
    public function simpansupplier(request $request)
    {
        try {
            $data = json_decode($_POST['data'], true);
            foreach ($data as $nama) {
                $index =  $nama['name'];
                $value =  $nama['value'];
                $dataSet[$index] = $value;
            }
            $datasupplier = [
                'kode_supplier' => $this->createkodesupplier(),
                'nama_supplier' => strtoupper($dataSet['nama_supplier']),
                'telepon' => strtoupper($dataSet['telepon']),
                'alamat' => strtoupper($dataSet['alamat']),
                'email' => $dataSet['email'],
                'pic' => auth()->user()->id,
                'tgl_entry' => $this->get_now(),
            ];
            Supplier::create($datasupplier);
            $response = [
                'code' => 200,
                'message' => 'sukses'
            ];
            echo json_encode($response);
            die;
        } catch (\Exception $e) {
            $err = $e->getMessage();
            $data = [
                'kode' => 500,
                'message' => 'Ops error ! ...( ' . $err . ' )'
            ];
            echo json_encode($data);
            die;
        }
    }
    public function simpanbarang(request $request)
    {
        try {
            $data = json_decode($_POST['data'], true);
            foreach ($data as $nama) {
                $index =  $nama['name'];
                $value =  $nama['value'];
                $dataSet[$index] = $value;
            }
            $dataSet['tgl_entry'] = $this->get_now();
            $databarang = [
                'kode_barang' => $this->createkodebarang(),
                'produsen' => strtoupper($dataSet['produsen']),
                'nama_dagang' => strtoupper($dataSet['merkdagang']),
                'nama_obat' => strtoupper($dataSet['namabarang']),
                'satuan_besar' => $dataSet['satuanbesar'],
                'satuan_sedang' => $dataSet['satuansedang'],
                'satuan_kecil' => $dataSet['satuankecil'],
                'sediaan' => $dataSet['sediaan'],
                'aturan_pakai' => $dataSet['aturanpakai'],
                'pic' => auth()->user()->id,
                'tgl_entry' => $this->get_now(),
                'tgl_update' => $this->get_now(),
                'rasio_sedang' => $dataSet['rasiosatuansedang'],
                'rasio_kecil' => $dataSet['rasiosatuankecil'],
            ];
            medicine::create($databarang);
            $response = [
                'code' => 200,
                'message' => 'sukses'
            ];
            echo json_encode($response);
            die;
        } catch (\Exception $e) {
            $err = $e->getMessage();
            $data = [
                'kode' => 500,
                'message' => 'Ops error ! ...( ' . $err . ' )'
            ];
            echo json_encode($data);
            die;
        }
    }
    public function simpaneditbarang(request $request)
    {
        try {
            $data = json_decode($_POST['data'], true);
            foreach ($data as $nama) {
                $index =  $nama['name'];
                $value =  $nama['value'];
                $dataSet[$index] = $value;
            }
            $databarang = [
                'produsen' => strtoupper($dataSet['produsen']),
                'nama_dagang' => strtoupper($dataSet['merkdagang']),
                'nama_obat' => strtoupper($dataSet['namabarang']),
                'satuan_besar' => $dataSet['satuanbesar'],
                'satuan_sedang' => $dataSet['satuansedang'],
                'satuan_kecil' => $dataSet['satuankecil'],
                'sediaan' => $dataSet['sediaan'],
                'aturan_pakai' => $dataSet['aturanpakai'],
                'pic2' => auth()->user()->id,
                'tgl_update' => $this->get_now(),
                'rasio_sedang' => $dataSet['rasiosatuansedang'],
                'rasio_kecil' => $dataSet['rasiosatuankecil'],
            ];
            medicine::where('id', $dataSet['idbarang'])->update($databarang);
            $response = [
                'code' => 200,
                'message' => 'sukses'
            ];
            echo json_encode($response);
            die;
        } catch (\Exception $e) {
            $err = $e->getMessage();
            $data = [
                'kode' => 500,
                'message' => 'Ops error ! ...( ' . $err . ' )'
            ];
            echo json_encode($data);
            die;
        }
    }
    public function simpaneditsupplier(request $request)
    {
        try {
            $data = json_decode($_POST['data'], true);
            foreach ($data as $nama) {
                $index =  $nama['name'];
                $value =  $nama['value'];
                $dataSet[$index] = $value;
            }
            $datasupplier = [
                'nama_supplier' => strtoupper($dataSet['nama_supplier']),
                'email' => strtoupper($dataSet['email']),
                'telepon' => strtoupper($dataSet['telepon']),
                'alamat' => $dataSet['alamat'],
            ];
            Supplier::where('id', $dataSet['id_supplier'])->update($datasupplier);
            $response = [
                'code' => 200,
                'message' => 'sukses'
            ];
            echo json_encode($response);
            die;
        } catch (\Exception $e) {
            $err = $e->getMessage();
            $data = [
                'kode' => 500,
                'message' => 'Ops error ! ...( ' . $err . ' )'
            ];
            echo json_encode($data);
            die;
        }
    }
    public function hapusbarang(request $request)
    {
        try {
            $id = $request->idbarang;
            Medicine::where('id', $id)->delete();
            $response = [
                'code' => 200,
                'message' => 'sukses'
            ];
            echo json_encode($response);
            die;
        } catch (\Exception $e) {
            $err = $e->getMessage();
            $data = [
                'kode' => 500,
                'message' => 'Ops error ! ...( ' . $err . ' )'
            ];
            echo json_encode($data);
            die;
        }
    }
    public function simpanhargajual(request $request)
    {
        try {
            $id = $request->idbarang;
            $harga = $request->harga;
            $mt_barang = db::select('select * from mt_barang where id = ?',[$id]);
            $rasio_sedang = $mt_barang[0]->rasio_sedang;
            $rasio_kecil = $mt_barang[0]->rasio_kecil;
            $harga_jual = $harga / $rasio_kecil;
            Medicine::where('id', $id)->update(['harga_jual' => $harga_jual]);
            $response = [
                'code' => 200,
                'message' => 'sukses'
            ];
            echo json_encode($response);
            die;
        } catch (\Exception $e) {
            $err = $e->getMessage();
            $data = [
                'kode' => 500,
                'message' => 'Ops error ! ...( ' . $err . ' )'
            ];
            echo json_encode($data);
            die;
        }
    }
    public function hapussupplier(request $request)
    {
        try {
            $id = $request->idsupplier;
            Supplier::where('id', $id)->delete();
            $response = [
                'code' => 200,
                'message' => 'sukses'
            ];
            echo json_encode($response);
            die;
        } catch (\Exception $e) {
            $err = $e->getMessage();
            $data = [
                'kode' => 500,
                'message' => 'Ops error ! ...( ' . $err . ' )'
            ];
            echo json_encode($data);
            die;
        }
    }
    public function ambildetailsediaanbarang(Request $request)
    {
        $id = $request->idbarang;
        $data_master = Medicine::where('id', $id)->get();
        $kode_barang = $data_master[0]->kode_barang;
        $data_sediaan = model_sediaan_barang::with('supplier')
            ->select('id as idsediaan', 'kode_supplier', 'tgl_input', 'kode_batch', 'tgl_expired', 'stok_awal', 'stok_sekarang', 'harga_modal_satuan_kecil')
            ->where('kode_barang', $kode_barang)
            ->orderBy('id', 'desc') // Mengurutkan berdasarkan ID terbaru
            ->get();
        $html = view('dataMaster.tabel_data_sediaan', compact(['data_sediaan', 'data_master']))->render();
        $response = [
            'code' => 200,
            'html' => $html,
            'message' => 'sukses'
        ];
        echo json_encode($response);
        die;
    }
    public function formeditbarang(Request $request)
    {
        $id = $request->idbarang;
        $data = Medicine::where('id', $id)->get()->first();
        $satuan = db::select('select * from mt_satuan');
        $html = view('dataMaster.formeditbarang', compact(['data', 'satuan']))->render();
        $response = [
            'code' => 200,
            'html' => $html,
            'message' => 'sukses'
        ];
        echo json_encode($response);
        die;
    }
    public function formeditsupplier(Request $request)
    {
        $id = $request->idsupplier;
        $data = Supplier::where('id', $id)->get()->first();
        $html = view('dataMaster.formeditsupplier', compact(['data']))->render();
        $response = [
            'code' => 200,
            'html' => $html,
            'message' => 'sukses'
        ];
        echo json_encode($response);
        die;
    }
    public function Indexmastersupplier()
    {
        $now = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $date_start = $now->format('Y-m-d');
        $date_end = $end->format('Y-m-d');
        $menu = 'mastersupplier';
        return view('DataMaster.indexmastersupplier', compact([
            'menu',
            'date_start',
            'date_end'
        ]));
    }
    public function Indexmasteruser()
    {
        $now = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $date_start = $now->format('Y-m-d');
        $date_end = $end->format('Y-m-d');
        $menu = 'masteruser';
        return view('DataMaster.indexmasteruser', compact([
            'menu',
            'date_start',
            'date_end'
        ]));
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
    public function createkodesupplier()
    {
        $q = DB::connection('mysql')->select('SELECT id,RIGHT(kode_supplier,3) AS kd_max  FROM mt_supplier ORDER BY id DESC LIMIT 1');
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

        return 'SUP' . $kd;
    }
}
