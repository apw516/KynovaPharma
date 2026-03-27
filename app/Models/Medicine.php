<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    use HasFactory;
    const UPDATED_AT = null;
    const CREATED_AT = null;
    protected $table = 'mt_barang';
    protected $guarded = [];
    public function brand()
    {
        return $this->belongsTo(Brand::class, 'merk_id');
    }
}
