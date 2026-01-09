<?php
namespace App\Http\Controllers;

use App\Models\KartuPiutang;
use App\Models\PembayaranPiutang;
use App\Models\Penjualan;
use App\Models\UserTokoAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PembayaranPiutangController extends Controller
{
    /**
     * =========================================
     * LIST PEMBAYARAN PIUTANG (PER TOKO)
     * =========================================
     */
    public function index(Request $request)
    {
        $user   = Auth::user();
        $idToko = session('id_toko');

        if (! UserTokoAccess::userHasAccessToToko($user->id_user, $idToko)) {
            abort(403, 'Tidak punya akses ke toko');
        }

        $query = PembayaranPiutang::with([
            'kartuPiutang.pelanggan',
            'kartuPiutang.penjualan',
            'user',
        ])->whereHas('kartuPiutang', function ($q) use ($idToko) {
            $q->where('id_toko', $idToko);
        });

        if ($request->filled('metode_bayar')) {
            $query->where('metode_bayar', $request->metode_bayar);
        }

        if ($request->filled('tanggal')) {
            $query->whereDate('tgl_bayar', $request->tanggal);
        }

        $data = $query
            ->orderBy('tgl_bayar', 'desc')
            ->get();

        return response()->json($data);
    }

    /**
     * =========================================
     * DETAIL PEMBAYARAN
     * =========================================
     */
    public function show($id)
    {
        $pembayaran = PembayaranPiutang::with([
            'kartuPiutang.pelanggan',
            'kartuPiutang.penjualan',
            'user',
        ])->findOrFail($id);

        return response()->json($pembayaran);
    }

    /**
     * =========================================
     * SIMPAN PEMBAYARAN PIUTANG
     * =========================================
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_piutang'   => 'required|exists:kartu_piutang,id_piutang',
            'jumlah_bayar' => 'required|numeric|min:1',
            'metode_bayar' => 'required|in:Tunai,Transfer',
            'keterangan'   => 'nullable|string',
        ]);

        $user   = Auth::user();
        $idToko = session('id_toko');

        DB::beginTransaction();

        try {
            /**
             * =============================
             * 1. LOCK PIUTANG
             * =============================
             */
            $piutang = KartuPiutang::lockForUpdate()
                ->findOrFail($request->id_piutang);

            if ($piutang->id_toko !== $idToko) {
                abort(403, 'Piutang bukan milik toko ini');
            }

            if ($request->jumlah_bayar > $piutang->sisa_piutang) {
                abort(400, 'Jumlah bayar melebihi sisa piutang');
            }

            /**
             * =============================
             * 2. SIMPAN HISTORI PEMBAYARAN
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
             * 3. UPDATE KARTU PIUTANG
             * =============================
             */
            $piutang->tambahPembayaran($request->jumlah_bayar);

            /**
             * =============================
             * 4. UPDATE STATUS PENJUALAN
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
                'data'    => $piutang,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Pembayaran piutang gagal',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * =========================================
     * HAPUS PEMBAYARAN (ROLLBACK PIUTANG)
     * =========================================
     * Optional: untuk admin / koreksi input
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $pembayaran = PembayaranPiutang::lockForUpdate()->findOrFail($id);
            $piutang    = KartuPiutang::lockForUpdate()->findOrFail($pembayaran->id_piutang);

            // rollback nilai
            $piutang->sudah_dibayar -= $pembayaran->jumlah_bayar;
            $piutang->sisa_piutang += $pembayaran->jumlah_bayar;
            $piutang->refreshStatus();

            // update penjualan
            $penjualan = Penjualan::find($piutang->id_penjualan);
            $penjualan->update([
                'status_bayar' => $piutang->status === 'Lunas' ? 'Lunas' : 'Belum Lunas',
                'jumlah_bayar' => $penjualan->total_netto - $piutang->sisa_piutang,
            ]);

            $pembayaran->delete();

            DB::commit();

            return response()->json([
                'message' => 'Pembayaran berhasil dihapus',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Gagal menghapus pembayaran',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
