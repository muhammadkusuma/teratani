<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembelianDetail extends Model
{
    use HasFactory;

    /**
     * =========================================
     * TABLE CONFIG
     * =========================================
     */
    protected $table      = 'pembelian_detail';
    protected $primaryKey = 'id_detail_beli';
    public $timestamps    = false;

    /**
     * =========================================
     * MASS ASSIGNMENT
     * =========================================
     */
    protected $fillable = [
        'id_pembelian',
        'id_produk',
        'qty',
        'harga_beli_satuan',
        'subtotal',
    ];

    /**
     * =========================================
     * RELATIONSHIP
     * =========================================
     */

    // Detail milik pembelian
    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class, 'id_pembelian', 'id_pembelian');
    }

    // Detail ke produk
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }

    /**
     * =========================================
     * ACCESSOR & HELPER
     * =========================================
     */

    // Hitung subtotal otomatis
    public function getSubtotalAttribute()
    {
        return $this->qty * $this->harga_beli_satuan;
    }
}
