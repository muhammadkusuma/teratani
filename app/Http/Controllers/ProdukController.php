<?php
namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Produk;
use App\Models\Satuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ProdukController extends Controller
{
    /**
     * =========================================
     * LIST PRODUK (PER TENANT)
     * =========================================
     */
    public function index()
    {
        $this->validateContext();

        $tenantId = Session::get('tenant_id');

        $produk = Produk::with(['kategori', 'satuan'])
            ->where('id_tenant', $tenantId)
            ->orderBy('nama_produk')
            ->get();

        return view('produk.index', compact('produk'));
    }

    /**
     * =========================================
     * FORM TAMBAH PRODUK
     * =========================================
     */
    public function create()
    {
        $this->validateContext();

        $tenantId = Session::get('tenant_id');

        $kategori = Kategori::where('id_tenant', $tenantId)->orderBy('nama_kategori')->get();
        $satuan   = Satuan::where('id_tenant', $tenantId)->orderBy('nama_satuan')->get();

        return view('produk.create', compact('kategori', 'satuan'));
    }

    /**
     * =========================================
     * SIMPAN PRODUK BARU
     * =========================================
     */
    public function store(Request $request)
    {
        $this->validateContext();

        $request->validate([
            'nama_produk'         => 'required|string|max:150',
            'id_kategori'         => 'nullable|integer',
            'id_satuan'           => 'nullable|integer',
            'sku'                 => 'nullable|string|max:50',
            'barcode'             => 'nullable|string|max:100',
            'harga_pokok_standar' => 'nullable|numeric|min:0',
            'berat_gram'          => 'nullable|integer|min:0',
            'gambar_produk'       => 'nullable|image|max:2048',
        ]);

        $tenantId = Session::get('tenant_id');

        /**
         * ======================================
         * CEK SKU UNIK PER TENANT
         * ======================================
         */
        if ($request->sku) {
            $exists = Produk::where('id_tenant', $tenantId)
                ->where('sku', $request->sku)
                ->exists();

            if ($exists) {
                return redirect()->back()
                    ->withErrors(['sku' => 'SKU sudah digunakan'])
                    ->withInput();
            }
        }

        /**
         * ======================================
         * UPLOAD GAMBAR (JIKA ADA)
         * ======================================
         */
        $gambarPath = null;
        if ($request->hasFile('gambar_produk')) {
            $gambarPath = $request->file('gambar_produk')
                ->store('produk', 'public');
        }

        DB::beginTransaction();
        try {
            Produk::create([
                'id_tenant'           => $tenantId,
                'id_kategori'         => $request->id_kategori,
                'id_satuan'           => $request->id_satuan,
                'sku'                 => $request->sku,
                'barcode'             => $request->barcode,
                'nama_produk'         => $request->nama_produk,
                'deskripsi'           => $request->deskripsi,
                'harga_pokok_standar' => $request->harga_pokok_standar ?? 0,
                'berat_gram'          => $request->berat_gram ?? 0,
                'gambar_produk'       => $gambarPath,
                'is_active'           => true,
            ]);

            DB::commit();
            return redirect()->route('produk.index')
                ->with('success', 'Produk berhasil ditambahkan');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * =========================================
     * FORM EDIT PRODUK
     * =========================================
     */
    public function edit($id_produk)
    {
        $this->validateContext();

        $tenantId = Session::get('tenant_id');

        $produk = Produk::where('id_produk', $id_produk)
            ->where('id_tenant', $tenantId)
            ->first();

        if (! $produk) {
            abort(403, 'Produk tidak ditemukan atau tidak memiliki akses');
        }

        $kategori = Kategori::where('id_tenant', $tenantId)->orderBy('nama_kategori')->get();
        $satuan   = Satuan::where('id_tenant', $tenantId)->orderBy('nama_satuan')->get();

        return view('produk.edit', compact('produk', 'kategori', 'satuan'));
    }

    /**
     * =========================================
     * UPDATE PRODUK
     * =========================================
     */
    public function update(Request $request, $id_produk)
    {
        $this->validateContext();

        $tenantId = Session::get('tenant_id');

        $produk = Produk::where('id_produk', $id_produk)
            ->where('id_tenant', $tenantId)
            ->first();

        if (! $produk) {
            abort(403, 'Produk tidak ditemukan atau tidak memiliki akses');
        }

        $request->validate([
            'nama_produk'         => 'required|string|max:150',
            'id_kategori'         => 'nullable|integer',
            'id_satuan'           => 'nullable|integer',
            'sku'                 => 'nullable|string|max:50',
            'barcode'             => 'nullable|string|max:100',
            'harga_pokok_standar' => 'nullable|numeric|min:0',
            'berat_gram'          => 'nullable|integer|min:0',
            'gambar_produk'       => 'nullable|image|max:2048',
        ]);

        /**
         * ======================================
         * CEK SKU UNIK SAAT UPDATE
         * ======================================
         */
        if ($request->sku) {
            $exists = Produk::where('id_tenant', $tenantId)
                ->where('sku', $request->sku)
                ->where('id_produk', '!=', $id_produk)
                ->exists();

            if ($exists) {
                return redirect()->back()
                    ->withErrors(['sku' => 'SKU sudah digunakan'])
                    ->withInput();
            }
        }

        /**
         * ======================================
         * UPLOAD GAMBAR BARU (JIKA ADA)
         * ======================================
         */
        $gambarPath = $produk->gambar_produk;
        if ($request->hasFile('gambar_produk')) {
            $gambarPath = $request->file('gambar_produk')
                ->store('produk', 'public');
        }

        DB::beginTransaction();
        try {
            $produk->update([
                'id_kategori'         => $request->id_kategori,
                'id_satuan'           => $request->id_satuan,
                'sku'                 => $request->sku,
                'barcode'             => $request->barcode,
                'nama_produk'         => $request->nama_produk,
                'deskripsi'           => $request->deskripsi,
                'harga_pokok_standar' => $request->harga_pokok_standar ?? 0,
                'berat_gram'          => $request->berat_gram ?? 0,
                'gambar_produk'       => $gambarPath,
            ]);

            DB::commit();
            return redirect()->route('produk.index')
                ->with('success', 'Produk berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * =========================================
     * AKTIF / NONAKTIF PRODUK
     * =========================================
     */
    public function toggleActive($id_produk)
    {
        $this->validateContext();

        $produk = Produk::where('id_produk', $id_produk)
            ->where('id_tenant', Session::get('tenant_id'))
            ->first();

        if (! $produk) {
            abort(403, 'Produk tidak ditemukan atau tidak memiliki akses');
        }

        $produk->update([
            'is_active' => ! $produk->is_active,
        ]);

        return redirect()->back()
            ->with('success', 'Status produk berhasil diubah');
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
