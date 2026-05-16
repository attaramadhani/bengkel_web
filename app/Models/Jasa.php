<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Jasa extends Model
{
    use HasUuids;

    protected $primaryKey = 'id_jasa';
    protected $fillable = ['nama_jasa', 'harga_jasa'];
}
