 <div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>Nama Obat</th>
                <th>Satuan</th>
                <th class="text-center">Qty</th>
                <th class="text-right">Harga Satuan</th>
                <th class="text-right">Potongan</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotal = 0; @endphp
            @foreach($hasil_transaksi as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $item['nama_obat'] }}</strong>
                    </td>
                    <td>
                        <span class="badge bg-info text-dark">{{ $item['satuan_pilih'] }}</span>
                    </td>
                    <td class="text-center">{{ $item['qty'] }}</td>
                    <td class="text-right">Rp {{ number_format($item['harga_satuan'], 0, ',', '.') }}</td>
                    <td class="text-right text-danger">
                        {{ $item['diskon'] > 0 ? '- Rp ' . number_format($item['diskon'], 0, ',', '.') : '-' }}
                    </td>
                    <td class="text-right fw-bold">
                        Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                    </td>
                </tr>
                @php $grandTotal += $item['subtotal']; @endphp
            @endforeach
        </tbody>
        <tfoot class="table-light">
            <tr>
                <th colspan="6" class="text-right">Total Pembayaran :</th>
                <th class="text-right text-primary" style="font-size: 1.1rem;">
                    Rp {{ number_format($grandTotal, 0, ',', '.') }}
                </th>
            </tr>
        </tfoot>
    </table>
</div>
 <form class="formhasil" id="formhasil">
     <table class="table table-bordered">
         <thead>
             <tr>
                 <th>Grandtotal</th>
                 <th>Bayar</th>
                 <th></th>
             </tr>
         </thead>
         <tbody>
             <tr>
                 <td width="15%">
                     <input type="text" class="form-control form-control-sm" name="grandtotal"
                         value="{{ $v_gt }}" readonly>
                     <input hidden type="text" id="grandtotalasli" class="form-control form-control-sm"
                         name="grandtotalasli" value="{{ $gt }}" readonly>
                 </td>
                 <td width="15%">
                     <input type="text" name="uangbayar" class="form-control form-control-sm input-mask-uang"
                         value="0" min="1">
                     <input hidden type="text" id="bayarasli" name="bayarasli"
                         class="form-control form-control-sm nilai-asli" value="0" min="1">
                 </td>
                 <td>
                     <button type="button" class="btn btn-primary btn-sm btnbayar" onclick="bayar()"><i class="bi bi-cash-coin"
                             style="margin-right:3px"></i> Bayar</button>
                 </td>
             </tr>
         </tbody>
     </table>
 </form>
 <div class="v_kembalian mt-2">

 </div>
 <script>
     function formatRupiah(angka) {
         let number_string = angka.replace(/[^,\d]/g, '').toString(),
             split = number_string.split(','),
             sisa = split[0].length % 3,
             rupiah = split[0].substr(0, sisa),
             ribuan = split[0].substr(sisa).match(/\d{3}/gi);

         if (ribuan) {
             let separator = sisa ? '.' : '';
             rupiah += separator + ribuan.join('.');
         }

         return split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
     }

     // Event Delegation: Memantau class .input-mask-uang meski baru ditambahkan
     $(document).on('keyup', '.input-mask-uang', function() {
         let nilai = $(this).val();

         // 1. Format tampilan (tambah titik)
         $(this).val(formatRupiah(nilai));

         // 2. Ambil angka bersih (tanpa titik)
         let angkaBersih = nilai.replace(/\./g, '');

         // 3. Masukkan ke hidden input di sebelahnya agar terkirim ke server
         $(this).siblings('.nilai-asli').val(angkaBersih);
     });
 </script>
 <script>
     function bayar() {
         const swalWithBootstrapButtons = Swal.mixin({
             customClass: {
                 confirmButton: "btn btn-success",
                 cancelButton: "btn btn-danger"
             },
             buttonsStyling: true
         });
         swalWithBootstrapButtons.fire({
             title: "Anda yakin ?",
             text: "Pastikan data pembayaran sudah benar !",
             icon: "warning",
             showCancelButton: true,
             confirmButtonText: "Ya, data sudah benar",
             cancelButtonText: "Batal",
             reverseButtons: true
         }).then((result) => {
             if (result.isConfirmed) {
                bayarfinal()
             }
             else if (result.dismiss === Swal.DismissReason.cancel) swalWithBootstrapButtons.fire({
                 title: "Cancelled",
                 text: "Your imaginary file is safe :)",
                 icon: "error"
             });
         });
     }

     function bayarfinal() {
         spinner_on()
         var data = $('.formhasil').serializeArray();
         var data2 = $('.formbarang').serializeArray();
         gt = $('#grandtotalasli').val()
         uang = $('#bayarasli').val()
         id_sesi_kasir = $('#id_sesi_kasir').val()
         $.ajax({
             async: true,
             type: 'post',
             dataType: 'json',
             data: {
                 _token: "{{ csrf_token() }}",
                 data: JSON.stringify(data),
                 data2: JSON.stringify(data2),
                 gt,
                 uang,
                 id_sesi_kasir
             },
             url: '<?= route('prosesbarangfinal') ?>',
             error: function(data) {
                 spinner_off()
                 Swal.fire({
                     icon: 'error',
                     title: 'Ooops....',
                     text: 'Sepertinya ada masalah......',
                     footer: ''
                 })
             },
             success: function(data) {
                 spinner_off()
                 if (data.code == 500) {
                     Swal.fire({
                         icon: 'error',
                         title: 'Oopss...',
                         text: data.message,
                         footer: ''
                     })
                 } else {
                     spinner_off()
                     $('.resetbarang').prop('disabled',true)
                     $('.btnbayar').prop('disabled',true)
                     $('.v_kembalian').html(data.html);
                 }
             }
         });
     }
 </script>
