<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}