<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    /**
     * =========================================
     * KONFIGURASI DASAR
     * =========================================
     */
    protected $table      = 'pelanggan';
    protected $primaryKey = 'id_pelanggan';
    public $timestamps    = false;

    protected $fillable = [
        'id_tenant',
        'kode_pelanggan',
        'nama_pelanggan',
        'wilayah',
        'no_hp',
        'alamat',
        'limit_piutang',
        'top_hari',
    ];

    /**
     * =========================================
     * RELASI
     * =========================================
     */

    /**
     * Tenant pemilik pelanggan
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'id_tenant');
    }

    /**
     * Penjualan oleh pelanggan ini
     */
    public function penjualan()
    {
        return $this->hasMany(Penjualan::class, 'id_pelanggan');
    }

    /**
     * Kartu piutang pelanggan
     */
    public function kartuPiutang()
    {
        return $this->hasMany(KartuPiutang::class, 'id_pelanggan');
    }

    /**
     * =========================================
     * BUSINESS LOGIC
     * =========================================
     */

    /**
     * Hitung total piutang aktif pelanggan
     */
    public function totalPiutangAktif()
    {
        return $this->kartuPiutang()
            ->where('status', 'Belum Lunas')
            ->sum('sisa_piutang');
    }

    /**
     * Cek apakah pelanggan masih boleh kredit
     */
    public function masihBolehKredit($nilaiTransaksi)
    {
        $totalPiutang = $this->totalPiutangAktif();

        return ($totalPiutang + $nilaiTransaksi) <= $this->limit_piutang;
    }

    /**
     * Generate kode pelanggan otomatis (per tenant)
     */
    public static function generateKode($idTenant)
    {
        $last = self::where('id_tenant', $idTenant)
            ->orderBy('id_pelanggan', 'desc')
            ->first();

        $next = $last ? ((int) substr($last->kode_pelanggan, -4) + 1) : 1;

        return 'PLG-' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}
