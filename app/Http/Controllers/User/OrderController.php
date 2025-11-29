<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
  /**
   * 注文確認画面（カート内容の確認）
   */
  public function create(Request $request)
  {
    $cart = $request->session()->get('cart', []);

    // カートが空の場合はカート一覧へリダイレクト
    if (empty($cart)) {
      return redirect()->route('user.cart.index')
        ->with('error', 'カートに商品がありません。');
    }

    // 合計金額と合計数量を計算
    $totalPrice = 0;
    $totalQuantity = 0;

    foreach ($cart as $item) {
      $totalPrice += $item['unit_price'] * $item['quantity'];
      $totalQuantity += $item['quantity'];
    }

    return view('user.orders.create', compact('cart', 'totalPrice', 'totalQuantity'));
  }

  /**
   * 注文確定処理
   */
  public function store(Request $request)
  {
    // バリデーション
    $validated = $request->validate([
      'customer_name' => ['required', 'string', 'max:100'],
      'phone_number' => ['required', 'string', 'max:20'],
      'address' => ['required', 'string', 'max:255'],
    ]);

    $cart = $request->session()->get('cart', []);

    // カートが空の場合はエラー
    if (empty($cart)) {
      return redirect()->route('user.cart.index')
        ->with('error', 'カートに商品がありません。');
    }

    DB::beginTransaction();

    try {
      // 注文を作成
      $order = Order::create([
        'customer_name' => $validated['customer_name'],
        'phone_number' => $validated['phone_number'],
        'address' => $validated['address'],
      ]);

      // 注文明細を作成
      foreach ($cart as $productId => $item) {
        // 商品の存在確認（念のため）
        $product = Product::find($productId);

        if (!$product) {
          throw new \Exception("商品ID {$productId} が存在しません。");
        }

        // 在庫チェック
        if ($product->stock < $item['quantity']) {
          throw new \Exception("{$product->name} の在庫が不足しています。");
        }

        // 注文明細を作成
        $order->orderItems()->create([
          'product_id' => $productId,
          'product_name' => $item['product_name'],
          'quantity' => $item['quantity'],
          'unit_price' => $item['unit_price'],
        ]);

        // 在庫を減らす
        $product->decrement('stock', $item['quantity']);
      }

      // カートを空にする
      $request->session()->forget('cart');

      DB::commit();

      // 注文詳細画面へリダイレクト
      return redirect()->route('user.cart.index')
        ->with('success', '注文が確定しました。カートが空になりました。');
    } catch (\Exception $e) {
      DB::rollBack();

      return redirect()->route('user.cart.index')
        ->with('error', '注文処理に失敗しました: ' . $e->getMessage());
    }
  }

  /**
   * 注文履歴一覧
   */
  public function index()
  {
    // 最新の注文から表示
    $orders = Order::with('orderItems')
      ->orderBy('ordered_at', 'desc')
      ->paginate(10);

    return view('user.orders.index', compact('orders'));
  }

}