<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    protected $table      = 'kategori';
    protected $primaryKey = 'id_kategori';
    public $timestamps    = false;

    protected $fillable = [
        'id_tenant',
        'nama_kategori',
        'deskripsi',
    ];

    public function produk()
    {
        return $this->hasMany(Produk::class, 'id_kategori');
    }
}
