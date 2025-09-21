<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Category;

return new class extends Migration
{
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('products', function (Blueprint $table) {
      $table->id();

      // カテゴリが削除される場合、そのカテゴリに紐づいた商品（products）が一つでも存在すれば削除できない
      $table->foreignIdFor(Category::class)
        ->constrained()
        ->onUpdate('cascade')
        ->onDelete('restrict');

      $table->char('uuid', 36)->unique();
      $table->string('name', 255);
      $table->text('description');
      $table->mediumInteger('price')->unsigned();
      $table->smallInteger('stock')->unsigned();

      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('products');
  }
};