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
      ProductImage::factory()->mainImage()->create([
        'product_id' => $product->id,
      ]);
      ProductImage::factory()->subImageOne()->create([
        'product_id' => $product->id,
      ]);
      ProductImage::factory()->subImageTwo()->create([
        'product_id' => $product->id,
      ]);
    });
  }
}