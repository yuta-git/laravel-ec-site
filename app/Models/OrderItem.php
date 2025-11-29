<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
  use HasFactory;

  protected $fillable = [
    'order_id',
    'product_id',
    'product_name',
    'quantity',
    'unit_price',
  ];

  /**
   * キャストする属性
   */
  protected $casts = [
    'quantity' => 'integer',
    'unit_price' => 'integer',
  ];

  /**
   * 注文とのリレーション（多対1）
   */
  public function order()
  {
    return $this->belongsTo(Order::class);
  }

  /**
   * 商品とのリレーション（多対1）
   * ※商品が削除されていてもエラーにならない
   */
  public function product()
  {
    return $this->belongsTo(Product::class);
  }

  /**
   * 小計を計算（アクセサ）
   */
  public function getSubtotalAttribute(): int
  {
    return $this->unit_price * $this->quantity;
  }
}