<?php
namespace App\Http\Controllers;

use App\Models\UserTokoAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LaporanKeuanganController extends Controller
{
    /**
     * =========================================
     * LAPORAN LABA RUGI
     * =========================================
     */
    public function labaRugi(Request $request)
    {
        $user   = Auth::user();
        $idToko = session('id_toko');

        if (! UserTokoAccess::userHasAccessToToko($user->id_user, $idToko)) {
            abort(403, 'Tidak punya akses ke toko');
        }

        $request->validate([
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date',
        ]);

        /**
         * ==============================
         * TOTAL PENJUALAN NETTO
         * ==============================
         */
        $penjualan = DB::table('penjualan')
            ->where('id_toko', $idToko)
            ->where('status_transaksi', 'Selesai')
            ->whereBetween('tgl_transaksi', [
                $request->tanggal_mulai . ' 00:00:00',
                $request->tanggal_selesai . ' 23:59:59',
            ])
            ->sum('total_netto');

        /**
         * ==============================
         * HPP (HARGA POKOK PENJUALAN)
         * ==============================
         */
        $hpp = DB::table('penjualan_detail as d')
            ->join('penjualan as p', 'p.id_penjualan', '=', 'd.id_penjualan')
            ->where('p.id_toko', $idToko)
            ->where('p.status_transaksi', 'Selesai')
            ->whereBetween('p.tgl_transaksi', [
                $request->tanggal_mulai . ' 00:00:00',
                $request->tanggal_selesai . ' 23:59:59',
            ])
            ->sum(DB::raw('d.qty * d.harga_modal_saat_jual'));

        /**
         * ==============================
         * TOTAL PENGELUARAN OPERASIONAL
         * ==============================
         */
        $pengeluaran = DB::table('pengeluaran')
            ->where('id_toko', $idToko)
            ->whereBetween('tgl_pengeluaran', [
                $request->tanggal_mulai,
                $request->tanggal_selesai,
            ])
            ->sum('nominal');

        /**
         * ==============================
         * HITUNG LABA
         * ==============================
         */
        $labaKotor  = $penjualan - $hpp;
        $labaBersih = $labaKotor - $pengeluaran;

        return response()->json([
            'penjualan'   => $penjualan,
            'hpp'         => $hpp,
            'laba_kotor'  => $labaKotor,
            'pengeluaran' => $pengeluaran,
            'laba_bersih' => $labaBersih,
        ]);
    }

    /**
     * =========================================
     * LAPORAN ARUS KAS
     * =========================================
     */
    public function arusKas(Request $request)
    {
        $user   = Auth::user();
        $idToko = session('id_toko');

        if (! UserTokoAccess::userHasAccessToToko($user->id_user, $idToko)) {
            abort(403, 'Tidak punya akses ke toko');
        }

        $request->validate([
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date',
        ]);

        /**
         * ==============================
         * KAS MASUK (PENJUALAN TUNAI)
         * ==============================
         */
        $kasMasuk = DB::table('penjualan')
            ->where('id_toko', $idToko)
            ->where('status_transaksi', 'Selesai')
            ->whereIn('metode_bayar', ['Tunai', 'Transfer', 'E-Wallet'])
            ->whereBetween('tgl_transaksi', [
                $request->tanggal_mulai . ' 00:00:00',
                $request->tanggal_selesai . ' 23:59:59',
            ])
            ->sum('jumlah_bayar');

        /**
         * ==============================
         * KAS KELUAR (PENGELUARAN)
         * ==============================
         */
        $kasKeluar = DB::table('pengeluaran')
            ->where('id_toko', $idToko)
            ->whereBetween('tgl_pengeluaran', [
                $request->tanggal_mulai,
                $request->tanggal_selesai,
            ])
            ->sum('nominal');

        $saldoKas = $kasMasuk - $kasKeluar;

        return response()->json([
            'kas_masuk'  => $kasMasuk,
            'kas_keluar' => $kasKeluar,
            'saldo_kas'  => $saldoKas,
        ]);
    }

    /**
     * =========================================
     * RINGKASAN KEUANGAN (DASHBOARD)
     * =========================================
     */
    public function summary(Request $request)
    {
        $user   = Auth::user();
        $idToko = session('id_toko');

        if (! UserTokoAccess::userHasAccessToToko($user->id_user, $idToko)) {
            abort(403, 'Tidak punya akses ke toko');
        }

        $tanggalMulai   = $request->tanggal_mulai ?? now()->startOfMonth()->toDateString();
        $tanggalSelesai = $request->tanggal_selesai ?? now()->endOfMonth()->toDateString();

        $totalPenjualan = DB::table('penjualan')
            ->where('id_toko', $idToko)
            ->where('status_transaksi', 'Selesai')
            ->whereBetween('tgl_transaksi', [
                $tanggalMulai . ' 00:00:00',
                $tanggalSelesai . ' 23:59:59',
            ])
            ->sum('total_netto');

        $totalPengeluaran = DB::table('pengeluaran')
            ->where('id_toko', $idToko)
            ->whereBetween('tgl_pengeluaran', [
                $tanggalMulai,
                $tanggalSelesai,
            ])
            ->sum('nominal');

        return response()->json([
            'periode'           => [
                'mulai'   => $tanggalMulai,
                'selesai' => $tanggalSelesai,
            ],
            'total_penjualan'   => $totalPenjualan,
            'total_pengeluaran' => $totalPengeluaran,
            'estimasi_laba'     => $totalPenjualan - $totalPengeluaran,
        ]);
    }
}
