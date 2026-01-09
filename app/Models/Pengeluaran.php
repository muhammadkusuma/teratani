<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengeluaran extends Model
{
    use HasFactory;

    /**
     * =========================================
     * TABLE CONFIG
     * =========================================
     */
    protected $table      = 'pengeluaran';
    protected $primaryKey = 'id_pengeluaran';
    public $timestamps    = false;

    /**
     * =========================================
     * MASS ASSIGNMENT
     * =========================================
     */
    protected $fillable = [
        'id_toko',
        'id_user',
        'tgl_pengeluaran',
        'kategori_biaya',
        'nominal',
        'keterangan',
        'bukti_foto',
    ];

    /**
     * =========================================
     * RELATIONSHIP
     * =========================================
     */

    // Pengeluaran milik toko
    public function toko()
    {
        return $this->belongsTo(Toko::class, 'id_toko', 'id_toko');
    }

    // Pengeluaran dicatat oleh user
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    /**
     * =========================================
     * ACCESSOR & HELPER
     * =========================================
     */

    // Format nominal rupiah (opsional)
    public function getNominalRupiahAttribute()
    {
        return 'Rp ' . number_format($this->nominal, 0, ',', '.');
    }
}
