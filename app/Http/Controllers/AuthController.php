<?php
namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\Toko;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    /**
     * =========================
     * TAMPILKAN FORM LOGIN
     * =========================
     */
    public function loginForm()
    {
        return view('auth.login');
    }

    /**
     * =========================
     * PROSES LOGIN USER
     * =========================
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Cari user berdasarkan username ATAU email
        $user = User::where('username', $request->username)
            ->orWhere('email', $request->username)
            ->first();

        if (! $user) {
            return back()->withErrors(['username' => 'User tidak ditemukan']);
        }

        if (! $user->is_active) {
            return back()->withErrors(['username' => 'Akun tidak aktif']);
        }

        if (! Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Password salah']);
        }

        /**
         * ======================================
         * AMBIL DATA TENANT USER
         * ======================================
         */
        $tenantMapping = DB::table('user_tenant_mapping')
            ->where('id_user', $user->id_user)
            ->orderByDesc('is_primary')
            ->get();

        if ($tenantMapping->count() === 0) {
            return back()->withErrors([
                'username' => 'User belum terdaftar pada tenant manapun',
            ]);
        }

        /**
         * ======================================
         * SET SESSION USER
         * ======================================
         */
        Session::put('auth', true);
        Session::put('user_id', $user->id_user);
        Session::put('username', $user->username);
        Session::put('is_superadmin', $user->is_superadmin);

        /**
         * ======================================
         * JIKA USER HANYA PUNYA 1 TENANT
         * ======================================
         */
        if ($tenantMapping->count() === 1) {

            $tenantId = $tenantMapping->first()->id_tenant;
            $this->setTenantSession($user->id_user, $tenantId);

            return redirect()->route('dashboard');
        }

        /**
         * ======================================
         * JIKA USER PUNYA BANYAK TENANT
         * ======================================
         */
        return redirect()->route('tenant.select');
    }

    /**
     * =========================
     * SET SESSION TENANT & TOKO
     * =========================
     */
    private function setTenantSession($id_user, $id_tenant)
    {
        $tenant = Tenant::findOrFail($id_tenant);

        // Validasi user benar punya akses tenant
        $mapping = DB::table('user_tenant_mapping')
            ->where('id_user', $id_user)
            ->where('id_tenant', $id_tenant)
            ->first();

        if (! $mapping) {
            abort(403, 'Akses tenant ditolak');
        }

        Session::put('tenant_id', $tenant->id_tenant);
        Session::put('tenant_name', $tenant->nama_bisnis);
        Session::put('tenant_role', $mapping->role_in_tenant);

        /**
         * ======================================
         * PILIH TOKO DEFAULT
         * ======================================
         * Prioritas:
         * 1. Toko pusat
         * 2. Toko pertama yg diizinkan
         */
        $toko = Toko::where('id_tenant', $tenant->id_tenant)
            ->whereIn('id_toko', function ($query) use ($id_user) {
                $query->select('id_toko')
                    ->from('user_toko_access')
                    ->where('id_user', $id_user);
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
    }

    /**
     * =========================
     * LOGOUT
     * =========================
     */
    public function logout(Request $request)
    {
        Session::flush();
        return redirect()->route('login.form');
    }
}
