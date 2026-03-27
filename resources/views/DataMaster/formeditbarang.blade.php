 <form class="formeditmasterbarang" id="formeditmasterbarang">
     <div class="row">
         <div class="col-md-4">
             <div class="mb-3">
                 <label for="exampleInputEmail1" class="form-label">Nama Barang</label>
                 <input type="text" placeholder="Masukan nama barang ..." class="form-control" id="namabarang"
                     name="namabarang" aria-describedby="emailHelp" value={{ $data->nama_obat}}>
                 <input readonly type="text" placeholder="Masukan nama barang ..." class="form-control" id="kode_barang"
                     name="kode_barang" aria-describedby="emailHelp" value={{ $data->kode_barang}}>
                 <input hidden type="text" placeholder="Masukan nama barang ..." class="form-control" id="idbarang"
                     name="idbarang" aria-describedby="emailHelp" value={{ $data->id}}>
             </div>
         </div>
         <div class="col-md-4">
             <div class="mb-3">
                 <label for="exampleInputEmail1" class="form-label">Merk Dagang</label>
                 <input type="text" placeholder="Masukan nama merk dagang ..." class="form-control" id="merkdagang"
                     name="merkdagang" aria-describedby="emailHelp" value="{{ $data->nama_dagang}}">
             </div>
         </div>
         <div class="col-md-4">
             <div class="mb-3">
                 <label for="exampleInputEmail1" class="form-label">Produsen</label>
                 <input type="text" placeholder="Masukan nama produsen ..." class="form-control" id="produsen"
                     name="produsen" aria-describedby="emailHelp" value="{{ $data->produsen}}">
             </div>
         </div>
     </div>
     <div class="row">
         <div class="col-md-3">
             <div class="mb-3">
                 <label for="exampleInputEmail1" class="form-label">Satuan Besar</label>
                 <select class="form-select" aria-label="Default select example" id="satuanbesar" name="satuanbesar">
                     <option selected>- Silahkan Pilih - </option>
                     @foreach ($satuan as $s)
                         <option value="{{ $s->kode_satuan }}" @if($data->satuan_besar == $s->kode_satuan) selected @endif>{{ $s->nama_satuan }}</option>
                     @endforeach
                 </select>
             </div>
         </div>
         <div class="col-md-3">
             <div class="mb-3">
                 <label for="exampleInputEmail1" class="form-label">Satuan Sedang</label>
                 <select class="form-select" aria-label="Default select example" id="satuansedang" name="satuansedang">
                     <option selected>- Silahkan Pilih - </option>
                     @foreach ($satuan as $s)
                         <option value="{{ $s->kode_satuan }}" @if($data->satuan_sedang == $s->kode_satuan) selected @endif>{{ $s->nama_satuan }}</option>
                     @endforeach
                 </select>
             </div>
         </div>
         <div class="col-md-3">
             <div class="mb-3">
                 <label for="exampleInputEmail1" class="form-label">Satuan Kecil</label>
                 <select class="form-select" aria-label="Default select example" id="satuankecil" name="satuankecil">
                     <option selected>- Silahkan Pilih - </option>
                     @foreach ($satuan as $s)
                         <option value="{{ $s->kode_satuan }}" @if($data->satuan_kecil == $s->kode_satuan) selected @endif>{{ $s->nama_satuan }}</option>
                     @endforeach
                 </select>
             </div>
         </div>
         <div class="col-md-3">
             <div class="mb-3">
                 <label for="exampleInputEmail1" class="form-label">Sediaan</label>
                 <select class="form-select" aria-label="Default select example" id="sediaan" name="sediaan">
                     <option selected>- Silahkan Pilih - </option>
                     @foreach ($satuan as $s)
                         <option value="{{ $s->kode_satuan }}" @if($data->sediaan == $s->kode_satuan) selected @endif>{{ $s->nama_satuan }}</option>
                     @endforeach
                 </select>
             </div>
         </div>
     </div>
     <div class="row">
         <div class="col-md-5">
             <div class="mb-3">
                 <label for="exampleInputEmail1" class="form-label">Rasio Satuan Besar Ke Satuan
                     Sedang</label>
                 <input type="text" placeholder="Masukan rasio sedang ..." class="form-control"
                     id="rasiosatuansedang" name="rasiosatuansedang" aria-describedby="emailHelp" value="{{ $data->rasio_sedang}}">
                 <div id="emailHelp" class="form-text">Contoh: 10 (1 Box isi 10 Strip)
                 </div>
             </div>
         </div>
         <div class="col-md-5">
             <div class="mb-3">
                 <label for="exampleInputEmail1" class="form-label">Rasio Satuan Besar Ke Satuan
                     Kecil</label>
                 <input type="text" placeholder="Masukan rasio kecil ..." class="form-control" id="rasiosatuankecil"
                     name="rasiosatuankecil" aria-describedby="emailHelp" value="{{ $data->rasio_kecil}}">
                 <div id="emailHelp" class="form-text">Contoh: 10 (1 Strip isi 10 Tablet)
                 </div>
             </div>
         </div>
     </div>
     <div class="mb-3">
         <label for="exampleInputPassword1" class="form-label">Aturan Pakai</label>
         <textarea rows="5" type="text" class="form-control" id="aturanpakai" name="aturanpakai"
             placeholder="Masukan aturan pakai barang ...">{{ $data->aturan_pakai}}</textarea>
     </div>
 </form>
