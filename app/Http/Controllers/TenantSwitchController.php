<?php
namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\Toko;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class TenantSwitchController extends Controller
{
    /**
     * =========================================
     * LIST TENANT YANG DIMILIKI USER
     * =========================================
     */
    public function listTenant()
    {
        if (! Session::get('auth')) {
            return redirect()->route('login.form');
        }

        $userId = Session::get('user_id');

        $tenants = DB::table('user_tenant_mapping')
            ->join('tenants', 'tenants.id_tenant', '=', 'user_tenant_mapping.id_tenant')
            ->where('user_tenant_mapping.id_user', $userId)
            ->select(
                'tenants.id_tenant',
                'tenants.nama_bisnis',
                'tenants.status_langganan',
                'user_tenant_mapping.role_in_tenant'
            )
            ->orderByDesc('user_tenant_mapping.is_primary')
            ->get();

        return view('tenant.select', compact('tenants'));
    }

    /**
     * =========================================
     * SWITCH TENANT
     * =========================================
     */
    public function switchTenant(Request $request, $id_tenant)
    {
        if (! Session::get('auth')) {
            return redirect()->route('login.form');
        }

        $userId = Session::get('user_id');

        /**
         * ======================================
         * VALIDASI AKSES USER KE TENANT
         * ======================================
         */
        $mapping = DB::table('user_tenant_mapping')
            ->where('id_user', $userId)
            ->where('id_tenant', $id_tenant)
            ->first();

        if (! $mapping) {
            abort(403, 'Anda tidak memiliki akses ke tenant ini');
        }

        /**
         * ======================================
         * VALIDASI STATUS TENANT
         * ======================================
         */
        $tenant = Tenant::findOrFail($id_tenant);

        if ($tenant->status_langganan !== 'Aktif') {
            return redirect()->route('tenant.select')
                ->withErrors(['tenant' => 'Tenant tidak aktif / expired']);
        }

        /**
         * ======================================
         * SET SESSION TENANT
         * ======================================
         */
        Session::put('tenant_id', $tenant->id_tenant);
        Session::put('tenant_name', $tenant->nama_bisnis);
        Session::put('tenant_role', $mapping->role_in_tenant);

        /**
         * ======================================
         * PILIH TOKO DEFAULT
         * ======================================
         */
        $toko = Toko::where('id_tenant', $tenant->id_tenant)
            ->whereIn('id_toko', function ($query) use ($userId) {
                $query->select('id_toko')
                    ->from('user_toko_access')
                    ->where('id_user', $userId);
            })
            ->orderByDesc('is_pusat')
            ->first();

        if ($toko) {
            Session::put('toko_id', $toko->id_toko);
            Session::put('toko_name', $toko->nama_toko);
        } else {
            Session::forget('toko_id');
            Session::forget('toko_name');
        }

        return redirect()->route('dashboard');
    }
}
