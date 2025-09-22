<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Product;

return new class extends Migration
{
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('product_images', function (Blueprint $table) {
      $table->id();

      // 商品本体（products）が削除されたとき、それに紐づく画像（product_images）も一緒に自動で削除
      $table->foreignIdFor(Product::class)
        ->constrained()
        ->onUpdate('cascade')
        ->onDelete('cascade');

      $table->string('image_path', 255);
      $table->tinyInteger('image_type')->unsigned();

      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('product_images');
  }
};