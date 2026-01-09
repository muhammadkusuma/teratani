<?php
namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\UserTokoAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LaporanPenjualanController extends Controller
{
    /**
     * =========================================
     * LAPORAN PENJUALAN (HEADER)
     * =========================================
     */
    public function index(Request $request)
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

        $query = Penjualan::with(['pelanggan', 'sales', 'user'])
            ->where('id_toko', $idToko)
            ->whereBetween('tgl_transaksi', [
                $request->tanggal_mulai . ' 00:00:00',
                $request->tanggal_selesai . ' 23:59:59',
            ])
            ->where('status_transaksi', 'Selesai')
            ->orderBy('tgl_transaksi');

        if ($request->filled('metode_bayar')) {
            $query->where('metode_bayar', $request->metode_bayar);
        }

        if ($request->filled('status_bayar')) {
            $query->where('status_bayar', $request->status_bayar);
        }

        $data = $query->get();

        /**
         * =========================================
         * RINGKASAN
         * =========================================
         */
        $summary = [
            'total_transaksi' => $data->count(),
            'total_bruto'     => $data->sum('total_bruto'),
            'total_diskon'    => $data->sum('diskon_nota'),
            'total_ppn'       => $data->sum('pajak_ppn'),
            'total_netto'     => $data->sum('total_netto'),
            'total_bayar'     => $data->sum('jumlah_bayar'),
            'total_kembalian' => $data->sum('kembalian'),
        ];

        return response()->json([
            'summary' => $summary,
            'data'    => $data,
        ]);
    }

    /**
     * =========================================
     * LAPORAN PENJUALAN DETAIL ITEM
     * =========================================
     */
    public function detailItem(Request $request)
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

        $items = DB::table('penjualan_detail as d')
            ->join('penjualan as p', 'p.id_penjualan', '=', 'd.id_penjualan')
            ->join('produk as pr', 'pr.id_produk', '=', 'd.id_produk')
            ->where('p.id_toko', $idToko)
            ->where('p.status_transaksi', 'Selesai')
            ->whereBetween('p.tgl_transaksi', [
                $request->tanggal_mulai . ' 00:00:00',
                $request->tanggal_selesai . ' 23:59:59',
            ])
            ->select(
                'pr.nama_produk',
                DB::raw('SUM(d.qty) as total_qty'),
                DB::raw('SUM(d.subtotal) as total_penjualan')
            )
            ->groupBy('pr.id_produk', 'pr.nama_produk')
            ->orderByDesc('total_penjualan')
            ->get();

        return response()->json($items);
    }

    /**
     * =========================================
     * LAPORAN PENJUALAN PER PELANGGAN
     * =========================================
     */
    public function perPelanggan(Request $request)
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

        $data = DB::table('penjualan as p')
            ->leftJoin('pelanggan as pl', 'pl.id_pelanggan', '=', 'p.id_pelanggan')
            ->where('p.id_toko', $idToko)
            ->where('p.status_transaksi', 'Selesai')
            ->whereBetween('p.tgl_transaksi', [
                $request->tanggal_mulai . ' 00:00:00',
                $request->tanggal_selesai . ' 23:59:59',
            ])
            ->select(
                DB::raw('COALESCE(pl.nama_pelanggan, "UMUM") as nama_pelanggan'),
                DB::raw('COUNT(p.id_penjualan) as total_transaksi'),
                DB::raw('SUM(p.total_netto) as total_belanja')
            )
            ->groupBy('pl.id_pelanggan', 'pl.nama_pelanggan')
            ->orderByDesc('total_belanja')
            ->get();

        return response()->json($data);
    }
}
