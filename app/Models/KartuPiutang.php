<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KartuPiutang extends Model
{
    /**
     * =========================================
     * KONFIGURASI DASAR
     * =========================================
     */
    protected $table      = 'kartu_piutang';
    protected $primaryKey = 'id_piutang';
    public $timestamps    = false;

    protected $fillable = [
        'id_toko',
        'id_penjualan',
        'id_pelanggan',
        'tgl_jatuh_tempo',
        'total_piutang',
        'sudah_dibayar',
        'sisa_piutang',
        'status',
    ];

    /**
     * =========================================
     * RELASI
     * =========================================
     */

    /**
     * Relasi ke Penjualan
     */
    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class, 'id_penjualan');
    }

    /**
     * Relasi ke Pelanggan
     */
    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan');
    }

    /**
     * Relasi ke Toko
     */
    public function toko()
    {
        return $this->belongsTo(Toko::class, 'id_toko');
    }

    /**
     * =========================================
     * BUSINESS LOGIC
     * =========================================
     */

    /**
     * Update status piutang otomatis
     */
    public function refreshStatus()
    {
        if ($this->sisa_piutang <= 0) {
            $this->status       = 'Lunas';
            $this->sisa_piutang = 0;
        } else {
            $this->status = 'Belum Lunas';
        }

        $this->save();
    }

    /**
     * Tambah pembayaran (dipakai PembayaranPiutangController)
     */
    public function tambahPembayaran($jumlah)
    {
        $this->sudah_dibayar += $jumlah;
        $this->sisa_piutang -= $jumlah;

        $this->refreshStatus();
    }

    /**
     * Scope piutang belum lunas
     */
    public function scopeBelumLunas($query)
    {
        return $query->where('status', 'Belum Lunas');
    }
}
