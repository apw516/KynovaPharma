<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class model_sediaan_barang extends Model
{
    use HasFactory;
    const UPDATED_AT = null;
    const CREATED_AT = null;
    protected $table = 'mt_sediaan_obat';
    protected $guarded = [];
    public function supplier()
    {
        // foreign_key: kode_supplier, owner_key: kode_supplier (di tabel supplier)
        return $this->belongsTo(Supplier::class, 'kode_supplier', 'kode_supplier');
    }
}
