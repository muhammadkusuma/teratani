<?php
namespace App\Http\Controllers;

use App\Models\Toko;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class TokoController extends Controller
{
    /**
     * =========================================
     * LIST TOKO (PER TENANT)
     * =========================================
     */
    public function index()
    {
        $this->validateContext();

        $tenantId = Session::get('tenant_id');

        $toko = Toko::where('id_tenant', $tenantId)
            ->orderBy('is_active', 'desc')
            ->orderBy('nama_toko')
            ->get();

        return view('toko.index', compact('toko'));
    }

    /**
     * =========================================
     * FORM TAMBAH TOKO
     * =========================================
     */
    public function create()
    {
        $this->validateContext();
        return view('toko.create');
    }

    /**
     * =========================================
     * SIMPAN TOKO BARU
     * =========================================
     */
    public function store(Request $request)
    {
        $this->validateContext();

        $request->validate([
            'nama_toko' => 'required|string|max:150',
            'alamat'    => 'nullable|string',
            'telepon'   => 'nullable|string|max:30',
        ]);

        DB::beginTransaction();
        try {
            Toko::create([
                'id_tenant' => Session::get('tenant_id'),
                'nama_toko' => $request->nama_toko,
                'alamat'    => $request->alamat,
                'telepon'   => $request->telepon,
                'is_active' => true,
            ]);

            DB::commit();
            return redirect()->route('toko.index')
                ->with('success', 'Toko berhasil ditambahkan');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * =========================================
     * FORM EDIT TOKO
     * =========================================
     */
    public function edit($id_toko)
    {
        $this->validateContext();

        $toko = Toko::where('id_toko', $id_toko)
            ->where('id_tenant', Session::get('tenant_id'))
            ->first();

        if (! $toko) {
            abort(403, 'Toko tidak ditemukan atau tidak memiliki akses');
        }

        return view('toko.edit', compact('toko'));
    }

    /**
     * =========================================
     * UPDATE TOKO
     * =========================================
     */
    public function update(Request $request, $id_toko)
    {
        $this->validateContext();

        $toko = Toko::where('id_toko', $id_toko)
            ->where('id_tenant', Session::get('tenant_id'))
            ->first();

        if (! $toko) {
            abort(403, 'Toko tidak ditemukan atau tidak memiliki akses');
        }

        $request->validate([
            'nama_toko' => 'required|string|max:150',
            'alamat'    => 'nullable|string',
            'telepon'   => 'nullable|string|max:30',
            'is_active' => 'required|boolean',
        ]);

        /**
         * ======================================
         * MINIMAL 1 TOKO AKTIF
         * ======================================
         */
        if ($toko->is_active && ! $request->is_active) {
            $activeCount = Toko::where('id_tenant', Session::get('tenant_id'))
                ->where('is_active', true)
                ->count();

            if ($activeCount <= 1) {
                return redirect()->back()
                    ->withErrors(['error' => 'Minimal harus ada 1 toko aktif']);
            }
        }

        DB::beginTransaction();
        try {
            $toko->update([
                'nama_toko' => $request->nama_toko,
                'alamat'    => $request->alamat,
                'telepon'   => $request->telepon,
                'is_active' => $request->is_active,
            ]);

            DB::commit();
            return redirect()->route('toko.index')
                ->with('success', 'Toko berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * =========================================
     * HAPUS TOKO
     * =========================================
     */
    public function destroy($id_toko)
    {
        $this->validateContext();

        $toko = Toko::where('id_toko', $id_toko)
            ->where('id_tenant', Session::get('tenant_id'))
            ->first();

        if (! $toko) {
            abort(403, 'Toko tidak ditemukan atau tidak memiliki akses');
        }

        /**
         * ======================================
         * CEK TOKO AKTIF TERAKHIR
         * ======================================
         */
        if ($toko->is_active) {
            $activeCount = Toko::where('id_tenant', Session::get('tenant_id'))
                ->where('is_active', true)
                ->count();

            if ($activeCount <= 1) {
                return redirect()->back()
                    ->withErrors(['error' => 'Tidak bisa menghapus toko aktif terakhir']);
            }
        }

        DB::beginTransaction();
        try {
            $toko->delete();

            DB::commit();
            return redirect()->route('toko.index')
                ->with('success', 'Toko berhasil dihapus');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()]);
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
