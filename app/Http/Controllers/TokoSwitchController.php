<?php
namespace App\Http\Controllers;

use App\Models\Toko;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class TokoSwitchController extends Controller
{
    /**
     * =========================================
     * LIST TOKO YANG BISA DIAKSES USER
     * =========================================
     */
    public function listToko()
    {
        if (! Session::get('auth')) {
            return redirect()->route('login.form');
        }

        if (! Session::get('tenant_id')) {
            return redirect()->route('tenant.select');
        }

        $userId   = Session::get('user_id');
        $tenantId = Session::get('tenant_id');

        /**
         * ======================================
         * AMBIL TOKO SESUAI AKSES USER & TENANT
         * ======================================
         */
        $toko = DB::table('user_toko_access')
            ->join('toko', 'toko.id_toko', '=', 'user_toko_access.id_toko')
            ->where('user_toko_access.id_user', $userId)
            ->where('toko.id_tenant', $tenantId)
            ->select(
                'toko.id_toko',
                'toko.nama_toko',
                'toko.kode_toko',
                'toko.is_pusat'
            )
            ->orderByDesc('toko.is_pusat')
            ->get();

        if ($toko->count() === 0) {
            abort(403, 'Anda tidak memiliki akses ke toko manapun');
        }

        return view('toko.select', compact('toko'));
    }

    /**
     * =========================================
     * SWITCH TOKO
     * =========================================
     */
    public function switchToko(Request $request, $id_toko)
    {
        if (! Session::get('auth')) {
            return redirect()->route('login.form');
        }

        if (! Session::get('tenant_id')) {
            return redirect()->route('tenant.select');
        }

        $userId   = Session::get('user_id');
        $tenantId = Session::get('tenant_id');

        /**
         * ======================================
         * VALIDASI AKSES TOKO
         * ======================================
         */
        $akses = DB::table('user_toko_access')
            ->join('toko', 'toko.id_toko', '=', 'user_toko_access.id_toko')
            ->where('user_toko_access.id_user', $userId)
            ->where('toko.id_toko', $id_toko)
            ->where('toko.id_tenant', $tenantId)
            ->first();

        if (! $akses) {
            abort(403, 'Anda tidak memiliki akses ke toko ini');
        }

        /**
         * ======================================
         * SET SESSION TOKO AKTIF
         * ======================================
         */
        $toko = Toko::findOrFail($id_toko);

        Session::put('toko_id', $toko->id_toko);
        Session::put('toko_name', $toko->nama_toko);

        return redirect()->route('dashboard');
    }
}
