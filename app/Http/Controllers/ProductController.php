<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category;

use App\Http\Requests\ProductStoreRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

use App\Models\Import;
use App\Jobs\ImportProductsJob;

class ProductController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index(Request $request)
  {
    $categories = Category::getOrderedCategories();

    // 検索条件を取得
    $search = $request->input('search');
    $categoryId = $request->input('category_id');

    // 検索実行（カテゴリと商品名の両方で絞り込み）
    $products = Product::search($search, $categoryId)
      ->with(['mainImage'])
      ->orderByDesc('updated_at')
      ->paginate(15);

    $view = $request->route()->getName() === 'admin.products.index'
      ? 'products.index'
      : 'user.products.index';

    return view($view, compact('products', 'categories', 'categoryId', 'search'));
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    $categories = Category::getOrderedCategories();
    return view('products.create', compact('categories'));
  }

  /**
   * Display the specified resource.
   */
  public function show(Request $request, string $uuid)
  {
    $product = Product::with(['category', 'productImages'])
      ->where('uuid', $uuid)->firstOrFail();

    // ルート名に基づいてビューを切り替え
    $view = $request->route()->getName() === 'admin.products.show'
      ? 'products.show'
      : 'user.products.show';

    return view($view, compact('product'));
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
      ProductImage::saveProductImage($request, $product, 'main_image', 0);

      // サブ画像1の処理（image_type = 1）
      ProductImage::saveProductImage($request, $product, 'sub_image_1', 1);

      // サブ画像2の処理（image_type = 2）
      ProductImage::saveProductImage($request, $product, 'sub_image_2', 2);

      DB::commit();

      return redirect()->route('admin.products.index')
        ->with('success', '商品を登録しました');
    } catch (\Exception $e) {
      DB::rollBack();

      Log::error('商品作成エラー', [
        'exception' => $e
      ]);

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

    $categories = Category::getOrderedCategories();

    return view('products.edit', compact('product', 'categories'));
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(ProductStoreRequest $request, $uuid)
  {
    // トランザクション開始（画像保存に失敗したら商品も更新しない）
    DB::beginTransaction();

    $product = Product::where('uuid', $uuid)->firstOrFail();

    try {

      // 商品情報を更新（画像以外）
      $product->update([
        'name' => $request->name,
        'description' => $request->description,
        'price' => $request->price,
        'stock' => $request->stock,
        'category_id' => $request->category_id,
      ]);

      // メイン画像の処理（image_type = 0）
      ProductImage::saveProductImage($request, $product, 'main_image', 0);

      // サブ画像1の処理（image_type = 1）
      ProductImage::saveProductImage($request, $product, 'sub_image_1', 1);

      // サブ画像2の処理（image_type = 2）
      ProductImage::saveProductImage($request, $product, 'sub_image_2', 2);

      DB::commit();

      return redirect()->route('admin.products.show', $product->uuid)
        ->with('success', '商品を更新しました');
    } catch (\Exception $e) {
      DB::rollBack();

      Log::error('商品更新エラー', [
        'id' => $product->id,
        'exception' => $e
      ]);

      return redirect()->back()
        ->withInput()
        ->with('error', '商品の更新に失敗しました');
    }
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy($uuid)
  {

    $product = Product::where('uuid', $uuid)->firstOrFail();
    $imagePaths = $product->productImages->pluck('image_path')->toArray();

    DB::beginTransaction();

    try {
      foreach ($product->productImages as $image) {
        $image->delete();
      }

      $product->delete();

      DB::commit();

      // わざとエラーを起こす
      // throw new \Exception('テスト用のエラーです');

      //ここで落ちても最悪物理ファイルが残るだけなので運用で回避してもらう  
      foreach ($imagePaths as $path) {
        Storage::disk('public')->delete($path);
      }

      return redirect()->route('admin.products.index')
        ->with('success', '商品を削除しました');
    } catch (\Exception $e) {
      DB::rollBack();

      Log::error('商品削除エラー', [
        'id' => $product->id,
        '画像パス' => $imagePaths,
        'exception' => $e
      ]);

      return redirect()->back()
        ->with('error', '商品の削除に失敗しました');
    }
  }


  /*******
   CSV インポート機能
   *******/
  public function import(Request $request)
  {
    // CSVファイル自体のバリデーション
    $request->validate([
      'csv_file' => 'required|file|mimes:csv,txt|extensions:csv,txt|max:2048'
    ]);

    try {
      $file = $request->file('csv_file');

      // ファイルが空でないかチェック
      if ($file->getSize() == 0) {
        return redirect()->route('admin.products.index')
          ->with('error', 'CSVファイルが空です');
      }

      // ファイルをstorageに保存
      $path = $request->file('csv_file')->store('imports', 'local');

      // Importレコードを作成
      $import = Import::create([
        'user_id' => Auth::id(),
        'file_path' => $path,
        'status' => 'pending',
        'progress' => 0,
      ]);

      // Jobをディスパッチ(キューに投入)
      ImportProductsJob::dispatch($import->id, $path);

      // 進捗確認用にimport_idを渡してリダイレクト
      return redirect()->route('admin.products.index')
        ->with('success', 'CSVインポート処理を受け付けました')
        ->with('import_id', $import->id);
    } catch (\Exception $e) {
      Log::error('CSV Import Error: ' . $e->getMessage());
      return redirect()->route('admin.products.index')
        ->with('error', 'CSVのインポートに失敗しました: ' . $e->getMessage());
    }
  }
}