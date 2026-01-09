<?php
namespace App\Http\Controllers;

use App\Models\LogStok;
use App\Models\Produk;
use App\Models\StokToko;
use App\Models\Toko;
use App\Models\UserTokoAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class StokController extends Controller
{
    /**
     * =========================================
     * LIST STOK PER TOKO
     * =========================================
     */
    public function index()
    {
        $this->validateContext();

        $tokoId = Session::get('toko_id');
        $userId = Session::get('user_id');

        $this->authorizeToko($userId, $tokoId);

        $stok = StokToko::with('produk')
            ->where('id_toko', $tokoId)
            ->orderBy('id_produk')
            ->get();

        return view('stok.index', compact('stok'));
    }

    /**
     * =========================================
     * FORM SET STOK AWAL / TAMBAH PRODUK KE TOKO
     * =========================================
     */
    public function create()
    {
        $this->validateContext();

        $tokoId   = Session::get('toko_id');
        $tenantId = Session::get('tenant_id');
        $userId   = Session::get('user_id');

        $this->authorizeToko($userId, $tokoId);

        $produk = Produk::where('id_tenant', $tenantId)
            ->where('is_active', true)
            ->orderBy('nama_produk')
            ->get();

        return view('stok.create', compact('produk'));
    }

    /**
     * =========================================
     * SIMPAN STOK AWAL
     * =========================================
     */
    public function store(Request $request)
    {
        $this->validateContext();

        $request->validate([
            'id_produk'    => 'required|integer',
            'stok_fisik'   => 'required|integer|min:0',
            'harga_jual'   => 'required|numeric|min:0',
            'stok_minimal' => 'nullable|integer|min:0',
            'lokasi_rak'   => 'nullable|string|max:50',
        ]);

        $tokoId   = Session::get('toko_id');
        $tenantId = Session::get('tenant_id');
        $userId   = Session::get('user_id');

        $this->authorizeToko($userId, $tokoId);

        /**
         * ======================================
         * VALIDASI PRODUK MILIK TENANT
         * ======================================
         */
        $produk = Produk::where('id_produk', $request->id_produk)
            ->where('id_tenant', $tenantId)
            ->first();

        if (! $produk) {
            abort(403, 'Produk tidak valid');
        }

        /**
         * ======================================
         * CEK DUPLIKASI STOK
         * ======================================
         */
        if (StokToko::where('id_toko', $tokoId)
            ->where('id_produk', $request->id_produk)
            ->exists()) {
            return redirect()->back()
                ->withErrors(['error' => 'Produk sudah memiliki stok di toko ini']);
        }

        DB::beginTransaction();
        try {
            $stok = StokToko::create([
                'id_toko'      => $tokoId,
                'id_produk'    => $request->id_produk,
                'stok_fisik'   => $request->stok_fisik,
                'stok_minimal' => $request->stok_minimal ?? 5,
                'harga_jual'   => $request->harga_jual,
                'lokasi_rak'   => $request->lokasi_rak,
            ]);

            /**
             * ==================================
             * LOG STOK (OPNAME AWAL)
             * ==================================
             */
            LogStok::create([
                'id_toko'         => $tokoId,
                'id_produk'       => $request->id_produk,
                'id_user'         => $userId,
                'jenis_transaksi' => 'Opname',
                'qty_masuk'       => $request->stok_fisik,
                'qty_keluar'      => 0,
                'stok_akhir'      => $request->stok_fisik,
                'keterangan'      => 'Stok awal',
            ]);

            DB::commit();
            return redirect()->route('stok.index')
                ->with('success', 'Stok awal berhasil disimpan');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * =========================================
     * FORM PENYESUAIAN STOK (OPNAME)
     * =========================================
     */
    public function edit($id_stok)
    {
        $this->validateContext();

        $stok = StokToko::with('produk')
            ->where('id_stok', $id_stok)
            ->first();

        if (! $stok || $stok->toko->id_tenant !== Session::get('tenant_id')) {
            abort(403, 'Akses tidak valid');
        }

        $this->authorizeToko(Session::get('user_id'), $stok->id_toko);

        return view('stok.edit', compact('stok'));
    }

    /**
     * =========================================
     * UPDATE STOK (OPNAME)
     * =========================================
     */
    public function update(Request $request, $id_stok)
    {
        $this->validateContext();

        $request->validate([
            'stok_fisik'   => 'required|integer|min:0',
            'harga_jual'   => 'required|numeric|min:0',
            'stok_minimal' => 'nullable|integer|min:0',
            'lokasi_rak'   => 'nullable|string|max:50',
            'keterangan'   => 'nullable|string',
        ]);

        $stok = StokToko::with('produk')
            ->where('id_stok', $id_stok)
            ->first();

        if (! $stok || $stok->toko->id_tenant !== Session::get('tenant_id')) {
            abort(403, 'Akses tidak valid');
        }

        $this->authorizeToko(Session::get('user_id'), $stok->id_toko);

        $stokAwal  = $stok->stok_fisik;
        $stokAkhir = $request->stok_fisik;

        DB::beginTransaction();
        try {
            $stok->update([
                'stok_fisik'   => $stokAkhir,
                'stok_minimal' => $request->stok_minimal ?? $stok->stok_minimal,
                'harga_jual'   => $request->harga_jual,
                'lokasi_rak'   => $request->lokasi_rak,
            ]);

            /**
             * ==================================
             * HITUNG SELISIH STOK
             * ==================================
             */
            $qtyMasuk  = 0;
            $qtyKeluar = 0;

            if ($stokAkhir > $stokAwal) {
                $qtyMasuk = $stokAkhir - $stokAwal;
            } elseif ($stokAkhir < $stokAwal) {
                $qtyKeluar = $stokAwal - $stokAkhir;
            }

            LogStok::create([
                'id_toko'         => $stok->id_toko,
                'id_produk'       => $stok->id_produk,
                'id_user'         => Session::get('user_id'),
                'jenis_transaksi' => 'Opname',
                'qty_masuk'       => $qtyMasuk,
                'qty_keluar'      => $qtyKeluar,
                'stok_akhir'      => $stokAkhir,
                'keterangan'      => $request->keterangan ?? 'Penyesuaian stok',
            ]);

            DB::commit();
            return redirect()->route('stok.index')
                ->with('success', 'Stok berhasil disesuaikan');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * =========================================
     * HELPER VALIDASI AUTH + TENANT + TOKO
     * =========================================
     */
    private function validateContext()
    {
        if (! Session::get('auth')) {
            abort(401, 'Silakan login terlebih dahulu');
        }

        if (! Session::get('tenant_id') || ! Session::get('toko_id')) {
            abort(403, 'Tenant / Toko belum dipilih');
        }
    }

    /**
     * =========================================
     * HELPER CEK AKSES TOKO
     * =========================================
     */
    private function authorizeToko($userId, $tokoId)
    {
        if (
            Session::get('role_in_tenant') === 'KASIR' &&
            ! UserTokoAccess::where('id_user', $userId)
            ->where('id_toko', $tokoId)
            ->exists()
        ) {
            abort(403, 'Anda tidak memiliki akses ke toko ini');
        }
    }
}
