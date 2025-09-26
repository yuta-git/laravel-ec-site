<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 既存カテゴリを全部取得し、それぞれに商品を作成
        Category::all()->each(function ($category) {
            Product::factory()->count(15)->create([
                'category_id' => $category->id
            ]);
        });
    }
}