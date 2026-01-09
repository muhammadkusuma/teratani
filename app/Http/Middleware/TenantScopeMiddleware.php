<?php
namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Models\UserTenant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TenantScopeMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (! Auth::check()) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = Auth::user();

        /**
         * ======================================
         * VALIDASI TENANT AKTIF DI SESSION
         * ======================================
         */
        $tenantId = session('tenant_id');

        if (! $tenantId) {
            return response()->json([
                'message' => 'Tenant belum dipilih',
            ], 403);
        }

        /**
         * ======================================
         * VALIDASI AKSES USER KE TENANT
         * ======================================
         */
        $hasAccess = UserTenant::where('id_user', $user->id_user)
            ->where('id_tenant', $tenantId)
            ->exists();

        if (! $hasAccess) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses ke tenant ini',
            ], 403);
        }

        /**
         * ======================================
         * SET TENANT GLOBAL (REQUEST)
         * ======================================
         */
        app()->instance('tenant_id', $tenantId);

        /**
         * OPTIONAL:
         * - attach ke request agar mudah dipakai controller
         */
        $request->attributes->set('tenant_id', $tenantId);

        return $next($request);
    }
}
