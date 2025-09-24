<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductImage>
 */
class ProductImageFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    
    $sampleImages = [
      'images/products/sample1.jpg',
      'images/products/sample2.jpg',
      'images/products/sample3.jpg',
      'images/products/sample4.jpg',
      'images/products/sample5.jpg',
    ];

    return [
      'product_id' => null, // Seederで設定
      'image_path' => $this->faker->randomElement($sampleImages),
      'image_type' => null // Seederで設定
    ];
  }
}