<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProductImage;
use App\Models\Product;

class ProductImagesTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    Product::all()->each(function ($product) {
      // image_type = 0 (メイン画像)
      ProductImage::factory()->create([
        'product_id' => $product->id,
        'image_type' => 0
      ]);
      // サブ画像1
      ProductImage::factory()->create([
        'product_id' => $product->id,
        'image_type' => 1
      ]);
      // サブ画像2
      ProductImage::factory()->create([
        'product_id' => $product->id,
        'image_type' => 2
      ]);
    });
  }
}