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
 
  
  public function mainImage() {
    return $this->hasOne(ProductImage::class, 'product_id')->where('image_type', 0);   
  }

  public function subImage1(){
    return $this->hasOne(ProductImage::class, 'product_id')->where('image_type', 1);
  }


  public function subImage2(){
    return $this->hasOne(ProductImage::class, 'product_id')->where('image_type', 2);
  }
  
}