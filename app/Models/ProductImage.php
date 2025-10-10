<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;


class ProductImage extends Model
{
  use HasFactory;

  protected $fillable = [
    'product_id',
    'image_path',
    'image_type' // 0=メイン, 1=サブ1, 2=サブ2
  ];

  /**
   * 商品とのリレーション
   */
  public function product()
  {
    return $this->belongsTo(Product::class);
  }


  public static function saveProductImage($request, $product, $fieldName, $imageType)
  {
    if (!$request->hasFile($fieldName)) {
      return;
    }

    // 既存の画像を確認して削除（update時のみ実行される）
    $existingImage = self::where('product_id', $product->id)
      ->where('image_type', $imageType)
      ->first();

    if ($existingImage) {
      // ストレージから古い画像ファイルを削除
      Storage::disk('public')->delete($existingImage->image_path);
      $existingImage->delete();
    }

    // 新しい画像を保存
    $file = $request->file($fieldName);
    $path = $file->store('images/products/' . $product->id, 'public');

    ProductImage::create([
      'product_id' => $product->id,
      'image_path' => $path,
      'image_type' => $imageType
    ]);
  }
}