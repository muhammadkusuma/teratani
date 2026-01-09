<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserTenantMapping extends Model
{
    protected $table = 'user_tenant_mapping';
    protected $primaryKey = 'id_mapping';
    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'id_tenant',
        'role_in_tenant',
        'is_primary'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'id_tenant');
    }
}
