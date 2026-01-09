<?php
namespace App\Http\Controllers;

use App\Models\Distributor;
use App\Models\UserTokoAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DistributorController extends Controller
{
    /**
     * =========================================
     * LIST DISTRIBUTOR
     * =========================================
     */
    public function index(Request $request)
    {
        $user   = Auth::user();
        $idToko = session('id_toko');

        if (! UserTokoAccess::userHasAccessToToko($user->id_user, $idToko)) {
            abort(403, 'Tidak punya akses ke toko');
        }

        $query = Distributor::where('id_toko', $idToko);

        if ($request->filled('keyword')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_distributor', 'like', '%' . $request->keyword . '%')
                    ->orWhere('no_hp', 'like', '%' . $request->keyword . '%');
            });
        }

        $data = $query
            ->orderBy('nama_distributor')
            ->get();

        return response()->json($data);
    }

    /**
     * =========================================
     * DETAIL DISTRIBUTOR
     * =========================================
     */
    public function show($id)
    {
        $distributor = Distributor::findOrFail($id);

        return response()->json($distributor);
    }

    /**
     * =========================================
     * SIMPAN DISTRIBUTOR BARU
     * =========================================
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_distributor' => 'required|string|max:150',
            'alamat'           => 'nullable|string',
            'no_hp'            => 'nullable|string|max:20',
            'email'            => 'nullable|email|max:100',
            'keterangan'       => 'nullable|string',
        ]);

        $user   = Auth::user();
        $idToko = session('id_toko');

        if (! UserTokoAccess::userHasAccessToToko($user->id_user, $idToko)) {
            abort(403, 'Tidak punya akses ke toko');
        }

        $distributor = Distributor::create([
            'id_toko'          => $idToko,
            'nama_distributor' => $request->nama_distributor,
            'alamat'           => $request->alamat,
            'no_hp'            => $request->no_hp,
            'email'            => $request->email,
            'keterangan'       => $request->keterangan,
            'created_by'       => $user->id_user,
        ]);

        return response()->json([
            'message' => 'Distributor berhasil ditambahkan',
            'data'    => $distributor,
        ]);
    }

    /**
     * =========================================
     * UPDATE DISTRIBUTOR
     * =========================================
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_distributor' => 'required|string|max:150',
            'alamat'           => 'nullable|string',
            'no_hp'            => 'nullable|string|max:20',
            'email'            => 'nullable|email|max:100',
            'keterangan'       => 'nullable|string',
        ]);

        $user   = Auth::user();
        $idToko = session('id_toko');

        $distributor = Distributor::findOrFail($id);

        if ($distributor->id_toko !== $idToko) {
            abort(403, 'Distributor bukan milik toko ini');
        }

        $distributor->update([
            'nama_distributor' => $request->nama_distributor,
            'alamat'           => $request->alamat,
            'no_hp'            => $request->no_hp,
            'email'            => $request->email,
            'keterangan'       => $request->keterangan,
            'updated_by'       => $user->id_user,
        ]);

        return response()->json([
            'message' => 'Distributor berhasil diperbarui',
            'data'    => $distributor,
        ]);
    }

    /**
     * =========================================
     * HAPUS DISTRIBUTOR
     * =========================================
     */
    public function destroy($id)
    {
        $idToko = session('id_toko');

        $distributor = Distributor::findOrFail($id);

        if ($distributor->id_toko !== $idToko) {
            abort(403, 'Distributor bukan milik toko ini');
        }

        $distributor->delete();

        return response()->json([
            'message' => 'Distributor berhasil dihapus',
        ]);
    }
}
