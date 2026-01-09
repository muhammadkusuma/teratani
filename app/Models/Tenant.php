<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $table      = 'tenants';
    protected $primaryKey = 'id_tenant';
    public $timestamps    = false;

    protected $fillable = [
        'nama_bisnis',
        'kode_unik_tenant',
        'alamat_kantor_pusat',
        'owner_contact',
        'paket_layanan',
        'max_toko',
        'status_langganan',
        'tgl_expired',
    ];

    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'user_tenant_mapping',
            'id_tenant',
            'id_user'
        )->withPivot(['role_in_tenant', 'is_primary']);
    }

    public function toko()
    {
        return $this->hasMany(Toko::class, 'id_tenant');
    }
}
