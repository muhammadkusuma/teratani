<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    protected $table      = 'penjualan';
    protected $primaryKey = 'id_penjualan';
    public $timestamps    = false;

    protected $fillable = [
        'id_toko',
        'id_user',
        'id_pelanggan',
        'id_sales',
        'no_faktur',
        'tgl_transaksi',
        'tgl_jatuh_tempo',
        'total_bruto',
        'diskon_nota',
        'pajak_ppn',
        'biaya_lain',
        'total_netto',
        'jumlah_bayar',
        'kembalian',
        'metode_bayar',
        'status_transaksi',
        'status_bayar',
        'catatan',
    ];

    public function detail()
    {
        return $this->hasMany(PenjualanDetail::class, 'id_penjualan');
    }
}
