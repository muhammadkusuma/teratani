<?php
namespace App\Http\Controllers;

use App\Models\Toko;
use App\Models\User;
use App\Models\UserTenantMapping;
use App\Models\UserTokoAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class UserTokoAccessController extends Controller
{
    /**
     * =========================================
     * LIST USER & AKSES TOKO
     * =========================================
     */
    public function index()
    {
        $this->validateContext();
        $this->authorizeAdmin();

        $tenantId = Session::get('tenant_id');

        $users = UserTenantMapping::with(['user', 'user.userTokoAccess.toko'])
            ->where('id_tenant', $tenantId)
            ->get();

        $toko = Toko::where('id_tenant', $tenantId)
            ->where('is_active', true)
            ->orderBy('nama_toko')
            ->get();

        return view('user_toko.index', compact('users', 'toko'));
    }

    /**
     * =========================================
     * SIMPAN AKSES USER KE TOKO
     * =========================================
     */
    public function store(Request $request)
    {
        $this->validateContext();
        $this->authorizeAdmin();

        $request->validate([
            'id_user' => 'required|integer',
            'id_toko' => 'required|integer',
        ]);

        $tenantId = Session::get('tenant_id');

        /**
         * ======================================
         * VALIDASI USER MASIH DI TENANT INI
         * ======================================
         */
        $mapping = UserTenantMapping::where('id_user', $request->id_user)
            ->where('id_tenant', $tenantId)
            ->first();

        if (! $mapping) {
            abort(403, 'User tidak terdaftar di tenant ini');
        }

        /**
         * ======================================
         * VALIDASI TOKO MILIK TENANT
         * ======================================
         */
        $toko = Toko::where('id_toko', $request->id_toko)
            ->where('id_tenant', $tenantId)
            ->first();

        if (! $toko) {
            abort(403, 'Toko tidak valid');
        }

        /**
         * ======================================
         * CEK DUPLIKASI AKSES
         * ======================================
         */
        $exists = UserTokoAccess::where('id_user', $request->id_user)
            ->where('id_toko', $request->id_toko)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withErrors(['error' => 'User sudah memiliki akses ke toko ini']);
        }

        DB::beginTransaction();
        try {
            UserTokoAccess::create([
                'id_user' => $request->id_user,
                'id_toko' => $request->id_toko,
            ]);

            DB::commit();
            return redirect()->back()
                ->with('success', 'Akses toko berhasil diberikan');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * =========================================
     * CABUT AKSES USER DARI TOKO
     * =========================================
     */
    public function destroy($id_access)
    {
        $this->validateContext();
        $this->authorizeAdmin();

        $access = UserTokoAccess::with('toko')
            ->where('id_access', $id_access)
            ->first();

        if (! $access) {
            abort(404, 'Akses tidak ditemukan');
        }

        /**
         * ======================================
         * PASTIKAN MASIH SATU TENANT
         * ======================================
         */
        if ($access->toko->id_tenant !== Session::get('tenant_id')) {
            abort(403, 'Akses tidak valid');
        }

        DB::beginTransaction();
        try {
            $access->delete();

            DB::commit();
            return redirect()->back()
                ->with('success', 'Akses toko berhasil dicabut');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * =========================================
     * VALIDASI SESSION AUTH + TENANT
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
     * ADMIN / MANAGER / OWNER
     * =========================================
     */
    private function authorizeAdmin()
    {
        if (! in_array(Session::get('role_in_tenant'), ['OWNER', 'MANAGER', 'ADMIN'])) {
            abort(403, 'Akses ditolak');
        }
    }
}
