@extends('layouts.app')

@section('title', 'Data Satuan')
@section('header', 'Master Satuan')

@section('content')
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="font-bold text-gray-800 text-lg">Daftar Satuan Unit</h3>
            <button onclick="openModal()"
                class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium transition shadow-sm">
                <i class="fas fa-plus mr-2"></i> Tambah Satuan
            </button>
        </div>

        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-50 text-red-600 rounded-lg text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-gray-700 font-semibold uppercase tracking-wider text-xs">
                    <tr>
                        <th class="px-6 py-4 w-16">No</th>
                        <th class="px-6 py-4">Nama Satuan</th>
                        <th class="px-6 py-4 text-center w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($satuans as $index => $item)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 font-medium text-gray-800">{{ $item->nama_satuan }}</td>
                            <td class="px-6 py-4 text-center flex justify-center space-x-2">
                                <button onclick="editModal({{ $item->id_satuan }}, '{{ $item->nama_satuan }}')"
                                    class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('satuan.destroy', $item->id_satuan) }}" method="POST"
                                    onsubmit="return confirm('Hapus satuan ini?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-gray-500">Belum ada data.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div id="modalSatuan" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75" onclick="closeModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <div class="bg-white p-6">
                    <h3 class="text-lg font-medium text-gray-900" id="modalTitle">Tambah Satuan</h3>
                    <form id="formSatuan" method="POST" class="mt-4">
                        @csrf
                        <div id="methodField"></div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Satuan</label>
                        <input type="text" name="nama_satuan" id="inputNama" required placeholder="Pcs, Kg, Liter..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition">
                        <div class="mt-5 flex justify-end space-x-3">
                            <button type="button" onclick="closeModal()"
                                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg">Batal</button>
                            <button type="submit"
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('modalSatuan').classList.remove('hidden');
            document.getElementById('formSatuan').action = "{{ route('satuan.store') }}";
            document.getElementById('modalTitle').innerText = "Tambah Satuan";
            document.getElementById('inputNama').value = "";
            document.getElementById('methodField').innerHTML = "";
        }

        function editModal(id, nama) {
            document.getElementById('modalSatuan').classList.remove('hidden');
            let url = "{{ route('satuan.update', ':id') }}".replace(':id', id);
            document.getElementById('formSatuan').action = url;
            document.getElementById('modalTitle').innerText = "Edit Satuan";
            document.getElementById('inputNama').value = nama;
            document.getElementById('methodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        }

        function closeModal() {
            document.getElementById('modalSatuan').classList.add('hidden');
        }
    </script>
@endsection
