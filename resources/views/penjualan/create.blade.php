@extends('layouts.app')

@section('title', 'Kasir (POS)')
@section('header', 'Point of Sale')

@section('content')
    <div class="flex flex-col lg:flex-row h-[calc(100vh-140px)] gap-6">

        <div class="lg:w-2/3 flex flex-col bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 border-b border-gray-100 flex gap-4">
                <div class="relative flex-1">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    <input type="text" id="searchProduct" placeholder="Cari nama barang atau scan SKU..."
                        class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:bg-white focus:ring-2 focus:ring-green-500 outline-none transition">
                </div>
                <select
                    class="px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-600 focus:ring-2 focus:ring-green-500 outline-none">
                    <option value="">Semua Kategori</option>
                    @foreach ($kategoris as $k)
                        <option value="{{ $k->id_kategori }}">{{ $k->nama_kategori }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex-1 overflow-y-auto p-4 bg-gray-50">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4" id="productGrid">
                    @foreach ($produks as $produk)
                        <div class="bg-white p-3 rounded-xl shadow-sm border border-gray-100 hover:shadow-md cursor-pointer transition flex flex-col h-full product-item"
                            onclick="addToCart({{ $produk->id_produk }}, '{{ $produk->nama_produk }}', {{ $produk->stok_toko->harga_jual_cabang ?? 0 }})">

                            <div
                                class="h-24 bg-green-50 rounded-lg mb-3 flex items-center justify-center text-green-600 text-3xl">
                                <i class="fas fa-box"></i>
                            </div>

                            <h4 class="font-bold text-gray-800 text-sm leading-tight mb-1">{{ $produk->nama_produk }}</h4>
                            <p class="text-xs text-gray-500 mb-2">{{ $produk->sku }}</p>

                            <div class="mt-auto flex justify-between items-end">
                                <span class="font-bold text-green-600 text-sm">Rp
                                    {{ number_format($produk->stok_toko->harga_jual_cabang ?? 0, 0, ',', '.') }}</span>
                                <span class="text-xs bg-gray-100 text-gray-600 px-1.5 py-0.5 rounded">Stok:
                                    {{ $produk->stok_toko->stok_fisik ?? 0 }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="lg:w-1/3 flex flex-col bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 bg-slate-800 text-white flex justify-between items-center">
                <h3 class="font-bold"><i class="fas fa-shopping-cart mr-2"></i> Keranjang</h3>
                <button onclick="clearCart()" class="text-xs text-red-300 hover:text-white transition">Reset</button>
            </div>

            <div class="flex-1 overflow-y-auto p-4 space-y-3" id="cartItems">
                <div class="text-center text-gray-400 mt-10 empty-cart-msg">
                    <i class="fas fa-basket-shopping text-4xl mb-2"></i>
                    <p class="text-sm">Keranjang kosong</p>
                </div>
            </div>

            <div class="p-4 bg-gray-50 border-t border-gray-200">
                <div class="mb-4">
                    <select name="id_pelanggan" id="pelangganSelect"
                        class="w-full p-2 text-sm border border-gray-300 rounded-lg focus:ring-green-500">
                        <option value="">-- Tamu / Umum --</option>
                        @foreach ($pelanggans as $p)
                            <option value="{{ $p->id_pelanggan }}">{{ $p->nama_pelanggan }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-2 mb-4 text-sm">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal</span>
                        <span id="subtotalDisplay">Rp 0</span>
                    </div>
                    <div class="flex justify-between font-bold text-lg text-gray-800">
                        <span>Total</span>
                        <span id="totalDisplay">Rp 0</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2 mb-4">
                    <button type="button" onclick="setPayment('Tunai')"
                        class="payment-btn active bg-green-100 border-green-500 text-green-700 py-2 rounded-lg text-sm font-bold border">Tunai</button>
                    <button type="button" onclick="setPayment('Piutang')"
                        class="payment-btn bg-white border-gray-300 text-gray-600 py-2 rounded-lg text-sm font-bold border hover:bg-gray-50">Hutang</button>
                </div>

                <form action="{{ route('penjualan.store') }}" method="POST" id="checkoutForm">
                    @csrf
                    <input type="hidden" name="cart_data" id="cartDataInput">
                    <input type="hidden" name="total_netto" id="totalNettoInput">
                    <input type="hidden" name="metode_bayar" id="metodeBayarInput" value="Tunai">
                    <button type="button" onclick="submitTransaction()"
                        class="w-full py-3 bg-slate-800 hover:bg-slate-900 text-white font-bold rounded-xl shadow-lg transition flex justify-center items-center">
                        <i class="fas fa-print mr-2"></i> Proses Bayar
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        let cart = [];

        function addToCart(id, name, price) {
            let existingItem = cart.find(item => item.id === id);
            if (existingItem) {
                existingItem.qty++;
            } else {
                cart.push({
                    id: id,
                    name: name,
                    price: price,
                    qty: 1
                });
            }
            updateCartUI();
        }

        function updateCartUI() {
            const container = document.getElementById('cartItems');
            const emptyMsg = document.querySelector('.empty-cart-msg');
            container.innerHTML = '';

            if (cart.length === 0) {
                if (emptyMsg) container.appendChild(emptyMsg); // Re-append empty msg logic simplified
                container.innerHTML =
                    `<div class="text-center text-gray-400 mt-10"><i class="fas fa-basket-shopping text-4xl mb-2"></i><p class="text-sm">Keranjang kosong</p></div>`;
                document.getElementById('subtotalDisplay').innerText = 'Rp 0';
                document.getElementById('totalDisplay').innerText = 'Rp 0';
                return;
            }

            let total = 0;

            cart.forEach((item, index) => {
                let subtotal = item.price * item.qty;
                total += subtotal;

                let html = `
                <div class="flex justify-between items-center bg-white p-2 rounded-lg border border-gray-100">
                    <div class="flex-1">
                        <h5 class="text-sm font-bold text-gray-800">${item.name}</h5>
                        <p class="text-xs text-gray-500">Rp ${item.price.toLocaleString('id-ID')}</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button onclick="changeQty(${index}, -1)" class="w-6 h-6 bg-gray-100 rounded text-gray-600 hover:bg-red-100 hover:text-red-600 font-bold">-</button>
                        <span class="text-sm font-bold w-4 text-center">${item.qty}</span>
                        <button onclick="changeQty(${index}, 1)" class="w-6 h-6 bg-gray-100 rounded text-gray-600 hover:bg-green-100 hover:text-green-600 font-bold">+</button>
                    </div>
                </div>
            `;
                container.innerHTML += html;
            });

            document.getElementById('subtotalDisplay').innerText = 'Rp ' + total.toLocaleString('id-ID');
            document.getElementById('totalDisplay').innerText = 'Rp ' + total.toLocaleString('id-ID');
            document.getElementById('totalNettoInput').value = total;
        }

        function changeQty(index, change) {
            cart[index].qty += change;
            if (cart[index].qty <= 0) {
                cart.splice(index, 1);
            }
            updateCartUI();
        }

        function clearCart() {
            cart = [];
            updateCartUI();
        }

        function setPayment(method) {
            document.getElementById('metodeBayarInput').value = method;
            document.querySelectorAll('.payment-btn').forEach(btn => {
                btn.classList.remove('bg-green-100', 'border-green-500', 'text-green-700');
                btn.classList.add('bg-white', 'text-gray-600');
            });

            // Highlight active
            const btn = event.target;
            btn.classList.remove('bg-white', 'text-gray-600');
            btn.classList.add('bg-green-100', 'border-green-500', 'text-green-700');
        }

        function submitTransaction() {
            if (cart.length === 0) {
                alert('Keranjang masih kosong!');
                return;
            }
            // Prepare data
            document.getElementById('cartDataInput').value = JSON.stringify(cart);

            // Include Pelanggan ID manually to form
            const pelangganSelect = document.getElementById('pelangganSelect');
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'id_pelanggan';
            hiddenInput.value = pelangganSelect.value;
            document.getElementById('checkoutForm').appendChild(hiddenInput);

            document.getElementById('checkoutForm').submit();
        }
    </script>
@endsection
