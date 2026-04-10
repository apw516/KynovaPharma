<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DataMasterController;
use App\Http\Controllers\GudangController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\LaporanController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'Index'])->middleware('guest');
Route::get('/login', [AuthController::class, 'index'])->middleware('guest')->name('login');
Route::post('/login', [AuthController::class, 'authenticate']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/home', [AuthController::class, 'logout'])->name('logout')->name('home');
Route::post('/register', [AuthController::class, 'Store'])->middleware('guest')->name('register');

Route::get('/dashboard', [DashboardController::class, 'Index'])->name('indexdashboard');
Route::get('/indexdashboardkeuangan', [DashboardController::class, 'indexdashboardkeuangan'])->name('indexdashboardkeuangan');
Route::get('/indexringkasankeuangan', [DashboardController::class, 'indexringkasankeuangan'])->name('indexringkasankeuangan');
Route::post('ambildatatabelanalisisprodukfastmoving', [DashboardController::class, 'ambildatatabelanalisisprodukfastmoving'])->name('ambildatatabelanalisisprodukfastmoving');
Route::post('ambildatabaranghampirhabis', [DashboardController::class, 'ambildatabaranghampirhabis'])->name('ambildatabaranghampirhabis');
Route::post('ambildatabaranghampired', [DashboardController::class, 'ambildatabaranghampired'])->name('ambildatabaranghampired');
Route::post('ambilringkasankeuangan', [DashboardController::class, 'ambilringkasankeuangan'])->name('ambilringkasankeuangan');
Route::get('/dashboard/cetak-laporan', [DashboardController::class, 'cetakLaporan'])->name('dashboard.cetak');


//CONTROLLER DATA MASTER
Route::get('/indexmasterbarang', [DataMasterController::class, 'Index'])->name('indexmasterbarang');
Route::get('/Indexmastersupplier', [DataMasterController::class, 'Indexmastersupplier'])->name('Indexmastersupplier');
Route::get('/Indexmasteruser', [DataMasterController::class, 'Indexmasteruser'])->name('Indexmasteruser');
Route::get('/getdatabarang', [DataMasterController::class, 'getdatabarang'])->name('getdatabarang');
Route::get('/getdatabarang2', [DataMasterController::class, 'getdatabarang2'])->name('getdatabarang2');
Route::get('/getdatabarang3', [DataMasterController::class, 'getdatabarang3'])->name('getdatabarang3');
Route::post('/simpanbarang', [DataMasterController::class, 'simpanbarang'])->name('simpanbarang');
Route::post('/simpaneditbarang', [DataMasterController::class, 'simpaneditbarang'])->name('simpaneditbarang');
Route::post('/hapusbarang', [DataMasterController::class, 'hapusbarang'])->name('hapusbarang');
Route::post('/formeditbarang', [DataMasterController::class, 'formeditbarang'])->name('formeditbarang');
Route::post('/ambildetailsediaanbarang', [DataMasterController::class, 'ambildetailsediaanbarang'])->name('ambildetailsediaanbarang');
Route::get('/getdatasupplier', [DataMasterController::class, 'getdatasupplier'])->name('getdatasupplier');
//supplier
Route::post('/simpansupplier', [DataMasterController::class, 'simpansupplier'])->name('simpansupplier');
Route::post('/formeditsupplier', [DataMasterController::class, 'formeditsupplier'])->name('formeditsupplier');
Route::post('/simpaneditsupplier', [DataMasterController::class, 'simpaneditsupplier'])->name('simpaneditsupplier');
Route::post('/hapussupplier', [DataMasterController::class, 'hapussupplier'])->name('hapussupplier');
Route::post('/simpanhargajual', [DataMasterController::class, 'simpanhargajual'])->name('simpanhargajual');



//Gudang
Route::get('/indexpurchaseorder', [GudangController::class, 'Index'])->name('indexpurchaseorder');
Route::get('/indexstokinject', [GudangController::class, 'indexstokinject'])->name('indexstokinject');
Route::get('/indexdatastokpersediaan', [GudangController::class, 'indexdatastokpersediaan'])->name('indexdatastokpersediaan');
Route::get('/indexstoksediaan', [GudangController::class, 'indexstoksediaan'])->name('indexstoksediaan');
Route::get('/indexlogkartustok', [GudangController::class, 'indexlogkartustok'])->name('indexlogkartustok');
Route::get('/indexstokretur', [GudangController::class, 'indexstokretur'])->name('indexstokretur');
Route::post('/cariobat', [GudangController::class, 'cariobat'])->name('cariobat');
Route::post('/prosespo', [GudangController::class, 'prosespo'])->name('prosespo');
Route::get('/cari-supplier', [GudangController::class, 'cariSupplier'])->name('cari-supplier');
Route::post('/savepo', [GudangController::class, 'savepo'])->name('savepo');
Route::post('/returpo', [GudangController::class, 'returpo'])->name('returpo');
Route::post('/bayarpo', [GudangController::class, 'bayarpo'])->name('bayarpo');
Route::post('/ambildatapo', [GudangController::class, 'ambildatapo'])->name('ambildatapo');
Route::post('/ambildetailpo', [GudangController::class, 'ambildetailpo'])->name('ambildetailpo');
Route::post('/ambildatalog', [GudangController::class, 'ambildatalog'])->name('ambildatalog');
Route::post('/simpanretursediaan', [GudangController::class, 'simpanretursediaan'])->name('simpanretursediaan');
Route::post('/ambildatastokretur', [GudangController::class, 'ambildatastokretur'])->name('ambildatastokretur');
Route::post('/ambilstokpersediaanbarang', [GudangController::class, 'ambilstokpersediaanbarang'])->name('ambilstokpersediaanbarang');
Route::get('/getdatabarang_opname', [GudangController::class, 'getdatabarang_opname'])->name('getdatabarang_opname');
Route::post('/simpandatainject', [GudangController::class, 'simpandatainject'])->name('simpandatainject');



Route::get('/indexkasir', [KasirController::class, 'Index'])->name('indexkasir');
Route::get('/indexlogsesikasir', [KasirController::class, 'indexlogsesikasir'])->name('indexlogsesikasir');
Route::get('/logtransaksikasir', [KasirController::class, 'logtransaksikasir'])->name('logtransaksikasir');
Route::get('/indexriwayatpenjualan', [KasirController::class, 'indexriwayatpenjualan'])->name('indexriwayatpenjualan');
Route::get('/indexlogtransaksistok', [KasirController::class, 'indexlogtransaksistok'])->name('indexlogtransaksistok');
Route::post('/prosesbarang', [KasirController::class, 'prosesbarang'])->name('prosesbarang');
Route::post('/prosesbarangfinal', [KasirController::class, 'prosesbarangfinal'])->name('prosesbarangfinal');
Route::post('/simpansesikasir', [KasirController::class, 'simpansesikasir'])->name('simpansesikasir');
Route::post('/tutupsesikasir', [KasirController::class, 'tutupsesikasir'])->name('tutupsesikasir');
Route::post('/ambildatalogsesi', [KasirController::class, 'ambildatalogsesi'])->name('ambildatalogsesi');
Route::post('/ambillogtransaksi', [KasirController::class, 'ambillogtransaksi'])->name('ambillogtransaksi');
Route::post('/ambilriwayatpenjualan', [KasirController::class, 'ambilriwayatpenjualan'])->name('ambilriwayatpenjualan');
Route::post('/ambildetailtransaksi', [KasirController::class, 'ambildetailtransaksi'])->name('ambildetailtransaksi');
Route::post('/returheader', [KasirController::class, 'returheader'])->name('returheader');
Route::post('/returdetail', [KasirController::class, 'returdetail'])->name('returdetail');
Route::post('/ambilriwayatkartustok', [KasirController::class, 'ambilriwayatkartustok'])->name('ambilriwayatkartustok');
Route::get('/transaksi/cetak/{id}', [KasirController::class, 'cetakStruk'])->name('transaksi.cetak');

Route::get('/indexlaporansesikasir', [LaporanController::class, 'indexlaporansesikasir'])->name('indexlaporansesikasir');
Route::get('/indexlaporantransaksipenjualan', [LaporanController::class, 'Index'])->name('indexlaporantransaksipenjualan');
Route::get('/indexlaporanstokpersediaan', [LaporanController::class, 'indexlaporanstokpersediaan'])->name('indexlaporanstokpersediaan');
Route::get('/indexlaporanpo', [LaporanController::class, 'indexlaporanpo'])->name('indexlaporanpo');
Route::get('/indexlaporanstokretur', [LaporanController::class, 'indexlaporanstokretur'])->name('indexlaporanstokretur');
Route::get('/indexlaporanlogkartustok', [LaporanController::class, 'indexlaporanlogkartustok'])->name('indexlaporanlogkartustok');
Route::get('/indexlaporandatapenjualan', [LaporanController::class, 'indexlaporandatapenjualan'])->name('indexlaporandatapenjualan');
Route::post('/ambillaporantransaksipenjualan', [LaporanController::class, 'ambillaporantransaksipenjualan'])->name('ambillaporantransaksipenjualan');
Route::post('/ambildetaillaporantransaksi', [LaporanController::class, 'ambildetaillaporantransaksi'])->name('ambildetaillaporantransaksi');
Route::post('/ambildatapurchaseorder', [LaporanController::class, 'ambildatapurchaseorder'])->name('ambildatapurchaseorder');
Route::post('/ambildatalaporansesikasir', [LaporanController::class, 'ambildatalaporansesikasir'])->name('ambildatalaporansesikasir');
