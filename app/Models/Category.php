<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
  use HasFactory;

  protected $fillable = [
    'name',
    'sort_order',
  ];

  public function products()
  {
    return $this->hasMany(Product::class);
  }

  public static function getOrderedCategories()
  {
    return self::select('id', 'name', 'sort_order')->orderBy('sort_order')->get();
  }
  
}