<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Models\Order;

return new class extends Migration
{
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('order_items', function (Blueprint $table) {
      $table->id();
      $table->foreignIdFor(Order::class)
        ->constrained()
        ->onUpdate('cascade')
        ->onDelete('cascade');
      // productsテーブルとは外部キー制約を設定しない（履歴保持のため）
      $table->unsignedBigInteger('product_id');
      $table->string('product_name', 255);
      $table->unsignedTinyInteger('quantity');
      $table->unsignedMediumInteger('unit_price');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('order_items');
  }
};