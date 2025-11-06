<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

return new class extends Migration
{
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('imports', function (Blueprint $table) {
      $table->id();

      // 外部キー制約
      $table->foreignIdFor(User::class)
        ->constrained()
        ->onUpdate('cascade')
        ->onDelete('cascade');

      $table->string('file_path', 255)->comment('アップロードされたファイルのパス');
      $table->string('status', 20)->default('pending')->comment('処理状況: pending, processing, done, failed');
      $table->unsignedTinyInteger('progress')->default(0)->comment('進捗率(0-100)');
      $table->unsignedMediumInteger('imported_count')->default(0)->comment('インポート成功件数');
      $table->text('error_message')->nullable()->comment('エラーメッセージ');

      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('imports');
  }
};