<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogStok extends Model
{
    /**
     * =========================================
     * KONFIGURASI DASAR
     * =========================================
     */
    protected $table = 'log_stok';
    protected $primaryKey = 'id_log';
    public $timestamps = false;

    protected $fillable = [
        'id_toko',
        'id_produk',
        'id_user',
        'jenis_transaksi',
        'no_referensi',
        'qty_masuk',
        'qty_keluar',
        'stok_akhir',
        'keterangan',
        'created_at'
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
     * Relasi ke User (pelaku perubahan stok)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    /**
     * =========================================
     * HELPER / BUSINESS LOGIC
     * =========================================
     */

    /**
     * Simpan log stok masuk
     */
    public static function logMasuk(
        $idToko,
        $idProduk,
        $idUser,
        $qty,
        $stokAkhir,
        $jenisTransaksi,
        $noReferensi = null,
        $keterangan = null
    ) {
        return self::create([
            'id_toko'         => $idToko,
            'id_produk'       => $idProduk,
            'id_user'         => $idUser,
            'jenis_transaksi' => $jenisTransaksi,
            'no_referensi'    => $noReferensi,
            'qty_masuk'       => $qty,
            'qty_keluar'      => 0,
            'stok_akhir'      => $stokAkhir,
            'keterangan'      => $keterangan
        ]);
    }

    /**
     * Simpan log stok keluar
     */
    public static function logKeluar(
        $idToko,
        $idProduk,
        $idUser,
        $qty,
        $stokAkhir,
        $jenisTransaksi,
        $noReferensi = null,
        $keterangan = null
    ) {
        return self::create([
            'id_toko'         => $idToko,
            'id_produk'       => $idProduk,
            'id_user'         => $idUser,
            'jenis_transaksi' => $jenisTransaksi,
            'no_referensi'    => $noReferensi,
            'qty_masuk'       => 0,
            'qty_keluar'      => $qty,
            'stok_akhir'      => $stokAkhir,
            'keterangan'      => $keterangan
        ]);
    }

    /**
     * Simpan log stok opname (penyesuaian)
     */
    public static function logOpname(
        $idToko,
        $idProduk,
        $idUser,
        $stokSebelum,
        $stokSesudah,
        $keterangan = null
    ) {
        $qtyMasuk = 0;
        $qtyKeluar = 0;

        if ($stokSesudah > $stokSebelum) {
            $qtyMasuk = $stokSesudah - $stokSebelum;
        } elseif ($stokSesudah < $stokSebelum) {
            $qtyKeluar = $stokSebelum - $stokSesudah;
        }

        return self::create([
            'id_toko'         => $idToko,
            'id_produk'       => $idProduk,
            'id_user'         => $idUser,
            'jenis_transaksi' => 'Opname',
            'qty_masuk'       => $qtyMasuk,
            'qty_keluar'      => $qtyKeluar,
            'stok_akhir'      => $stokSesudah,
            'keterangan'      => $keterangan
        ]);
    }

    /**
     * Ambil histori stok per produk per toko
     */
    public static function historiProduk($idToko, $idProduk)
    {
        return self::where('id_toko', $idToko)
            ->where('id_produk', $idProduk)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
