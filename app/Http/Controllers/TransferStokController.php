<?php
namespace App\Http\Controllers;

use App\Models\LogStok;
use App\Models\Produk;
use App\Models\Stok;
use App\Models\Toko;
use App\Models\UserTokoAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransferStokController extends Controller
{
    /**
     * =========================================
     * LIST PRODUK & STOK (TOKO ASAL)
     * =========================================
     */
    public function index(Request $request)
    {
        $user       = Auth::user();
        $idTokoAsal = session('id_toko');

        // Validasi user punya akses ke toko asal
        if (! UserTokoAccess::userHasAccessToToko($user->id_user, $idTokoAsal)) {
            abort(403, 'Tidak punya akses ke toko ini');
        }

        $stok = Stok::with('produk')
            ->where('id_toko', $idTokoAsal)
            ->get();

        // Toko tujuan (selain toko asal)
        $tokoTujuan = Toko::where('id_tenant', session('id_tenant'))
            ->where('id_toko', '!=', $idTokoAsal)
            ->get();

        return response()->json([
            'stok_asal'   => $stok,
            'toko_tujuan' => $tokoTujuan,
        ]);
    }

    /**
     * =========================================
     * PROSES TRANSFER STOK
     * =========================================
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_toko_tujuan'    => 'required|integer',
            'items'             => 'required|array',
            'items.*.id_produk' => 'required|integer',
            'items.*.qty'       => 'required|integer|min:1',
            'keterangan'        => 'nullable|string',
        ]);

        $user         = Auth::user();
        $idTokoAsal   = session('id_toko');
        $idTokoTujuan = $request->id_toko_tujuan;

        // Validasi akses user ke kedua toko
        if (
            ! UserTokoAccess::userHasAccessToToko($user->id_user, $idTokoAsal) ||
            ! UserTokoAccess::userHasAccessToToko($user->id_user, $idTokoTujuan)
        ) {
            abort(403, 'Tidak punya akses ke salah satu toko');
        }

        DB::beginTransaction();

        try {
            foreach ($request->items as $item) {

                $idProduk = $item['id_produk'];
                $qty      = $item['qty'];

                /**
                 * =============================
                 * 1. KURANGI STOK TOKO ASAL
                 * =============================
                 */
                $stokAsal = Stok::kurangiStok($idTokoAsal, $idProduk, $qty);

                LogStok::create([
                    'id_toko'         => $idTokoAsal,
                    'id_produk'       => $idProduk,
                    'id_user'         => $user->id_user,
                    'jenis_transaksi' => 'Transfer Keluar',
                    'qty_masuk'       => 0,
                    'qty_keluar'      => $qty,
                    'stok_akhir'      => $stokAsal->stok,
                    'keterangan'      => $request->keterangan,
                ]);

                /**
                 * =============================
                 * 2. TAMBAH STOK TOKO TUJUAN
                 * =============================
                 */
                $stokTujuan = Stok::tambahStok($idTokoTujuan, $idProduk, $qty);

                LogStok::create([
                    'id_toko'         => $idTokoTujuan,
                    'id_produk'       => $idProduk,
                    'id_user'         => $user->id_user,
                    'jenis_transaksi' => 'Transfer Masuk',
                    'qty_masuk'       => $qty,
                    'qty_keluar'      => 0,
                    'stok_akhir'      => $stokTujuan->stok,
                    'keterangan'      => $request->keterangan,
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Transfer stok berhasil',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Transfer stok gagal',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
