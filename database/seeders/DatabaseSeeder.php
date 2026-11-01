<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Akun Super Admin
        User::updateOrCreate(
            ['email' => 'admin@inventaris.com'],
            [
                'name'     => 'Super Administrator',
                'role'     => 'super-admin',
                'password' => Hash::make('Admin123!'),
            ]
        );

        // Akun Staf Gudang
        User::updateOrCreate(
            ['email' => 'staff@inventaris.com'],
            [
                'name'     => 'Staf Gudang',
                'role'     => 'staff',
                'password' => Hash::make('Staff123!'),
            ]
        );

        $samples = [
            ['sku' => 'PRD-001', 'name' => 'Laptop ThinkPad X1 Carbon', 'category' => 'Elektronik', 'stock' => 12, 'price' => 24500000, 'description' => 'Laptop bisnis ringan dengan prosesor Intel Core Ultra.'],
            ['sku' => 'PRD-002', 'name' => 'Monitor Dell UltraSharp 27"', 'category' => 'Aksesoris', 'stock' => 25, 'price' => 6800000, 'description' => 'Monitor 4K IPS USB-C Hub untuk produktivitas.'],
            ['sku' => 'PRD-003', 'name' => 'Mechanical Keyboard Keychron K2', 'category' => 'Aksesoris', 'stock' => 40, 'price' => 1450000, 'description' => 'Keyboard mekanikal nirkabel 75% layout.'],
        ];

        foreach ($samples as $item) {
            Product::updateOrCreate(['sku' => $item['sku']], $item);
        }
    }
}
