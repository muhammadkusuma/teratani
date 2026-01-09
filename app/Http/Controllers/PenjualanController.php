<?php
namespace App\Http\Controllers;

use App\Models\KartuPiutang;
use App\Models\LogStok;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\Produk;
use App\Models\Stok;
use App\Models\UserTokoAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PenjualanController extends Controller
{
    /**
     * =========================================
     * LIST PRODUK SIAP JUAL (BERDASARKAN TOKO)
     * =========================================
     */
    public function index()
    {
        $user   = Auth::user();
        $idToko = session('id_toko');

        if (! UserTokoAccess::userHasAccessToToko($user->id_user, $idToko)) {
            abort(403, 'Tidak punya akses ke toko');
        }

        $produk = Stok::with('produk')
            ->where('id_toko', $idToko)
            ->where('stok', '>', 0)
            ->get();

        return response()->json($produk);
    }

    /**
     * =========================================
     * PROSES TRANSAKSI PENJUALAN
     * =========================================
     */
    public function store(Request $request)
    {
        $request->validate([
            'items'              => 'required|array|min:1',
            'items.*.id_produk'  => 'required|integer',
            'items.*.qty'        => 'required|integer|min:1',
            'items.*.harga_jual' => 'required|numeric|min:0',

            'id_pelanggan'       => 'nullable|integer',
            'id_sales'           => 'nullable|integer',
            'metode_bayar'       => 'required|in:Tunai,Kredit/Piutang,Transfer,E-Wallet',
            'tgl_jatuh_tempo'    => 'nullable|date',

            'diskon_nota'        => 'numeric|min:0',
            'pajak_ppn'          => 'numeric|min:0',
            'biaya_lain'         => 'numeric|min:0',
            'jumlah_bayar'       => 'numeric|min:0',
        ]);

        $user   = Auth::user();
        $idToko = session('id_toko');

        DB::beginTransaction();

        try {
            /**
             * =============================
             * 1. BUAT HEADER PENJUALAN
             * =============================
             */
            $penjualan = Penjualan::create([
                'id_toko'          => $idToko,
                'id_user'          => $user->id_user,
                'id_pelanggan'     => $request->id_pelanggan,
                'id_sales'         => $request->id_sales,
                'no_faktur'        => Penjualan::generateNoFaktur($idToko),
                'tgl_jatuh_tempo'  => $request->tgl_jatuh_tempo,
                'diskon_nota'      => $request->diskon_nota ?? 0,
                'pajak_ppn'        => $request->pajak_ppn ?? 0,
                'biaya_lain'       => $request->biaya_lain ?? 0,
                'metode_bayar'     => $request->metode_bayar,
                'status_transaksi' => 'Selesai',
            ]);

            $totalBruto = 0;

            /**
             * =============================
             * 2. DETAIL + POTONG STOK
             * =============================
             */
            foreach ($request->items as $item) {

                $stok = Stok::kurangiStok($idToko, $item['id_produk'], $item['qty']);

                $produk = Produk::findOrFail($item['id_produk']);

                $subtotal = $item['qty'] * $item['harga_jual'];
                $totalBruto += $subtotal;

                PenjualanDetail::create([
                    'id_penjualan'          => $penjualan->id_penjualan,
                    'id_produk'             => $item['id_produk'],
                    'qty'                   => $item['qty'],
                    'satuan_saat_jual'      => $produk->satuan?->nama_satuan,
                    'harga_modal_saat_jual' => $produk->harga_pokok_standar,
                    'harga_jual_satuan'     => $item['harga_jual'],
                    'diskon_item'           => 0,
                    'subtotal'              => $subtotal,
                ]);

                LogStok::create([
                    'id_toko'         => $idToko,
                    'id_produk'       => $item['id_produk'],
                    'id_user'         => $user->id_user,
                    'jenis_transaksi' => 'Penjualan',
                    'qty_masuk'       => 0,
                    'qty_keluar'      => $item['qty'],
                    'stok_akhir'      => $stok->stok,
                    'no_referensi'    => $penjualan->no_faktur,
                ]);
            }

            /**
             * =============================
             * 3. HITUNG TOTAL
             * =============================
             */
            $totalNetto = $totalBruto
                 - ($penjualan->diskon_nota)
                 + ($penjualan->pajak_ppn)
                 + ($penjualan->biaya_lain);

            $penjualan->update([
                'total_bruto'  => $totalBruto,
                'total_netto'  => $totalNetto,
                'jumlah_bayar' => $request->jumlah_bayar,
                'kembalian'    => max(0, $request->jumlah_bayar - $totalNetto),
                'status_bayar' => $request->metode_bayar === 'Kredit/Piutang'
                    ? 'Belum Lunas'
                    : 'Lunas',
            ]);

            /**
             * =============================
             * 4. KARTU PIUTANG (JIKA KREDIT)
             * =============================
             */
            if ($request->metode_bayar === 'Kredit/Piutang') {

                KartuPiutang::create([
                    'id_toko'         => $idToko,
                    'id_penjualan'    => $penjualan->id_penjualan,
                    'id_pelanggan'    => $request->id_pelanggan,
                    'tgl_jatuh_tempo' => $request->tgl_jatuh_tempo,
                    'total_piutang'   => $totalNetto,
                    'sudah_dibayar'   => $request->jumlah_bayar ?? 0,
                    'sisa_piutang'    => $totalNetto - ($request->jumlah_bayar ?? 0),
                    'status'          => 'Belum Lunas',
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Penjualan berhasil',
                'data'    => $penjualan,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Penjualan gagal',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
