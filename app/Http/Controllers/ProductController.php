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
use Illuminate\Support\Facades\Validator;


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

    return view('products.index', compact('products', 'categories', 'categoryId', 'search'));
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


    /**
   * CSV インポート機能
   */
  public function import(Request $request)
  {
    // CSVファイル自体のバリデーション
    $request->validate([
      'csv_file' => 'required|file|mimes:csv,txt|max:2048'
    ]);

    try {
      DB::beginTransaction();

      $file = $request->file('csv_file');

      // ファイルが空でないかチェック
      if ($file->getSize() == 0) {
        throw new \Exception('CSVファイルが空です');
      }

      // CSV読み込み処理...
      $csvData = file_get_contents($file->getRealPath());

      // UTF-8に変換。Laravelは基本的にUTF-8で動作するため、文字化けを防ぐため。
      if (mb_detect_encoding($csvData, 'UTF-8, SJIS, SJIS-win', true) !== 'UTF-8') {
        $csvData = mb_convert_encoding($csvData, 'UTF-8', 'SJIS-win');
      }

      //  UTF-8 BOM(Byte Order Mark)を削除。BOMがあると1列目のヘッダー名が正しく認識されない可能性がある。
      $csvData = str_replace("\xEF\xBB\xBF", '', $csvData);

      // 配列の中に各行を配列する
      $rows = array_map('str_getcsv', explode("\n", $csvData));

      // array_shift(): 配列の先頭要素を取り出して削除。$rowsにはデータ行のみが残る。
      $header = array_shift($rows);

      $importCount = 0;
      $errorMessages = [];

      
      foreach ($rows as $index => $row) {
        
        // 空行スキップ。array_filter($row): 空でない要素のみ残す
        if (empty(array_filter($row))) {
          continue;
        }

        // ヘッダーとデータのカラム数が一致するか確認
        if (count($row) != count($header)) {
          $errorMessages[] = ($index + 2) . '行目: カラム数が一致しません';
          continue;
        }

        // ヘッダー名をキーにした連想配列に変換。$data = ['name' => '商品A','price' => '1000','stock' => '50']
        $data = array_combine($header, $row);
        $rowNumber = $index + 2;

        // 各行のバリデーション
        $validator = Validator::make($data, [
          'name' => 'required|string|max:255',
          'description' => 'nullable|string|max:2000',
          'price' => 'required|integer|min:0|max:99999999',
          'stock' => 'required|integer|min:0|max:999999',
          'category_id' => 'required|exists:categories,id',
        ]);

        if ($validator->fails()) {
          foreach ($validator->errors()->all() as $error) {
            $errorMessages[] = "{$rowNumber}行目: {$error}";
          }
          continue;
        }

        try {
          Product::create($data);
          $importCount++;
        } catch (\Exception $e) {
          $errorMessages[] = "{$rowNumber}行目: " . $e->getMessage();
        }
      }

      if (!empty($errorMessages)) {
        DB::rollBack();
        return redirect()->route('admin.products.index')
          ->with('error', 'CSVのインポート中にエラーが発生しました:<br>' . implode('<br>', $errorMessages));
      }

      if ($importCount == 0) {
        DB::rollBack();
        return redirect()->route('admin.products.index')
          ->with('error', 'インポート可能なデータがありませんでした');
      }

      DB::commit();

      return redirect()->route('admin.products.index')
        ->with('success', "{$importCount}件の商品をインポートしました");
    } catch (\Exception $e) {
      DB::rollBack();
      Log::error('CSV Import Error: ' . $e->getMessage());
      return redirect()->route('admin.products.index')
        ->with('error', 'CSVのインポートに失敗しました: ' . $e->getMessage());
    }
  }
}