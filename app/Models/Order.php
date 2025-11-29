<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{

  use HasFactory;

  protected $fillable = [
    'ordered_at',
    'customer_name',
    'phone_number',
    'address',
  ];


  /**
   * キャストする属性
   */
  protected $casts = [
    'ordered_at' => 'datetime',
  ];

  /**
   * 注文明細とのリレーション（1対多）
   */
  public function orderItems()
  {
    return $this->hasMany(OrderItem::class);
  }

  /**
   * 注文合計金額を計算（アクセサ）
   */
  public function getTotalPriceAttribute(): int
  {
    return $this->orderItems->sum(function ($item) {
      return $item->unit_price * $item->quantity;
    });
  }

  /**
   * 注文合計数量を計算（アクセサ）
   */
  public function getTotalQuantityAttribute(): int
  {
    return $this->orderItems->sum('quantity');
  }
}