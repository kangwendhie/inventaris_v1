<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Inventaris Produk</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #1e293b; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #4f46e5; padding-bottom: 10px; }
        .header h2 { margin: 0; color: #4f46e5; }
        .header p { margin: 4px 0 0; color: #64748b; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #cbd5e1; padding: 8px; text-align: left; }
        th { background-color: #f1f5f9; font-weight: bold; color: #334155; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .footer { margin-top: 15px; text-align: right; font-weight: bold; font-size: 13px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN INVENTARIS PRODUK</h2>
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i') }} | Oleh: {{ auth()->user()->name ?? 'Admin' }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" width="4%">No</th>
                <th width="12%">SKU</th>
                <th width="25%">Nama Produk</th>
                <th width="14%">Kategori</th>
                <th class="text-right" width="8%">Stok</th>
                <th class="text-right" width="17%">Harga Satuan</th>
                <th class="text-right" width="20%">Subtotal Nilai</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $i => $product)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td>{{ $product->sku }}</td>
                <td>{{ $product->name }}</td>
                <td>{{ $product->category }}</td>
                <td class="text-right">{{ $product->stock }}</td>
                <td class="text-right">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($product->stock * $product->price, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Total Estimasi Nilai Inventaris: Rp {{ number_format($totalValue, 0, ',', '.') }}
    </div>
</body>
</html>