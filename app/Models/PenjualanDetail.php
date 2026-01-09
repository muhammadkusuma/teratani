<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenjualanDetail extends Model
{
    protected $table      = 'penjualan_detail';
    protected $primaryKey = 'id_detail';
    public $timestamps    = false;

    protected $fillable = [
        'id_penjualan',
        'id_produk',
        'qty',
        'satuan_saat_jual',
        'harga_modal_saat_jual',
        'harga_jual_satuan',
        'diskon_item',
        'subtotal',
    ];
}
