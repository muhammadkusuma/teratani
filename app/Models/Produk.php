<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    protected $table      = 'produk';
    protected $primaryKey = 'id_produk';
    public $timestamps    = false;

    protected $fillable = [
        'id_tenant',
        'id_kategori',
        'id_satuan',
        'sku',
        'barcode',
        'nama_produk',
        'deskripsi',
        'harga_pokok_standar',
        'berat_gram',
        'gambar_produk',
        'is_active',
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori');
    }

    public function satuan()
    {
        return $this->belongsTo(Satuan::class, 'id_satuan');
    }
}
