<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembayaranPiutang extends Model
{
    /**
     * =========================================
     * KONFIGURASI DASAR
     * =========================================
     */
    protected $table      = 'pembayaran_piutang';
    protected $primaryKey = 'id_bayar_piutang';
    public $timestamps    = false;

    protected $fillable = [
        'id_piutang',
        'tgl_bayar',
        'jumlah_bayar',
        'metode_bayar',
        'keterangan',
        'id_user',
    ];

    /**
     * =========================================
     * RELASI
     * =========================================
     */

    /**
     * Relasi ke kartu piutang
     */
    public function kartuPiutang()
    {
        return $this->belongsTo(KartuPiutang::class, 'id_piutang');
    }

    /**
     * User penerima pembayaran
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    /**
     * =========================================
     * BUSINESS LOGIC / SCOPE
     * =========================================
     */

    /**
     * Scope filter pembayaran hari ini
     */
    public function scopeHariIni($query)
    {
        return $query->whereDate('tgl_bayar', now()->toDateString());
    }

    /**
     * Scope filter metode bayar
     */
    public function scopeMetode($query, $metode)
    {
        return $query->where('metode_bayar', $metode);
    }

    /**
     * Total pembayaran per piutang
     */
    public static function totalByPiutang($idPiutang)
    {
        return self::where('id_piutang', $idPiutang)
            ->sum('jumlah_bayar');
    }
}
