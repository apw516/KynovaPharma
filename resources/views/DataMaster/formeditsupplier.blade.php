 <form class="formeditsupplier" id="formeditsupplier">
     <div class="mb-3">
         <label for="exampleInputEmail1" class="form-label">Nama Supplier</label>
         <input type="texts" class="form-control" id="nama_supplier" name="nama_supplier"
             placeholder="Masukan nama supplier ..." aria-describedby="emailHelp" value="{{$data->nama_supplier}}">
         <input readonly type="texts" class="form-control" id="kode_supplier" name="kode_supplier"
             placeholder="Masukan nama supplier ..." aria-describedby="emailHelp" value="{{$data->kode_supplier}}">
         <input hidden type="texts" class="form-control" id="id_supplier" name="id_supplier"
             placeholder="Masukan nama supplier ..." aria-describedby="emailHelp" value="{{$data->id}}">
     </div>
     <div class="mb-3">
         <label for="exampleInputPassword1" class="form-label">Telepon</label>
         <input type="text" class="form-control" id="telepon" name="telepon"
             placeholder="Masukan nomor telepon supplier ..." value="{{$data->telepon}}">
     </div>
     <div class="mb-3">
         <label for="exampleInputPassword1" class="form-label">Email</label>
         <input type="text" class="form-control" id="email" name="email"
             placeholder="Masukan email supplier ..." value="{{ $data->email}}">
     </div>
     <div class="mb-3">
         <label for="exampleInputPassword1" class="form-label">Alamat</label>
         <textarea type="text" class="form-control" id="alamat" name="alamat" placeholder="Masukan alamat supplier ...">{{ $data->alamat }}</textarea>
     </div>
 </form>
