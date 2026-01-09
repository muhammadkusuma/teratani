<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Pembelian;
use App\Models\PembelianDetail;
use App\Models\Distributor;
use App\Models\Stok;
use App\Models\LogStok;
use App\Models\UserTokoAccess;

class PembelianController extends Controller
{
    /**
     * =========================================
     * LIST PEMBELIAN
     * =========================================
     */
    public function index(Request $request)
    {
        $idToko = session('id_toko');
        $user   = Auth::user();

        if (!UserTokoAccess::userHasAccessToToko($user->id_user, $idToko)) {
            abort(403, 'Tidak punya akses ke toko');
        }

        $query = Pembelian::with('distributor')
            ->where('id_toko', $idToko)
            ->orderByDesc('id_pembelian');

        if ($request->filled('status_bayar')) {
            $query->where('status_bayar', $request->status_bayar);
        }

        return response()->json($query->get());
    }

    /**
     * =========================================
     * DETAIL PEMBELIAN
     * =========================================
     */
    public function show($id)
    {
        $pembelian = Pembelian::with(['distributor', 'detail.produk'])
            ->findOrFail($id);

        return response()->json($pembelian);
    }

    /**
     * =========================================
     * SIMPAN PEMBELIAN BARU
     * =========================================
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_distributor'           => 'nullable|exists:distributor,id_distributor',
            'no_faktur_supplier'       => 'nullable|string|max:50',
            'tgl_pembelian'            => 'required|date',
            'tgl_jatuh_tempo'          => 'nullable|date',
            'status_bayar'             => 'required|in:Lunas,Hutang',
            'items'                    => 'required|array|min:1',
            'items.*.id_produk'        => 'required|exists:produk,id_produk',
            'items.*.qty'              => 'required|integer|min:1',
            'items.*.harga_beli_satuan'=> 'required|numeric|min:0'
        ]);

        $idToko = session('id_toko');
        $user   = Auth::user();

        if (!UserTokoAccess::userHasAccessToToko($user->id_user, $idToko)) {
            abort(403, 'Tidak punya akses ke toko');
        }

        DB::beginTransaction();

        try {

            /**
             * ==============================
             * HITUNG TOTAL PEMBELIAN
             * ==============================
             */
            $totalPembelian = 0;
            foreach ($request->items as $item) {
                $totalPembelian += $item['qty'] * $item['harga_beli_satuan'];
            }

            /**
             * ==============================
             * SIMPAN HEADER PEMBELIAN
             * ==============================
             */
            $pembelian = Pembelian::create([
                'id_toko'           => $idToko,
                'id_distributor'    => $request->id_distributor,
                'no_faktur_supplier'=> $request->no_faktur_supplier,
                'tgl_pembelian'     => $request->tgl_pembelian,
                'tgl_jatuh_tempo'   => $request->tgl_jatuh_tempo,
                'total_pembelian'   => $totalPembelian,
                'status_bayar'      => $request->status_bayar
            ]);

            /**
             * ==============================
             * LOOP DETAIL PEMBELIAN
             * ==============================
             */
            foreach ($request->items as $item) {

                $subtotal = $item['qty'] * $item['harga_beli_satuan'];

                // Simpan detail
                PembelianDetail::create([
                    'id_pembelian'      => $pembelian->id_pembelian,
                    'id_produk'         => $item['id_produk'],
                    'qty'               => $item['qty'],
                    'harga_beli_satuan' => $item['harga_beli_satuan'],
                    'subtotal'          => $subtotal
                ]);

                /**
                 * ==============================
                 * UPDATE / CREATE STOK TOKO
                 * ==============================
                 */
                $stok = Stok::where('id_toko', $idToko)
                    ->where('id_produk', $item['id_produk'])
                    ->lockForUpdate()
                    ->first();

                if ($stok) {
                    $stok->stok_fisik += $item['qty'];
                    $stok->save();
                } else {
                    $stok = Stok::create([
                        'id_toko'     => $idToko,
                        'id_produk'   => $item['id_produk'],
                        'stok_fisik'  => $item['qty'],
                        'stok_minimal'=> 5,
                        'harga_jual'  => 0
                    ]);
                }

                /**
                 * ==============================
                 * LOG STOK (PEMBELIAN)
                 * ==============================
                 */
                LogStok::create([
                    'id_toko'        => $idToko,
                    'id_produk'      => $item['id_produk'],
                    'id_user'        => $user->id_user,
                    'jenis_transaksi'=> 'Pembelian',
                    'no_referensi'   => $request->no_faktur_supplier,
                    'qty_masuk'      => $item['qty'],
                    'qty_keluar'     => 0,
                    'stok_akhir'     => $stok->stok_fisik,
                    'keterangan'     => 'Pembelian dari distributor'
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Pembelian berhasil disimpan',
                'data'    => $pembelian
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
