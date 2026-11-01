<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Inventaris - Laravel Starter Kit</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen">

    <nav class="bg-indigo-600 text-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 py-4 flex flex-wrap justify-between items-center gap-4">
            <div class="font-bold text-xl tracking-tight">
                InventarisApp <span class="text-xs bg-indigo-800 px-2 py-1 rounded ml-2">Admin Panel</span>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-sm text-indigo-100 hidden sm:inline">Halo, <strong>{{ auth()->user()->name }}</strong></span>
                <button onclick="openCreateModal()" class="bg-white text-indigo-600 hover:bg-indigo-50 font-semibold px-4 py-2 rounded-lg text-sm shadow transition">
                    + Tambah Produk
                </button>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="bg-indigo-800 hover:bg-red-600 text-white px-3.5 py-2 rounded-lg text-sm font-medium transition">
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 py-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                <p class="text-sm text-slate-500">Total Jenis Produk</p>
                <p class="text-2xl font-bold text-slate-800 mt-1">{{ number_format($stats['total_items'], 0, ',', '.') }} Item</p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                <p class="text-sm text-slate-500">Total Stok Tersedia</p>
                <p class="text-2xl font-bold text-indigo-600 mt-1">{{ number_format($stats['total_stock'], 0, ',', '.') }} Unit</p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                <p class="text-sm text-slate-500">Estimasi Nilai Inventaris</p>
                <p class="text-2xl font-bold text-emerald-600 mt-1">Rp {{ number_format($stats['total_value'], 0, ',', '.') }}</p>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-300 text-emerald-800 px-4 py-3 rounded-lg mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-50 border border-red-300 text-red-800 px-4 py-3 rounded-lg mb-6">
                <ul class="list-disc pl-5 text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-4 border-b border-slate-200 flex flex-col sm:flex-row justify-between items-center gap-4">
                <form method="GET" action="{{ route('products.index') }}" class="flex w-full sm:w-96 gap-2">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari SKU, nama, atau kategori..." 
                           class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <button type="submit" class="bg-slate-800 text-white px-4 py-2 rounded-lg text-sm hover:bg-slate-700">Cari</button>
                </form>

                <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                    <a href="{{ route('products.export.excel', ['search' => $search]) }}" 
                       class="bg-emerald-600 hover:bg-emerald-700 text-white px-3.5 py-2 rounded-lg text-sm font-medium transition shadow-sm">
                        Export Excel (.xls)
                    </a>
                    <a href="{{ route('products.export.pdf', ['search' => $search]) }}" 
                       class="bg-rose-600 hover:bg-rose-700 text-white px-3.5 py-2 rounded-lg text-sm font-medium transition shadow-sm">
                        Export PDF (.pdf)
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-100 text-slate-600 text-xs uppercase tracking-wider">
                            <th class="p-4">SKU</th>
                            <th class="p-4">Nama Produk</th>
                            <th class="p-4">Kategori</th>
                            <th class="p-4 text-right">Stok</th>
                            <th class="p-4 text-right">Harga Satuan</th>
                            <th class="p-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-sm">
                        @forelse($products as $product)
                        <tr class="hover:bg-slate-50">
                            <td class="p-4 font-mono text-xs font-semibold text-indigo-600">{{ $product->sku }}</td>
                            <td class="p-4">
                                <div class="font-medium text-slate-900">{{ $product->name }}</div>
                                <div class="text-xs text-slate-500">{{ $product->description }}</div>
                            </td>
                            <td class="p-4"><span class="bg-slate-100 text-slate-700 px-2.5 py-1 rounded-full text-xs">{{ $product->category }}</span></td>
                            <td class="p-4 text-right font-semibold">{{ $product->stock }}</td>
                            <td class="p-4 text-right">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                            <td class="p-4 text-center space-x-2">
                                <button onclick='openEditModal(@json($product))' class="text-amber-600 hover:underline font-medium">Edit</button>
                                <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Hapus produk ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline font-medium">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-500">Belum ada data produk ditemukan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-200">
                {{ $products->links() }}
            </div>
        </div>
    </main>

    <div id="productModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center p-4 z-50">
        <div class="bg-white rounded-xl max-w-lg w-full p-6 shadow-xl">
            <h2 id="modalTitle" class="text-lg font-bold mb-4">Tambah Produk Baru</h2>
            <form id="productForm" method="POST" action="{{ route('products.store') }}">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-600 mb-1">Kode SKU</label>
                        <input type="text" name="sku" id="sku" required class="w-full border rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-600 mb-1">Kategori</label>
                        <input type="text" name="category" id="category" required class="w-full border rounded-lg px-3 py-2 text-sm">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-semibold uppercase text-slate-600 mb-1">Nama Produk</label>
                    <input type="text" name="name" id="name" required class="w-full border rounded-lg px-3 py-2 text-sm">
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-600 mb-1">Jumlah Stok</label>
                        <input type="number" name="stock" id="stock" min="0" required class="w-full border rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-600 mb-1">Harga (Rp)</label>
                        <input type="number" name="price" id="price" min="0" required class="w-full border rounded-lg px-3 py-2 text-sm">
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-xs font-semibold uppercase text-slate-600 mb-1">Deskripsi</label>
                    <textarea name="description" id="description" rows="2" class="w-full border rounded-lg px-3 py-2 text-sm"></textarea>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 text-sm text-slate-600 hover:bg-slate-100 rounded-lg">Batal</button>
                    <button type="submit" class="px-4 py-2 text-sm bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById('productModal');
        const form = document.getElementById('productForm');
        const methodInput = document.getElementById('formMethod');
        const title = document.getElementById('modalTitle');

        function openCreateModal() {
            title.innerText = 'Tambah Produk Baru';
            form.action = "{{ route('products.store') }}";
            methodInput.value = 'POST';
            form.reset();
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function openEditModal(product) {
            title.innerText = 'Edit Produk: ' + product.sku;
            form.action = "/products/" + product.id;
            methodInput.value = 'PUT';
            document.getElementById('sku').value = product.sku;
            document.getElementById('name').value = product.name;
            document.getElementById('category').value = product.category;
            document.getElementById('stock').value = product.stock;
            document.getElementById('price').value = parseFloat(product.price);
            document.getElementById('description').value = product.description || '';
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeModal() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    </script>
</body>
</html>