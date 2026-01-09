<?php
namespace App\Http\Controllers;

use App\Models\LogStok;
use App\Models\Produk;
use App\Models\Stok;
use App\Models\UserTokoAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StokOpnameController extends Controller
{
    /**
     * =========================================
     * CONSTRUCTOR (AUTH WAJIB)
     * =========================================
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * =========================================
     * HALAMAN LIST PRODUK UNTUK STOK OPNAME
     * =========================================
     */
    public function index(Request $request)
    {
        $idToko = session('id_toko');
        $userId = Auth::id();

        if (! $idToko) {
            abort(403, 'Toko belum dipilih');
        }

        // Validasi akses toko
        if (! UserTokoAccess::userHasAccessToToko($userId, $idToko)) {
            abort(403, 'Anda tidak memiliki akses ke toko ini');
        }

        $produk = Produk::with(['stok' => function ($q) use ($idToko) {
            $q->where('id_toko', $idToko);
        }])
            ->where('status', 'aktif')
            ->orderBy('nama_produk')
            ->get();

        return view('stok_opname.index', compact('produk'));
    }

    /**
     * =========================================
     * SIMPAN PROSES STOK OPNAME
     * =========================================
     */
    public function store(Request $request)
    {
        $request->validate([
            'produk'              => 'required|array',
            'produk.*.id_produk'  => 'required|integer',
            'produk.*.stok_fisik' => 'required|numeric|min:0',
            'keterangan'          => 'nullable|string',
        ]);

        $idToko = session('id_toko');
        $idUser = Auth::id();

        if (! $idToko) {
            abort(403, 'Toko belum dipilih');
        }

        if (! UserTokoAccess::userHasAccessToToko($idUser, $idToko)) {
            abort(403, 'Anda tidak memiliki akses ke toko ini');
        }

        DB::transaction(function () use ($request, $idToko, $idUser) {

            foreach ($request->produk as $item) {

                $produk = Produk::findOrFail($item['id_produk']);

                $stok = Stok::where('id_toko', $idToko)
                    ->where('id_produk', $produk->id_produk)
                    ->lockForUpdate()
                    ->first();

                if (! $stok) {
                    $stok = Stok::create([
                        'id_toko'   => $idToko,
                        'id_produk' => $produk->id_produk,
                        'stok'      => 0,
                    ]);
                }

                $stokSebelum = $stok->stok;
                $stokSesudah = $item['stok_fisik'];

                // Skip jika tidak ada perubahan
                if ($stokSebelum == $stokSesudah) {
                    continue;
                }

                // Update stok
                $stok->stok = $stokSesudah;
                $stok->save();

                // Log stok opname
                LogStok::logOpname(
                    $idToko,
                    $produk->id_produk,
                    $idUser,
                    $stokSebelum,
                    $stokSesudah,
                    $request->keterangan
                        ? 'Opname: ' . $request->keterangan
                        : 'Stok opname manual'
                );
            }
        });

        return redirect()
            ->route('stok-opname.index')
            ->with('success', 'Stok opname berhasil disimpan');
    }

    /**
     * =========================================
     * DETAIL HISTORI OPNAME PER PRODUK
     * =========================================
     */
    public function histori($idProduk)
    {
        $idToko = session('id_toko');
        $idUser = Auth::id();

        if (! $idToko) {
            abort(403, 'Toko belum dipilih');
        }

        if (! UserTokoAccess::userHasAccessToToko($idUser, $idToko)) {
            abort(403, 'Anda tidak memiliki akses ke toko ini');
        }

        $produk = Produk::findOrFail($idProduk);

        $histori = LogStok::where('id_toko', $idToko)
            ->where('id_produk', $idProduk)
            ->where('jenis_transaksi', 'Opname')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('stok_opname.histori', compact('produk', 'histori'));
    }
}
