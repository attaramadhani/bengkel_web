<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Barang extends Model
{
    use HasUuids;

    protected $primaryKey = 'id_barang';
    protected $fillable = ['nama_barang', 'harga_jual', 'stok'];
}
