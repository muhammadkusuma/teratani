<?php
namespace App\Http\Controllers;

use App\Models\Satuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class SatuanController extends Controller
{
    /**
     * =========================================
     * LIST SATUAN (PER TENANT)
     * =========================================
     */
    public function index()
    {
        $this->validateContext();

        $tenantId = Session::get('tenant_id');

        $satuan = Satuan::where('id_tenant', $tenantId)
            ->orderBy('nama_satuan')
            ->get();

        return view('satuan.index', compact('satuan'));
    }

    /**
     * =========================================
     * SIMPAN SATUAN BARU
     * =========================================
     */
    public function store(Request $request)
    {
        $this->validateContext();

        $request->validate([
            'nama_satuan' => 'required|string|max:20',
        ]);

        /**
         * ======================================
         * CEK DUPLIKASI SATUAN PER TENANT
         * ======================================
         */
        $exists = Satuan::where('id_tenant', Session::get('tenant_id'))
            ->where('nama_satuan', $request->nama_satuan)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withErrors(['nama_satuan' => 'Satuan sudah ada']);
        }

        DB::beginTransaction();
        try {
            Satuan::create([
                'id_tenant'   => Session::get('tenant_id'),
                'nama_satuan' => $request->nama_satuan,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Satuan berhasil ditambahkan');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * =========================================
     * UPDATE SATUAN
     * =========================================
     */
    public function update(Request $request, $id_satuan)
    {
        $this->validateContext();

        $request->validate([
            'nama_satuan' => 'required|string|max:20',
        ]);

        $satuan = Satuan::where('id_satuan', $id_satuan)
            ->where('id_tenant', Session::get('tenant_id'))
            ->first();

        if (! $satuan) {
            abort(403, 'Satuan tidak ditemukan atau tidak memiliki akses');
        }

        /**
         * ======================================
         * CEK DUPLIKASI NAMA SAAT UPDATE
         * ======================================
         */
        $exists = Satuan::where('id_tenant', Session::get('tenant_id'))
            ->where('nama_satuan', $request->nama_satuan)
            ->where('id_satuan', '!=', $id_satuan)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withErrors(['nama_satuan' => 'Nama satuan sudah digunakan']);
        }

        DB::beginTransaction();
        try {
            $satuan->update([
                'nama_satuan' => $request->nama_satuan,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Satuan berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * =========================================
     * HAPUS SATUAN
     * =========================================
     */
    public function destroy($id_satuan)
    {
        $this->validateContext();

        $satuan = Satuan::where('id_satuan', $id_satuan)
            ->where('id_tenant', Session::get('tenant_id'))
            ->first();

        if (! $satuan) {
            abort(403, 'Satuan tidak ditemukan atau tidak memiliki akses');
        }

        /**
         * ======================================
         * CEK JIKA MASIH DIGUNAKAN PRODUK
         * ======================================
         */
        if ($satuan->produk()->count() > 0) {
            return redirect()->back()
                ->withErrors(['error' => 'Satuan tidak bisa dihapus karena masih digunakan produk']);
        }

        DB::beginTransaction();
        try {
            $satuan->delete();

            DB::commit();
            return redirect()->back()->with('success', 'Satuan berhasil dihapus');

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
