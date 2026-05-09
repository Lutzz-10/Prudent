<?php
// database/seeders/CategorySeeder.php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Makanan & Minuman', 'icon' => '🍔', 'color' => '#F59E0B'],
            ['name' => 'Belanja Harian',    'icon' => '🛒', 'color' => '#10B981'],
            ['name' => 'Transportasi',      'icon' => '🚗', 'color' => '#3B82F6'],
            ['name' => 'Kesehatan',         'icon' => '💊', 'color' => '#EF4444'],
            ['name' => 'Elektronik',        'icon' => '📱', 'color' => '#8B5CF6'],
            ['name' => 'Pakaian',           'icon' => '👕', 'color' => '#EC4899'],
            ['name' => 'Hiburan',           'icon' => '🎮', 'color' => '#F97316'],
            ['name' => 'Pendidikan',        'icon' => '📚', 'color' => '#06B6D4'],
            ['name' => 'Tagihan',           'icon' => '🧾', 'color' => '#64748B'],
            ['name' => 'Lainnya',           'icon' => '📦', 'color' => '#A1A1AA'],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }
    }
}