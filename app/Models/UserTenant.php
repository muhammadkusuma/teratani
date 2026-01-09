<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTenant extends Model
{
    use HasFactory;

    protected $table      = 'user_tenant_mapping';
    protected $primaryKey = 'id_mapping';

    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'id_tenant',
        'role_in_tenant',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    /**
     * =========================
     * RELATIONS
     * =========================
     */

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'id_tenant', 'id_tenant');
    }

    /**
     * =========================
     * SCOPES
     * =========================
     */

    public function scopeByUser($query, $userId)
    {
        return $query->where('id_user', $userId);
    }

    public function scopeByTenant($query, $tenantId)
    {
        return $query->where('id_tenant', $tenantId);
    }

    /**
     * =========================
     * HELPERS
     * =========================
     */

    public static function userHasAccess($userId, $tenantId): bool
    {
        return self::where('id_user', $userId)
            ->where('id_tenant', $tenantId)
            ->exists();
    }

    public static function getUserRoleInTenant($userId, $tenantId): ?string
    {
        return self::where('id_user', $userId)
            ->where('id_tenant', $tenantId)
            ->value('role_in_tenant');
    }

    public static function getPrimaryTenant($userId)
    {
        return self::where('id_user', $userId)
            ->where('is_primary', true)
            ->first();
    }
}
