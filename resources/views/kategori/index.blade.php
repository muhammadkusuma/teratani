@extends('layouts.app')

@section('title', 'Data Kategori')
@section('header', 'Master Kategori')

@section('content')
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="font-bold text-gray-800 text-lg">Daftar Kategori Produk</h3>
            <button onclick="openModal('add')"
                class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium transition shadow-sm flex items-center">
                <i class="fas fa-plus mr-2"></i> Tambah Kategori
            </button>
        </div>

        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-50 text-red-600 rounded-lg text-sm">
                <ul class="list-disc pl-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-gray-700 font-semibold uppercase tracking-wider text-xs">
                    <tr>
                        <th class="px-6 py-4 w-16">No</th>
                        <th class="px-6 py-4">Nama Kategori</th>
                        <th class="px-6 py-4 text-center w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($kategoris as $index => $item)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 font-medium text-gray-800">{{ $item->nama_kategori }}</td>
                            <td class="px-6 py-4 text-center flex justify-center space-x-2">
                                <button onclick="editModal({{ $item->id_kategori }}, '{{ $item->nama_kategori }}')"
                                    class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('kategori.destroy', $item->id_kategori) }}" method="POST"
                                    onsubmit="return confirm('Hapus kategori ini?');">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-gray-500">Belum ada kategori.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div id="modalKategori" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
                onclick="closeModal()"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modalTitle">Tambah Kategori</h3>
                    <div class="mt-4">
                        <form id="formKategori" method="POST">
                            @csrf
                            <div id="methodField"></div> <label class="block text-sm font-medium text-gray-700 mb-1">Nama
                                Kategori</label>
                            <input type="text" name="nama_kategori" id="inputNama" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition">

                            <div class="mt-5 flex justify-end space-x-3">
                                <button type="button" onclick="closeModal()"
                                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">Batal</button>
                                <button type="submit"
                                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openModal(mode) {
            document.getElementById('modalKategori').classList.remove('hidden');
            document.getElementById('formKategori').action = "{{ route('kategori.store') }}";
            document.getElementById('modalTitle').innerText = "Tambah Kategori";
            document.getElementById('inputNama').value = "";
            document.getElementById('methodField').innerHTML = "";
        }

        function editModal(id, nama) {
            document.getElementById('modalKategori').classList.remove('hidden');
            // Set action URL untuk Update
            let url = "{{ route('kategori.update', ':id') }}";
            url = url.replace(':id', id);
            document.getElementById('formKategori').action = url;

            document.getElementById('modalTitle').innerText = "Edit Kategori";
            document.getElementById('inputNama').value = nama;
            // Tambahkan @method('PUT')
            document.getElementById('methodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        }

        function closeModal() {
            document.getElementById('modalKategori').classList.add('hidden');
        }
    </script>
@endsection
