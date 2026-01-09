<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Toko extends Model
{
    protected $table = 'toko';
    protected $primaryKey = 'id_toko';
    public $timestamps = false;

    protected $fillable = [
        'id_tenant',
        'kode_toko',
        'nama_toko',
        'alamat',
        'kota',
        'no_telp',
        'is_pusat'
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'id_tenant');
    }

    public function stok()
    {
        return $this->hasMany(StokToko::class, 'id_toko');
    }
}
