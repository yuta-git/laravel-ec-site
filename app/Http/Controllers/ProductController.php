<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category;
use App\Http\Requests\ProductStoreRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    $products = Product::with(['mainImage'])->orderByDesc('updated_at')->paginate(15);

    return view('products.index', compact('products'));
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    $categories = Category::select('id', 'name', 'sort_order')->orderBy('sort_order')->get();
    return view('products.create', compact('categories'));
  }

  /**
   * Display the specified resource.
   */
  public function show(string $uuid)
  {
    $product = Product::with(['category', 'productImages'])
      ->where('uuid', $uuid)->firstOrFail();

    return view('products.show', compact('product'));
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(ProductStoreRequest $request)
  {
    // トランザクション開始（画像保存に失敗したら商品も作成しない）
    DB::beginTransaction();

    try {
      // 商品レコードを作成（画像以外）
      $product = Product::create([
        'name' => $request->name,
        'description' => $request->description,
        'price' => $request->price,
        'stock' => $request->stock,
        'category_id' => $request->category_id,
      ]);

      // メイン画像の処理（image_type = 0）
      $this->saveProductImage($request, $product, 'main_image', 0);

      // サブ画像1の処理（image_type = 1）
      $this->saveProductImage($request, $product, 'sub_image_1', 1);

      // サブ画像2の処理（image_type = 2）
      $this->saveProductImage($request, $product, 'sub_image_2', 2);

      DB::commit();

      return redirect()->route('products.index')
        ->with('success', '商品を登録しました');
        
    } catch (\Exception $e) {
      DB::rollBack();

      return redirect()->back()
        ->withInput()
        ->with('error', '商品の登録に失敗しました');
    }
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(string $uuid)
  {
    $product = Product::with(['category', 'productImages'])
      ->where('uuid', $uuid)->firstOrFail();

    $categories = Category::select('id', 'name', 'sort_order')->orderBy('sort_order')->get();

    return view('products.edit', compact('product', 'categories'));
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(ProductStoreRequest $request, $uuid)
  {
    // トランザクション開始（画像保存に失敗したら商品も更新しない）
    DB::beginTransaction();

    try {
      // UUIDで商品を取得
      $product = Product::where('uuid', $uuid)->firstOrFail();

      // 商品情報を更新（画像以外）
      $product->update([
        'name' => $request->name,
        'description' => $request->description,
        'price' => $request->price,
        'stock' => $request->stock,
        'category_id' => $request->category_id,
      ]);

      // メイン画像の処理（image_type = 0）
      $this->saveProductImage($request, $product, 'main_image', 0);

      // サブ画像1の処理（image_type = 1）
      $this->saveProductImage($request, $product, 'sub_image_1', 1);

      // サブ画像2の処理（image_type = 2）
      $this->saveProductImage($request, $product, 'sub_image_2', 2);

      DB::commit();

      return redirect()->route('products.show', $product->uuid)
        ->with('success', '商品を更新しました');
    } catch (\Exception $e) {
      DB::rollBack();

      return redirect()->back()
        ->withInput()
        ->with('error', '商品の更新に失敗しました');
    }
  }


  private function saveProductImage($request, $product, $fieldName, $imageType)
  {
    if ($request->hasFile($fieldName)) {
      // 既存の画像を確認して削除（update時のみ実行される）
      $existingImage = ProductImage::where('product_id', $product->id)
        ->where('image_type', $imageType)
        ->first();

      if ($existingImage) {
        // ストレージから古い画像ファイルを削除
        Storage::disk('public')->delete($existingImage->image_path);
        $existingImage->delete();
      }

      // 新しい画像を保存
      $file = $request->file($fieldName);
      $path = $file->store('images/products', 'public');

      ProductImage::create([
        'product_id' => $product->id,
        'image_path' => $path,
        'image_type' => $imageType
      ]);
    }
  }


  /**
   * Remove the specified resource from storage.
   */
  public function destroy($uuid)
  {
    
    DB::beginTransaction();

    try {
      $product = Product::where('uuid', $uuid)->firstOrFail();

      // 関連する画像をストレージから削除
      foreach ($product->productImages as $image) {
        Storage::disk('public')->delete($image->image_path);
        $image->delete();
      }

      // 商品を削除
      $product->delete();

      DB::commit();

      return redirect()->route('products.index')
        ->with('success', '商品を削除しました');
        
    } catch (\Exception $e) {
      DB::rollBack();

      return redirect()->back()
        ->with('error', '商品の削除に失敗しました');
    }
  }
}