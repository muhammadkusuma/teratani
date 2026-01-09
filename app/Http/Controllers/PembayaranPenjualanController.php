<?php
namespace App\Http\Controllers;

use App\Models\KartuPiutang;
use App\Models\PembayaranPiutang;
use App\Models\Penjualan;
use App\Models\UserTokoAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PembayaranPenjualanController extends Controller
{
    /**
     * =========================================
     * LIST PIUTANG BELUM LUNAS (PER TOKO)
     * =========================================
     */
    public function index()
    {
        $user   = Auth::user();
        $idToko = session('id_toko');

        if (! UserTokoAccess::userHasAccessToToko($user->id_user, $idToko)) {
            abort(403, 'Tidak punya akses ke toko');
        }

        $piutang = KartuPiutang::with(['pelanggan', 'penjualan'])
            ->where('id_toko', $idToko)
            ->belumLunas()
            ->get();

        return response()->json($piutang);
    }

    /**
     * =========================================
     * DETAIL PIUTANG
     * =========================================
     */
    public function show($idPiutang)
    {
        $piutang = KartuPiutang::with([
            'pelanggan',
            'penjualan',
            'penjualan.penjualanDetail',
        ])->findOrFail($idPiutang);

        return response()->json($piutang);
    }

    /**
     * =========================================
     * PROSES PEMBAYARAN PIUTANG
     * =========================================
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_piutang'   => 'required|integer|exists:kartu_piutang,id_piutang',
            'jumlah_bayar' => 'required|numeric|min:1',
            'metode_bayar' => 'required|in:Tunai,Transfer',
            'keterangan'   => 'nullable|string',
        ]);

        $user   = Auth::user();
        $idToko = session('id_toko');

        DB::beginTransaction();

        try {
            $piutang = KartuPiutang::lockForUpdate()->findOrFail($request->id_piutang);

            if ($piutang->id_toko != $idToko) {
                abort(403, 'Piutang tidak berasal dari toko ini');
            }

            if ($request->jumlah_bayar > $piutang->sisa_piutang) {
                abort(400, 'Jumlah bayar melebihi sisa piutang');
            }

            /**
             * =============================
             * 1. SIMPAN HISTORI PEMBAYARAN
             * =============================
             */
            PembayaranPiutang::create([
                'id_piutang'   => $piutang->id_piutang,
                'jumlah_bayar' => $request->jumlah_bayar,
                'metode_bayar' => $request->metode_bayar,
                'keterangan'   => $request->keterangan,
                'id_user'      => $user->id_user,
            ]);

            /**
             * =============================
             * 2. UPDATE KARTU PIUTANG
             * =============================
             */
            $piutang->tambahPembayaran($request->jumlah_bayar);

            /**
             * =============================
             * 3. UPDATE STATUS PENJUALAN
             * =============================
             */
            $penjualan = Penjualan::find($piutang->id_penjualan);

            if ($piutang->status === 'Lunas') {
                $penjualan->update([
                    'status_bayar' => 'Lunas',
                    'jumlah_bayar' => $penjualan->total_netto,
                    'kembalian'    => 0,
                ]);
            } else {
                $penjualan->update([
                    'status_bayar' => 'Sebagian',
                    'jumlah_bayar' => $penjualan->total_netto - $piutang->sisa_piutang,
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Pembayaran piutang berhasil',
                'piutang' => $piutang,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Pembayaran gagal',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
