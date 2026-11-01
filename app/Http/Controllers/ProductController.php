<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    private function getFilteredQuery(Request $request)
    {
        $search = $request->query('search');
        return Product::when($search, function ($query, $search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
        })->latest();
    }

    public function index(Request $request)
    {
        $search = $request->query('search');
        $products = $this->getFilteredQuery($request)->paginate(10)->withQueryString();

        $stats = [
            'total_items' => Product::count(),
            'total_stock' => Product::sum('stock'),
            'total_value' => Product::selectRaw('SUM(stock * price) as val')->value('val') ?? 0,
        ];

        return view('products.index', compact('products', 'stats', 'search'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sku'         => 'required|string|max:50|unique:products,sku',
            'name'        => 'required|string|max:255',
            'category'    => 'required|string|max:100',
            'stock'       => 'required|integer|min:0',
            'price'       => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        Product::create($validated);
        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'sku'         => 'required|string|max:50|unique:products,sku,' . $product->id,
            'name'        => 'required|string|max:255',
            'category'    => 'required|string|max:100',
            'stock'       => 'required|integer|min:0',
            'price'       => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $product->update($validated);
        return redirect()->route('products.index')->with('success', 'Data produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus.');
    }

    public function exportExcel(Request $request)
    {
        $products = $this->getFilteredQuery($request)->get();
        $filename = 'laporan-inventaris-' . now()->format('Y-m-d-His') . '.xls';

        return response()->streamDownload(function () use ($products) {
            echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
            echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head>';
            echo '<body>';
            echo '<table border="1">';
            echo '<tr style="background-color:#4f46e5; color:#ffffff; font-weight:bold;">
                    <th>No</th>
                    <th>SKU</th>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th>Stok</th>
                    <th>Harga Satuan (Rp)</th>
                    <th>Total Nilai (Rp)</th>
                    <th>Deskripsi</th>
                  </tr>';

            foreach ($products as $index => $p) {
                $subtotal = $p->stock * $p->price;
                echo '<tr>
                        <td>' . ($index + 1) . '</td>
                        <td>' . e($p->sku) . '</td>
                        <td>' . e($p->name) . '</td>
                        <td>' . e($p->category) . '</td>
                        <td>' . $p->stock . '</td>
                        <td>' . $p->price . '</td>
                        <td>' . $subtotal . '</td>
                        <td>' . e($p->description) . '</td>
                      </tr>';
            }
            echo '</table></body></html>';
        }, $filename, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
        ]);
    }

    public function exportPdf(Request $request)
    {
        $products = $this->getFilteredQuery($request)->get();
        $totalValue = $products->sum(fn($item) => $item->stock * $item->price);

        $pdf = Pdf::loadView('products.pdf', compact('products', 'totalValue'))
                  ->setPaper('a4', 'landscape');

        return $pdf->download('laporan-inventaris-' . now()->format('Y-m-d-His') . '.pdf');
    }
}