<?php
namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\User;
use App\Models\UserTenantMapping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class UserTenantController extends Controller
{
    /**
     * =========================================
     * LIST USER DALAM TENANT
     * =========================================
     */
    public function index()
    {
        $this->validateContext();
        $this->authorizeOwnerOrManager();

        $tenantId = Session::get('tenant_id');

        $users = UserTenantMapping::with('user')
            ->where('id_tenant', $tenantId)
            ->orderBy('role_in_tenant')
            ->get();

        return view('user_tenant.index', compact('users'));
    }

    /**
     * =========================================
     * FORM TAMBAH USER KE TENANT
     * =========================================
     */
    public function create()
    {
        $this->validateContext();
        $this->authorizeOwnerOrManager();

        return view('user_tenant.create');
    }

    /**
     * =========================================
     * SIMPAN USER KE TENANT
     * =========================================
     */
    public function store(Request $request)
    {
        $this->validateContext();
        $this->authorizeOwnerOrManager();

        $request->validate([
            'username'       => 'required|string',
            'role_in_tenant' => 'required|in:OWNER,MANAGER,ADMIN,KASIR',
        ]);

        $tenantId = Session::get('tenant_id');

        $user = User::where('username', $request->username)->first();

        if (! $user) {
            return redirect()->back()
                ->withErrors(['username' => 'User tidak ditemukan']);
        }

        /**
         * ======================================
         * CEK DUPLIKASI AKSES
         * ======================================
         */
        $exists = UserTenantMapping::where('id_user', $user->id_user)
            ->where('id_tenant', $tenantId)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withErrors(['username' => 'User sudah memiliki akses ke tenant ini']);
        }

        DB::beginTransaction();
        try {
            UserTenantMapping::create([
                'id_user'        => $user->id_user,
                'id_tenant'      => $tenantId,
                'role_in_tenant' => $request->role_in_tenant,
                'is_primary'     => false,
            ]);

            DB::commit();
            return redirect()->route('user-tenant.index')
                ->with('success', 'User berhasil ditambahkan ke tenant');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * =========================================
     * UPDATE ROLE USER DALAM TENANT
     * =========================================
     */
    public function update(Request $request, $id_mapping)
    {
        $this->validateContext();
        $this->authorizeOwner();

        $request->validate([
            'role_in_tenant' => 'required|in:OWNER,MANAGER,ADMIN,KASIR',
        ]);

        $mapping = UserTenantMapping::where('id_mapping', $id_mapping)
            ->where('id_tenant', Session::get('tenant_id'))
            ->first();

        if (! $mapping) {
            abort(403, 'Akses tidak valid');
        }

        /**
         * ======================================
         * OWNER TIDAK BOLEH TURUN JIKA SATU-SATU
         * ======================================
         */
        if (
            $mapping->role_in_tenant === 'OWNER' &&
            $request->role_in_tenant !== 'OWNER'
        ) {
            $ownerCount = UserTenantMapping::where('id_tenant', Session::get('tenant_id'))
                ->where('role_in_tenant', 'OWNER')
                ->count();

            if ($ownerCount <= 1) {
                return redirect()->back()
                    ->withErrors(['error' => 'Minimal harus ada 1 OWNER']);
            }
        }

        DB::beginTransaction();
        try {
            $mapping->update([
                'role_in_tenant' => $request->role_in_tenant,
            ]);

            DB::commit();
            return redirect()->back()
                ->with('success', 'Role user berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * =========================================
     * CABUT AKSES USER DARI TENANT
     * =========================================
     */
    public function destroy($id_mapping)
    {
        $this->validateContext();
        $this->authorizeOwner();

        $mapping = UserTenantMapping::where('id_mapping', $id_mapping)
            ->where('id_tenant', Session::get('tenant_id'))
            ->first();

        if (! $mapping) {
            abort(403, 'Akses tidak valid');
        }

        /**
         * ======================================
         * OWNER TERAKHIR TIDAK BOLEH DIHAPUS
         * ======================================
         */
        if ($mapping->role_in_tenant === 'OWNER') {
            $ownerCount = UserTenantMapping::where('id_tenant', Session::get('tenant_id'))
                ->where('role_in_tenant', 'OWNER')
                ->count();

            if ($ownerCount <= 1) {
                return redirect()->back()
                    ->withErrors(['error' => 'OWNER terakhir tidak bisa dihapus']);
            }
        }

        DB::beginTransaction();
        try {
            $mapping->delete();

            DB::commit();
            return redirect()->route('user-tenant.index')
                ->with('success', 'Akses user berhasil dicabut');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * =========================================
     * HELPER: VALIDASI AUTH + TENANT
     * =========================================
     */
    private function validateContext()
    {
        if (! Session::get('auth')) {
            abort(401, 'Silakan login terlebih dahulu');
        }

        if (! Session::get('tenant_id')) {
            abort(403, 'Tenant belum dipilih');
        }
    }

    /**
     * =========================================
     * HELPER: OWNER / MANAGER
     * =========================================
     */
    private function authorizeOwnerOrManager()
    {
        if (! in_array(Session::get('role_in_tenant'), ['OWNER', 'MANAGER'])) {
            abort(403, 'Akses ditolak');
        }
    }

    /**
     * =========================================
     * HELPER: OWNER ONLY
     * =========================================
     */
    private function authorizeOwner()
    {
        if (Session::get('role_in_tenant') !== 'OWNER') {
            abort(403, 'Hanya OWNER yang diizinkan');
        }
    }
}
