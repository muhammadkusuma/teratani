<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table      = 'users';
    protected $primaryKey = 'id_user';
    public $timestamps    = false;

    protected $fillable = [
        'username',
        'password',
        'nama_lengkap',
        'email',
        'no_hp',
        'is_superadmin',
        'is_active',
    ];

    protected $hidden = [
        'password',
    ];

    public function tenants()
    {
        return $this->belongsToMany(
            Tenant::class,
            'user_tenant_mapping',
            'id_user',
            'id_tenant'
        )->withPivot(['role_in_tenant', 'is_primary']);
    }

    public function tokoAkses()
    {
        return $this->belongsToMany(
            Toko::class,
            'user_toko_access',
            'id_user',
            'id_toko'
        );
    }
}
