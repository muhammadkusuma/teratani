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
        $user = Auth::user();

        // PERBAIKAN: Gunakan 'toko_id' sesuai yang diset di AuthController
        $idToko = session('toko_id');

        // Validasi jika session toko hilang (misal session expired tapi user masih login)
        if (! $idToko) {
            return response()->json(['message' => 'Sesi toko tidak ditemukan, silakan pilih toko kembali.'], 400);
        }

        if (! UserTokoAccess::userHasAccessToToko($user->id_user, $idToko)) {
            abort(403, 'Tidak punya akses ke toko ini');
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
        // Pastikan juga mengecek kepemilikan toko saat show detail (best practice)
        $idToko = session('toko_id');

        $pembayaran = PembayaranPiutang::with([
            'kartuPiutang.pelanggan',
            'kartuPiutang.penjualan',
            'user',
        ])
            ->whereHas('kartuPiutang', function ($q) use ($idToko) {
                $q->where('id_toko', $idToko);
            })
            ->findOrFail($id);

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

        $user = Auth::user();

        // PERBAIKAN: Gunakan 'toko_id'
        $idToko = session('toko_id');

        if (! $idToko) {
            return response()->json(['message' => 'Sesi toko tidak valid.'], 400);
        }

        DB::beginTransaction();

        try {
            /**
             * =============================
             * 1. LOCK PIUTANG & VALIDASI
             * =============================
             */
            $piutang = KartuPiutang::lockForUpdate()
                ->findOrFail($request->id_piutang);

            // Validasi apakah piutang ini milik toko yang sedang aktif
            if ($piutang->id_toko != $idToko) {
                abort(403, 'Piutang bukan milik toko ini');
            }

            if ($request->jumlah_bayar > $piutang->sisa_piutang) {
                // Gunakan response JSON agar frontend bisa menangkap error 400 dengan rapi
                return response()->json(['message' => 'Jumlah bayar melebihi sisa piutang'], 400);
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
                'tgl_bayar'    => now(), // Pastikan tanggal terisi jika di database tidak default timestamp
            ]);

            /**
             * =============================
             * 3. UPDATE KARTU PIUTANG
             * =============================
             * Asumsi: Method tambahPembayaran() ada di model KartuPiutang
             */
            $piutang->tambahPembayaran($request->jumlah_bayar);

            /**
             * =============================
             * 4. UPDATE STATUS PENJUALAN
             * =============================
             */
            $penjualan = Penjualan::find($piutang->id_penjualan);

            // Refresh piutang untuk mendapatkan status terbaru setelah update
            $piutang->refresh();

            if ($piutang->status === 'Lunas') {
                $penjualan->update([
                    'status_bayar' => 'Lunas',
                    // Logic ini mungkin perlu disesuaikan jika ada diskon/pajak,
                    // tapi jika total_netto adalah final price, maka ini benar.
                    'jumlah_bayar' => $penjualan->total_netto,
                    'kembalian'    => 0,
                ]);
            } else {
                // Update jumlah bayar akumulatif
                // Asumsi: jumlah_bayar di penjualan adalah total uang masuk
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
     * HAPUS PEMBAYARAN (ROLLBACK)
     * =========================================
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $pembayaran = PembayaranPiutang::lockForUpdate()->findOrFail($id);
            $piutang    = KartuPiutang::lockForUpdate()->findOrFail($pembayaran->id_piutang);

            // Cek akses toko
            if ($piutang->id_toko != session('toko_id')) {
                abort(403, 'Akses ditolak');
            }

            // Rollback nilai di Kartu Piutang
            // Manual calculation karena mungkin tidak ada method 'kurangiPembayaran'
            $piutang->sudah_dibayar -= $pembayaran->jumlah_bayar;
            $piutang->sisa_piutang += $pembayaran->jumlah_bayar;

            // Update status piutang
            // Jika sisa piutang > 0, status jadi Belum Lunas
            $piutang->status = ($piutang->sisa_piutang <= 0) ? 'Lunas' : 'Belum Lunas';
            $piutang->save();

            // Update Penjualan
            $penjualan = Penjualan::find($piutang->id_penjualan);
            $penjualan->update([
                'status_bayar' => $piutang->status === 'Lunas' ? 'Lunas' : 'Belum Lunas',
                'jumlah_bayar' => $penjualan->total_netto - $piutang->sisa_piutang,
            ]);

            $pembayaran->delete();

            DB::commit();

            return response()->json([
                'message' => 'Pembayaran berhasil dihapus (Rollback)',
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
