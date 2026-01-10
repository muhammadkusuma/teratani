<?php
namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\Toko;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // <--- PENTING: Tambahkan Import Auth
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    /**
     * =========================
     * FORM LOGIN
     * =========================
     */
    public function loginForm()
    {
        return view('auth.login');
    }

    /**
     * =========================
     * PROSES LOGIN
     * =========================
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

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
         * ==================================================
         * PERBAIKAN UTAMA: LOGIN KE LARAVEL AUTH SYSTEM
         * ==================================================
         * Ini wajib agar middleware('auth') tidak menendang user
         */
        Auth::login($user); // <--- Baris Kunci

        /**
         * =========================
         * SET SESSION TAMBAHAN
         * =========================
         */
        Session::put('auth', true);
        Session::put('user_id', $user->id_user);
        Session::put('username', $user->username);
        Session::put('is_superadmin', (bool) $user->is_superadmin);

        /**
         * ==================================================
         * BYPASS KHUSUS SUPERADMIN (TANPA TENANT)
         * ==================================================
         */
        if ($user->is_superadmin) {
            Session::put('tenant_id', null);
            Session::put('tenant_name', 'SUPER ADMIN');
            Session::put('tenant_role', 'SUPERADMIN');

            Session::forget('toko_id');
            Session::forget('toko_name');

            return redirect()->route('dashboard.superadmin');
        }

        /**
         * =========================
         * USER NON SUPERADMIN
         * =========================
         */
        $tenantMapping = DB::table('user_tenant_mapping')
            ->where('id_user', $user->id_user)
            ->orderByDesc('is_primary')
            ->get();

        if ($tenantMapping->count() === 0) {
            Auth::logout(); // Logout jika tidak punya tenant
            return back()->withErrors([
                'username' => 'User belum terdaftar pada tenant manapun',
            ]);
        }

        /**
         * =========================
         * JIKA HANYA 1 TENANT
         * =========================
         */
        if ($tenantMapping->count() === 1) {
            $tenantId = $tenantMapping->first()->id_tenant;
            $this->setTenantSession($user->id_user, $tenantId);
            return redirect()->route('dashboard');
        }

        /**
         * =========================
         * JIKA BANYAK TENANT
         * =========================
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

        if (! Session::get('is_superadmin')) {
            $mapping = DB::table('user_tenant_mapping')
                ->where('id_user', $id_user)
                ->where('id_tenant', $id_tenant)
                ->first();

            if (! $mapping) {
                abort(403, 'Akses tenant ditolak');
            }

            Session::put('tenant_role', $mapping->role_in_tenant);
        } else {
            Session::put('tenant_role', 'SUPERADMIN');
        }

        Session::put('tenant_id', $tenant->id_tenant);
        Session::put('tenant_name', $tenant->nama_bisnis);

        // Pilih Toko Default
        $toko = Toko::where('id_tenant', $tenant->id_tenant)
            ->when(! Session::get('is_superadmin'), function ($query) use ($id_user) {
                $query->whereIn('id_toko', function ($q) use ($id_user) {
                    $q->select('id_toko')
                        ->from('user_toko_access')
                        ->where('id_user', $id_user);
                });
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
        Auth::logout();                         // <--- Gunakan Auth logout resmi
        $request->session()->invalidate();      // Bersihkan session ID
        $request->session()->regenerateToken(); // Regenerate CSRF token

        return redirect()->route('login.form');
    }
}
