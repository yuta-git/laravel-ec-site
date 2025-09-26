<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 具体的なカテゴリーを定義
        $categories = [
            ['name' => 'ファッション', 'sort_order' => 1],
            ['name' => '家電・カメラ', 'sort_order' => 2],
            ['name' => '食品・飲料', 'sort_order' => 3],
            ['name' => '本・雑誌', 'sort_order' => 4],
            ['name' => 'スポーツ・アウトドア', 'sort_order' => 5],
            ['name' => 'ホーム・キッチン', 'sort_order' => 6],
            ['name' => 'ビューティー・コスメ', 'sort_order' => 7],
            ['name' => 'おもちゃ・ゲーム', 'sort_order' => 8],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}