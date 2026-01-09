@extends('layouts.app')

@section('title', 'Input Pembelian')
@section('header', 'Belanja Stok Masuk')

@section('content')
    <form action="{{ route('pembelian.store') }}" method="POST" id="formPembelian">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-1 bg-white rounded-xl shadow-sm border border-gray-100 p-6 h-fit">
                <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">Info Faktur</h3>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">No Faktur (Dari Suplier)</label>
                        <input type="text" name="no_faktur" required class="w-full px-3 py-2 border rounded-lg">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Distributor</label>
                        <select name="id_distributor" required class="w-full px-3 py-2 border rounded-lg">
                            <option value="">-- Pilih Distributor --</option>
                            @foreach ($distributors as $d)
                                <option value="{{ $d->id_distributor }}">{{ $d->nama_distributor }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pembelian</label>
                        <input type="date" name="tgl_pembelian" value="{{ date('Y-m-d') }}" required
                            class="w-full px-3 py-2 border rounded-lg">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status Pembayaran</label>
                        <select name="status_bayar" class="w-full px-3 py-2 border rounded-lg">
                            <option value="Lunas">Lunas (Tunai)</option>
                            <option value="Hutang">Hutang (Tempo)</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-bold text-gray-800">Daftar Barang</h3>
                    <button type="button" onclick="addRow()"
                        class="text-sm bg-blue-50 text-blue-600 px-3 py-1 rounded hover:bg-blue-100 font-bold">
                        + Tambah Baris
                    </button>
                </div>

                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left">
                            <th class="p-2 rounded-l">Produk</th>
                            <th class="p-2 w-24">Qty</th>
                            <th class="p-2 w-32">Harga Beli</th>
                            <th class="p-2 w-32 text-right">Subtotal</th>
                            <th class="p-2 w-10"></th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody">
                        <tr>
                            <td class="p-2">
                                <select name="items[0][id_produk]" class="w-full border rounded p-1" required
                                    onchange="updatePrice(this, 0)">
                                    <option value="">-- Pilih --</option>
                                    @foreach ($produks as $p)
                                        <option value="{{ $p->id_produk }}" data-harga="{{ $p->harga_pokok }}">
                                            {{ $p->nama_produk }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="p-2">
                                <input type="number" name="items[0][qty]" min="1" value="1"
                                    class="w-full border rounded p-1 text-center" oninput="calculateRow(0)">
                            </td>
                            <td class="p-2">
                                <input type="number" name="items[0][harga_beli]" class="w-full border rounded p-1"
                                    oninput="calculateRow(0)">
                            </td>
                            <td class="p-2 text-right font-bold text-gray-700 subtotal-display-0">0</td>
                            <td class="p-2 text-center"></td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="border-t">
                            <td colspan="3" class="p-2 text-right font-bold text-lg">Total Akhir</td>
                            <td class="p-2 text-right font-bold text-lg text-green-600" id="grandTotal">0</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>

                <div class="mt-6 text-right">
                    <button type="submit"
                        class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg shadow">
                        Simpan Pembelian
                    </button>
                </div>
            </div>
        </div>
    </form>

    <script>
        let rowCount = 1;

        function addRow() {
            const tbody = document.getElementById('itemsBody');
            const index = rowCount;

            const html = `
            <tr>
                <td class="p-2">
                    <select name="items[${index}][id_produk]" class="w-full border rounded p-1" required onchange="updatePrice(this, ${index})">
                        <option value="">-- Pilih --</option>
                        @foreach ($produks as $p)
                            <option value="{{ $p->id_produk }}" data-harga="{{ $p->harga_pokok }}">{{ $p->nama_produk }}</option>
                        @endforeach
                    </select>
                </td>
                <td class="p-2">
                    <input type="number" name="items[${index}][qty]" min="1" value="1" class="w-full border rounded p-1 text-center" oninput="calculateRow(${index})">
                </td>
                <td class="p-2">
                    <input type="number" name="items[${index}][harga_beli]" class="w-full border rounded p-1" oninput="calculateRow(${index})">
                </td>
                <td class="p-2 text-right font-bold text-gray-700 subtotal-display-${index}">0</td>
                <td class="p-2 text-center">
                    <button type="button" onclick="removeRow(this)" class="text-red-500 hover:text-red-700"><i class="fas fa-times"></i></button>
                </td>
            </tr>
        `;

            tbody.insertAdjacentHTML('beforeend', html);
            rowCount++;
        }

        function removeRow(btn) {
            btn.closest('tr').remove();
            calculateGrandTotal();
        }

        function updatePrice(select, index) {
            const price = select.options[select.selectedIndex].getAttribute('data-harga');
            const inputHarga = document.querySelector(`input[name="items[${index}][harga_beli]"]`);
            if (inputHarga) {
                inputHarga.value = price;
                calculateRow(index);
            }
        }

        function calculateRow(index) {
            const qty = document.querySelector(`input[name="items[${index}][qty]"]`).value;
            const harga = document.querySelector(`input[name="items[${index}][harga_beli]"]`).value;
            const subtotal = qty * harga;

            document.querySelector(`.subtotal-display-${index}`).innerText = subtotal.toLocaleString('id-ID');
            calculateGrandTotal();
        }

        function calculateGrandTotal() {
            let total = 0;
            // Loop semua input qty dan harga
            const rows = document.querySelectorAll('#itemsBody tr');
            rows.forEach((row, i) => {
                const qtyInput = row.querySelector('input[name*="[qty]"]');
                const hargaInput = row.querySelector('input[name*="[harga_beli]"]');
                if (qtyInput && hargaInput) {
                    total += (qtyInput.value * hargaInput.value);
                }
            });
            document.getElementById('grandTotal').innerText = total.toLocaleString('id-ID');
        }
    </script>
@endsection
