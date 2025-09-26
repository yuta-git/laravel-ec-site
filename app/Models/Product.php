<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;

class Product extends Model
{
  use HasFactory;

  protected $fillable = [
    'uuid',
    'name',
    'description',
    'price',
    'stock',
    'category_id',
  ];

      // UUIDを自動生成
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid()->toString();
            }
        });
    }

  public function category()
  {
    return $this->belongsTo(Category::class);
  }

  public function productImages()
  {
    return $this->hasMany(ProductImage::class);
  }
}