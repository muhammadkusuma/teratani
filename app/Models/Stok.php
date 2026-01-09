<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stok extends Model
{
    /**
     * =========================================
     * KONFIGURASI DASAR
     * =========================================
     */
    protected $table      = 'stok';
    protected $primaryKey = 'id_stok';
    public $timestamps    = false;

    protected $fillable = [
        'id_toko',
        'id_produk',
        'stok',
    ];

    /**
     * =========================================
     * RELASI
     * =========================================
     */

    /**
     * Relasi ke Toko
     */
    public function toko()
    {
        return $this->belongsTo(Toko::class, 'id_toko');
    }

    /**
     * Relasi ke Produk
     */
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk');
    }

    /**
     * =========================================
     * BUSINESS LOGIC
     * =========================================
     */

    /**
     * Ambil stok produk di toko tertentu
     */
    public static function getStok($idToko, $idProduk)
    {
        return self::where('id_toko', $idToko)
            ->where('id_produk', $idProduk)
            ->value('stok') ?? 0;
    }

    /**
     * Tambah stok (AMAN TRANSAKSI)
     */
    public static function tambahStok($idToko, $idProduk, $qty)
    {
        $stok = self::where('id_toko', $idToko)
            ->where('id_produk', $idProduk)
            ->lockForUpdate()
            ->first();

        if (! $stok) {
            $stok = self::create([
                'id_toko'   => $idToko,
                'id_produk' => $idProduk,
                'stok'      => 0,
            ]);
        }

        $stok->stok += $qty;
        $stok->save();

        return $stok;
    }

    /**
     * Kurangi stok (VALIDASI MINUS)
     */
    public static function kurangiStok($idToko, $idProduk, $qty)
    {
        $stok = self::where('id_toko', $idToko)
            ->where('id_produk', $idProduk)
            ->lockForUpdate()
            ->firstOrFail();

        if ($stok->stok < $qty) {
            throw new \Exception('Stok tidak mencukupi');
        }

        $stok->stok -= $qty;
        $stok->save();

        return $stok;
    }

    /**
     * Set stok langsung (UNTUK OPNAME)
     */
    public static function setStok($idToko, $idProduk, $qty)
    {
        $stok = self::where('id_toko', $idToko)
            ->where('id_produk', $idProduk)
            ->lockForUpdate()
            ->first();

        if (! $stok) {
            $stok = self::create([
                'id_toko'   => $idToko,
                'id_produk' => $idProduk,
                'stok'      => 0,
            ]);
        }

        $stok->stok = $qty;
        $stok->save();

        return $stok;
    }
}
