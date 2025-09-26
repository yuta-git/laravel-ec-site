<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {

    // 日本語の商品名リスト
    $products = [
      'Tシャツ',
      'ジーンズ',
      'スニーカー',
      'バッグ',
      '財布',
      'ジャケット',
      'ワンピース',
      'テレビ',
      'ノートパソコン',
      'スマートフォン',
      'イヤホン',
      'カメラ',
      'タブレット',
      'コーヒー豆',
      'チョコレート',
      'お茶',
      'ワイン',
      'お菓子',
      'ジュース',
      '小説',
      'ビジネス書',
      '雑誌',
      '漫画',
      '写真集',
      'ヨガマット',
      'ダンベル',
      'ランニングシューズ',
      'テント',
      'リュック',
      'フライパン',
      '包丁',
      '炊飯器',
      '掃除機',
      '布団',
      'タオル',
      '化粧水',
      '口紅',
      'シャンプー',
      '香水',
      'ファンデーション',
      'ゲームソフト',
      'フィギュア',
      'パズル',
      'ボードゲーム',
      'ぬいぐるみ'
    ];

    // 日本語の説明文リスト
    $descriptions = [
      '高品質な素材を使用した人気商品です。',
      '使いやすさを追求した定番アイテムです。',
      '毎日の生活に欠かせない必需品です。',
      'プレゼントにも最適な商品です。',
      '限定生産のレアアイテムです。',
      'コストパフォーマンスに優れた商品です。',
      '多くのお客様にご好評いただいています。',
      '初心者にもおすすめの入門モデルです。',
    ];

    return [
      'uuid' => $this->faker->uuid,
      'name' => $this->faker->randomElement($products),
      'description' => $this->faker->randomElement($descriptions),
      'price' => $this->faker->numberBetween(1000, 90000),
      'stock' => $this->faker->numberBetween(1, 99),
      'category_id' => null, // Seederで設定
    ];
  }
}