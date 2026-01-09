<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    use HasFactory;

    /**
     * =========================================
     * TABLE CONFIG
     * =========================================
     */
    protected $table = 'pembelian';
    protected $primaryKey = 'id_pembelian';
    public $timestamps = false;

    /**
     * =========================================
     * MASS ASSIGNMENT
     * =========================================
     */
    protected $fillable = [
        'id_toko',
        'id_distributor',
        'no_faktur_supplier',
        'tgl_pembelian',
        'tgl_jatuh_tempo',
        'total_pembelian',
        'status_bayar'
    ];

    /**
     * =========================================
     * RELATIONSHIP
     * =========================================
     */

    // Pembelian milik toko
    public function toko()
    {
        return $this->belongsTo(Toko::class, 'id_toko', 'id_toko');
    }

    // Pembelian ke distributor
    public function distributor()
    {
        return $this->belongsTo(Distributor::class, 'id_distributor', 'id_distributor');
    }

    // Detail item pembelian
    public function detail()
    {
        return $this->hasMany(PembelianDetail::class, 'id_pembelian', 'id_pembelian');
    }

    /**
     * =========================================
     * ACCESSOR & HELPER
     * =========================================
     */

    // Hitung total qty item dalam pembelian
    public function getTotalQtyAttribute()
    {
        return $this->detail()->sum('qty');
    }

    // Cek apakah pembelian masih hutang
    public function isHutang()
    {
        return $this->status_bayar === 'Hutang';
    }
}
