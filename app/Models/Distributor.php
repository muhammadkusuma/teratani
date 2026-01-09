<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Distributor extends Model
{
    use HasFactory;

    /**
     * =========================================
     * TABLE CONFIG
     * =========================================
     */
    protected $table = 'distributor';
    protected $primaryKey = 'id_distributor';
    public $timestamps = false;

    /**
     * =========================================
     * MASS ASSIGNMENT
     * =========================================
     */
    protected $fillable = [
        'id_tenant',
        'nama_distributor',
        'nama_kontak',
        'no_telp',
        'alamat',
        'hutang_awal'
    ];

    /**
     * =========================================
     * RELATIONSHIP
     * =========================================
     */

    // Distributor milik tenant
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'id_tenant', 'id_tenant');
    }

    // Distributor memiliki banyak pembelian
    public function pembelian()
    {
        return $this->hasMany(Pembelian::class, 'id_distributor', 'id_distributor');
    }

    /**
     * =========================================
     * ACCESSOR & HELPER
     * =========================================
     */

    // Total hutang distributor (dari pembelian)
    public function getTotalHutangAttribute()
    {
        return $this->pembelian()
            ->where('status_bayar', 'Hutang')
            ->sum('total_pembelian');
    }

    // Total pembelian dari distributor
    public function getTotalPembelianAttribute()
    {
        return $this->pembelian()
            ->sum('total_pembelian');
    }
}
