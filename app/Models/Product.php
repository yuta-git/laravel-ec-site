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


  public function mainImage()
  {
    return $this->hasOne(ProductImage::class, 'product_id')->where('image_type', 0);
  }

  public function subImage1()
  {
    return $this->hasOne(ProductImage::class, 'product_id')->where('image_type', 1);
  }


  public function subImage2()
  {
    return $this->hasOne(ProductImage::class, 'product_id')->where('image_type', 2);
  }


  /*******
   商品名とカテゴリで絞り込むスコープ
  ********/
  public function scopeSearch($query, $search, $categoryId = null)
  {
    // カテゴリが選択されている場合（"すべて"以外）
    if (!empty($categoryId)) {
      $query->where('category_id', $categoryId);
    }

    // 検索キーワードが入力されている場合
    if (!empty($search)) {

      foreach ($this->splitSpaceToArray($search) as $value) {
        // ワイルドカード文字をエスケープ
        $escapedValue = $this->escapeLikeWildcards($value);
        $query->where('name', 'like', '%' . $escapedValue . '%');
      }
    }
    return $query;
  }

  // スペース区切りで配列化
  private function splitSpaceToArray($string)
  {
    $input = str_replace('　', ' ', $string); // 全角スペースを半角に統一
    $input = trim($input);
    
    $splited = preg_split('/[\s]+/', $input, -1, PREG_SPLIT_NO_EMPTY);

    return collect($splited)
      ->filter(fn($v) => mb_strlen($v) <= 20) // 20文字以下に制限
      ->unique()
      ->take(10)
      ->values() // インデックスを0から振り直し
      ->all(); // コレクションを配列に変換
  }

  // LIKE検索用のワイルドカード文字をエスケープする
  private function escapeLikeWildcards($value)
  {
    $value = str_replace('\\', '\\\\', $value);  // バックスラッシュをエスケープ
    $value = str_replace('%',  '\%',  $value);
    $value = str_replace('_',  '\_',  $value);

    return $value;
  }
}