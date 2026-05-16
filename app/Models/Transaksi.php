<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Transaksi extends Model
{
    use HasUuids;

    protected $primaryKey = 'id_transaksi';
    protected $fillable = ['id_user', 'total_pembayaran', 'metode_bayar', 'status_bayar'];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function details()
    {
        return $this->hasMany(DetailTransaksi::class, 'id_transaksi', 'id_transaksi');
    }
}
