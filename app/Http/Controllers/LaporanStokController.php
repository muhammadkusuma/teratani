<?php
namespace App\Http\Controllers;

use App\Models\LogStok;
use App\Models\Stok;
use App\Models\UserTokoAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LaporanStokController extends Controller
{
    /**
     * =========================================
     * LAPORAN STOK AKTIF (REAL TIME)
     * =========================================
     */
    public function index(Request $request)
    {
        $user   = Auth::user();
        $idToko = session('id_toko');

        if (! UserTokoAccess::userHasAccessToToko($user->id_user, $idToko)) {
            abort(403, 'Tidak punya akses ke toko');
        }

        $query = Stok::with('produk')
            ->where('id_toko', $idToko)
            ->orderBy('stok_fisik');

        if ($request->filled('stok_minimal')) {
            $query->whereColumn('stok_fisik', '<=', 'stok_minimal');
        }

        if ($request->filled('keyword')) {
            $query->whereHas('produk', function ($q) use ($request) {
                $q->where('nama_produk', 'like', '%' . $request->keyword . '%')
                    ->orWhere('sku', 'like', '%' . $request->keyword . '%');
            });
        }

        $data = $query->get();

        return response()->json($data);
    }

    /**
     * =========================================
     * KARTU STOK (MUTASI PRODUK)
     * =========================================
     */
    public function kartuStok(Request $request, $idProduk)
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

        $logs = LogStok::where('id_toko', $idToko)
            ->where('id_produk', $idProduk)
            ->whereBetween('created_at', [
                $request->tanggal_mulai . ' 00:00:00',
                $request->tanggal_selesai . ' 23:59:59',
            ])
            ->orderBy('created_at')
            ->get();

        return response()->json($logs);
    }

    /**
     * =========================================
     * REKAP NILAI STOK
     * =========================================
     */
    public function nilaiStok()
    {
        $user   = Auth::user();
        $idToko = session('id_toko');

        if (! UserTokoAccess::userHasAccessToToko($user->id_user, $idToko)) {
            abort(403, 'Tidak punya akses ke toko');
        }

        $data = DB::table('stok_toko as s')
            ->join('produk as p', 'p.id_produk', '=', 's.id_produk')
            ->where('s.id_toko', $idToko)
            ->select(
                DB::raw('SUM(s.stok_fisik * p.harga_pokok_standar) as total_nilai_stok'),
                DB::raw('SUM(s.stok_fisik) as total_qty')
            )
            ->first();

        return response()->json($data);
    }

    /**
     * =========================================
     * STOK MENIPIS (ALERT)
     * =========================================
     */
    public function stokMenipis()
    {
        $user   = Auth::user();
        $idToko = session('id_toko');

        if (! UserTokoAccess::userHasAccessToToko($user->id_user, $idToko)) {
            abort(403, 'Tidak punya akses ke toko');
        }

        $data = Stok::with('produk')
            ->where('id_toko', $idToko)
            ->whereColumn('stok_fisik', '<=', 'stok_minimal')
            ->orderBy('stok_fisik')
            ->get();

        return response()->json($data);
    }
}
