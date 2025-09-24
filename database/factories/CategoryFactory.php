<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Seederで具体的なカテゴリーを作るため、Factoryは汎用的に
        $categories = [
            'アクセサリー', 'インテリア', 'ペット用品', 
            '文房具', '楽器', 'DIY・工具'
        ];
        
        return [
            'name' => $this->faker->randomElement($categories),
            'sort_order' => $this->faker->numberBetween(1, 99),
        ];
    }
}