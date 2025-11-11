<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Requests\CartAddRequest;

class CartController extends Controller
{
  /***
   * カート表示
   ***/
  public function index(Request $request)
  {
    $cart = $request->session()->get('cart', []);

    // ビジネスロジックをController側で処理
    $totalQuantity = $this->calculateTotalQuantity($cart);
    $totalPrice = $this->calculateTotalPrice($cart);

    return view('user.cart.index', compact('cart', 'totalQuantity', 'totalPrice'));
  }

  /**
   * カート内の合計数量を計算(プライベートメソッド)
   */
  private function calculateTotalQuantity(array $cart): int
  {
    $total = 0;
    foreach ($cart as $item) {
      $total += $item['quantity'];
    }
    return $total;
  }

  /**
   * カート内の合計金額を計算(プライベートメソッド)
   */
  private function calculateTotalPrice(array $cart): int
  {
    $total = 0;
    foreach ($cart as $item) {
      $total += $item['unit_price'] * $item['quantity'];
    }
    return $total;
  }

  /***
   * カートに追加
   ****/
  public function add(CartAddRequest $request)
  {
    // バリデーション
    $validated = $request->validated();

    $product = Product::with(['mainImage', 'category'])
      ->findOrFail($request->product_id);

    $cartItem = [
      'product_id' => $product->id,
      'product_name' => $product->name,
      'quantity' => $validated['quantity'],  // バリデーション済みの値を使用
      'unit_price' => $product->price,
      'image_path' => $product->mainImage ? $product->mainImage->image_path : null,
      'category_name' => $product->category->name
    ];

    $cart = $request->session()->get('cart', []);

    if (isset($cart[$product->id])) {
      // 既存の数量と新規の数量の合計をチェック
      $newQuantity = $cart[$product->id]['quantity'] + $validated['quantity'];
      if ($newQuantity > 99) {
        return redirect()->back()->with('error', 'カート内の数量は最大99までです');
      }
      $cart[$product->id]['quantity'] = $newQuantity;
    } else {
      $cart[$product->id] = $cartItem;
    }

    $request->session()->put('cart', $cart);

    return redirect()->back()->with('success', 'カートに追加しました');
  }

  /***
   * カートの商品を削除
   ***/
  public function remove(Request $request, $productId)
  {
    $cart = $request->session()->get('cart', []);

    if (isset($cart[$productId])) {
      unset($cart[$productId]);
      $request->session()->put('cart', $cart);
      return redirect()->back()->with('success', 'カートから削除しました');
    }

    return redirect()->back()->with('error', '商品が見つかりませんでした');
  }

  /**
   * 数量を増やす("+"を押したときの動き)
   */
  public function increment(Request $request, $productId)
  {
    return response()->json(
      $this->updateQuantity($request, $productId, 1)
    );
  }

  /**
   * 数量を減らす("-"を押したときの動き)
   */
  public function decrement(Request $request, $productId)
  {
    return response()->json(
      $this->updateQuantity($request, $productId, -1)
    );
  }

  /**
   * 数量更新の共通処理（プライベートメソッド）
   */
  private function updateQuantity(Request $request, $productId, $delta): array
  {
    $cart = $request->session()->get('cart', []);

    if (!isset($cart[$productId])) {
      abort(404, '商品が見つかりませんでした');
    }

    // 減少の場合は最小値チェック
    if ($delta < 0 && $cart[$productId]['quantity'] <= 1) {
      abort(400, '数量は1以上である必要があります');
    }

    // 数量を更新
    $cart[$productId]['quantity'] += $delta;

    // セッションに保存
    $request->session()->put('cart', $cart);

    // カート全体の合計金額を計算
    $totalPrice = 0;
    $totalQuantity = 0;
    foreach ($cart as $item) {
      $totalPrice += $item['unit_price'] * $item['quantity'];
      $totalQuantity += $item['quantity'];
    }

    return [
      'quantity' => $cart[$productId]['quantity'],
      'subtotal' => $cart[$productId]['unit_price'] * $cart[$productId]['quantity'],
      'total_price' => $totalPrice,
      'total_quantity' => $totalQuantity
    ];
  }

  /*** 
  カート内の商品数をカウントする
   ***/
  public function count(Request $request)
  {
    $cart = $request->session()->get('cart', []);
    $totalQuantity = 0;

    foreach ($cart as $item) {
      $totalQuantity += $item['quantity'];
    }

    return response()->json(['count' => $totalQuantity]);
  }
}