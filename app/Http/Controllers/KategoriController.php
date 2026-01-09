<?php
namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class KategoriController extends Controller
{
    /**
     * =========================================
     * LIST KATEGORI (PER TENANT)
     * =========================================
     */
    public function index()
    {
        $this->validateContext();

        $tenantId = Session::get('tenant_id');

        $kategori = Kategori::where('id_tenant', $tenantId)
            ->orderBy('nama_kategori')
            ->get();

        return view('kategori.index', compact('kategori'));
    }

    /**
     * =========================================
     * SIMPAN KATEGORI BARU
     * =========================================
     */
    public function store(Request $request)
    {
        $this->validateContext();

        $request->validate([
            'nama_kategori' => 'required|string|max:50',
            'deskripsi'     => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            Kategori::create([
                'id_tenant'     => Session::get('tenant_id'),
                'nama_kategori' => $request->nama_kategori,
                'deskripsi'     => $request->deskripsi,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Kategori berhasil ditambahkan');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * =========================================
     * UPDATE KATEGORI
     * =========================================
     */
    public function update(Request $request, $id_kategori)
    {
        $this->validateContext();

        $request->validate([
            'nama_kategori' => 'required|string|max:50',
            'deskripsi'     => 'nullable|string',
        ]);

        $kategori = Kategori::where('id_kategori', $id_kategori)
            ->where('id_tenant', Session::get('tenant_id'))
            ->first();

        if (! $kategori) {
            abort(403, 'Kategori tidak ditemukan atau tidak memiliki akses');
        }

        DB::beginTransaction();
        try {
            $kategori->update([
                'nama_kategori' => $request->nama_kategori,
                'deskripsi'     => $request->deskripsi,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Kategori berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * =========================================
     * HAPUS KATEGORI
     * =========================================
     */
    public function destroy($id_kategori)
    {
        $this->validateContext();

        $kategori = Kategori::where('id_kategori', $id_kategori)
            ->where('id_tenant', Session::get('tenant_id'))
            ->first();

        if (! $kategori) {
            abort(403, 'Kategori tidak ditemukan atau tidak memiliki akses');
        }

        /**
         * ======================================
         * CEK APAKAH MASIH DIPAKAI PRODUK
         * ======================================
         */
        if ($kategori->produk()->count() > 0) {
            return redirect()->back()
                ->withErrors(['error' => 'Kategori tidak bisa dihapus karena masih digunakan produk']);
        }

        DB::beginTransaction();
        try {
            $kategori->delete();

            DB::commit();
            return redirect()->back()->with('success', 'Kategori berhasil dihapus');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * =========================================
     * VALIDASI SESSION AUTH + TENANT
     * =========================================
     */
    private function validateContext()
    {
        if (! Session::get('auth')) {
            abort(401, 'Silakan login terlebih dahulu');
        }

        if (! Session::get('tenant_id')) {
            abort(403, 'Tenant belum dipilih');
        }
    }
}
