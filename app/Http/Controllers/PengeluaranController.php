<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Pengeluaran;
use App\Models\UserTokoAccess;

class PengeluaranController extends Controller
{
    /**
     * =========================================
     * LIST PENGELUARAN
     * =========================================
     */
    public function index(Request $request)
    {
        $user   = Auth::user();
        $idToko = session('id_toko');

        if (!UserTokoAccess::userHasAccessToToko($user->id_user, $idToko)) {
            abort(403, 'Tidak punya akses ke toko');
        }

        $query = Pengeluaran::where('id_toko', $idToko)
            ->with('user')
            ->orderByDesc('tgl_pengeluaran');

        if ($request->filled('kategori_biaya')) {
            $query->where('kategori_biaya', $request->kategori_biaya);
        }

        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_selesai')) {
            $query->whereBetween('tgl_pengeluaran', [
                $request->tanggal_mulai,
                $request->tanggal_selesai
            ]);
        }

        return response()->json($query->get());
    }

    /**
     * =========================================
     * DETAIL PENGELUARAN
     * =========================================
     */
    public function show($id)
    {
        $pengeluaran = Pengeluaran::with('user')->findOrFail($id);

        return response()->json($pengeluaran);
    }

    /**
     * =========================================
     * SIMPAN PENGELUARAN
     * =========================================
     */
    public function store(Request $request)
    {
        $request->validate([
            'tgl_pengeluaran' => 'required|date',
            'kategori_biaya'  => 'required|string|max:50',
            'nominal'         => 'required|numeric|min:1',
            'keterangan'      => 'nullable|string',
            'bukti_foto'      => 'nullable|string|max:255'
        ]);

        $user   = Auth::user();
        $idToko = session('id_toko');

        if (!UserTokoAccess::userHasAccessToToko($user->id_user, $idToko)) {
            abort(403, 'Tidak punya akses ke toko');
        }

        $pengeluaran = Pengeluaran::create([
            'id_toko'        => $idToko,
            'id_user'        => $user->id_user,
            'tgl_pengeluaran'=> $request->tgl_pengeluaran,
            'kategori_biaya' => $request->kategori_biaya,
            'nominal'        => $request->nominal,
            'keterangan'     => $request->keterangan,
            'bukti_foto'     => $request->bukti_foto
        ]);

        return response()->json([
            'message' => 'Pengeluaran berhasil dicatat',
            'data'    => $pengeluaran
        ]);
    }

    /**
     * =========================================
     * UPDATE PENGELUARAN
     * =========================================
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'tgl_pengeluaran' => 'required|date',
            'kategori_biaya'  => 'required|string|max:50',
            'nominal'         => 'required|numeric|min:1',
            'keterangan'      => 'nullable|string',
            'bukti_foto'      => 'nullable|string|max:255'
        ]);

        $user   = Auth::user();
        $idToko = session('id_toko');

        $pengeluaran = Pengeluaran::findOrFail($id);

        if ($pengeluaran->id_toko !== $idToko) {
            abort(403, 'Pengeluaran bukan milik toko ini');
        }

        $pengeluaran->update([
            'tgl_pengeluaran'=> $request->tgl_pengeluaran,
            'kategori_biaya' => $request->kategori_biaya,
            'nominal'        => $request->nominal,
            'keterangan'     => $request->keterangan,
            'bukti_foto'     => $request->bukti_foto
        ]);

        return response()->json([
            'message' => 'Pengeluaran berhasil diperbarui',
            'data'    => $pengeluaran
        ]);
    }

    /**
     * =========================================
     * HAPUS PENGELUARAN
     * =========================================
     */
    public function destroy($id)
    {
        $idToko = session('id_toko');

        $pengeluaran = Pengeluaran::findOrFail($id);

        if ($pengeluaran->id_toko !== $idToko) {
            abort(403, 'Pengeluaran bukan milik toko ini');
        }

        $pengeluaran->delete();

        return response()->json([
            'message' => 'Pengeluaran berhasil dihapus'
        ]);
    }
}
