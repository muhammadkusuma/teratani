<?php
namespace App\Http\Controllers;

use App\Models\KartuPiutang;
use App\Models\UserTokoAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PiutangController extends Controller
{
    /**
     * =========================================
     * LIST PIUTANG (DASHBOARD PIUTANG)
     * =========================================
     * Filter:
     * - status (Lunas / Belum Lunas)
     * - pelanggan
     * - overdue
     */
    public function index(Request $request)
    {
        $user   = Auth::user();
        $idToko = session('id_toko');

        if (! UserTokoAccess::userHasAccessToToko($user->id_user, $idToko)) {
            abort(403, 'Tidak punya akses ke toko');
        }

        $query = KartuPiutang::with(['pelanggan', 'penjualan'])
            ->where('id_toko', $idToko);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('id_pelanggan')) {
            $query->where('id_pelanggan', $request->id_pelanggan);
        }

        if ($request->boolean('overdue')) {
            $query->whereDate('tgl_jatuh_tempo', '<', now())
                ->where('status', 'Belum Lunas');
        }

        $piutang = $query
            ->orderBy('tgl_jatuh_tempo', 'asc')
            ->get();

        return response()->json($piutang);
    }

    /**
     * =========================================
     * DETAIL PIUTANG + HISTORI PEMBAYARAN
     * =========================================
     */
    public function show($id)
    {
        $piutang = KartuPiutang::with([
            'pelanggan',
            'penjualan',
            'penjualan.penjualanDetail',
            'pembayaran',
        ])->findOrFail($id);

        return response()->json($piutang);
    }

    /**
     * =========================================
     * LAPORAN AGING PIUTANG
     * =========================================
     * Bucket:
     * 0-30 | 31-60 | 61-90 | >90 hari
     */
    public function aging()
    {
        $idToko = session('id_toko');
        $today  = now()->startOfDay();

        $piutang = KartuPiutang::where('id_toko', $idToko)
            ->where('status', 'Belum Lunas')
            ->get();

        $aging = [
            '0_30'  => 0,
            '31_60' => 0,
            '61_90' => 0,
            '90_up' => 0,
        ];

        foreach ($piutang as $p) {
            $hari = $today->diffInDays($p->tgl_jatuh_tempo, false);

            if ($hari >= 0 && $hari <= 30) {
                $aging['0_30'] += $p->sisa_piutang;
            } elseif ($hari >= -60 && $hari < -30) {
                $aging['31_60'] += $p->sisa_piutang;
            } elseif ($hari >= -90 && $hari < -60) {
                $aging['61_90'] += $p->sisa_piutang;
            } else {
                $aging['90_up'] += $p->sisa_piutang;
            }
        }

        return response()->json([
            'aging'         => $aging,
            'total_piutang' => array_sum($aging),
        ]);
    }
}
