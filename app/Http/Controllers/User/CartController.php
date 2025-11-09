<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class CartController extends Controller
{
  // カートに追加
  public function add(Request $request)
  {
    $product = Product::with(['mainImage', 'category'])->findOrFail($request->product_id);

    $cartItem = [
      'product_id' => $product->id,
      'product_name' => $product->name,
      'quantity' => $request->quantity ?? 1,
      'unit_price' => $product->price,
      'image_path' => $product->mainImage ? $product->mainImage->image_path : null,
      'category_name' => $product->category->name
    ];

    $cart = $request->session()->get('cart', []);

    // 既に同じ商品がカートにある場合は数量を加算
    if (isset($cart[$product->id])) {
      $cart[$product->id]['quantity'] += $cartItem['quantity'];
    } else {
      $cart[$product->id] = $cartItem;
    }

    $request->session()->put('cart', $cart);

    return redirect()->back()->with('success', 'カートに追加しました');
  }

  // カート表示
  public function index(Request $request)
  {
    $cart = $request->session()->get('cart', []);

    return view('user.cart.index', compact('cart'));
  }

  // カートの商品を削除
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

  /*** 
  カートの商品を増減させる("-"と"+"アイコンを押したときの動き) 
   ***/
  public function update(Request $request, $productId)
  {
    $cart = $request->session()->get('cart', []);

    if (!isset($cart[$productId])) {
      return response()->json([
        'success' => false,
        'message' => '商品が見つかりませんでした'
      ], 404);
    }

    $action = $request->input('action');

    if ($action === 'increment') {
      $cart[$productId]['quantity']++;
    } elseif ($action === 'decrement') {
      if ($cart[$productId]['quantity'] > 1) {
        $cart[$productId]['quantity']--;
      }
    }

    $request->session()->put('cart', $cart);

    // JSON形式でレスポンスを返す
    return response()->json([
      'success' => true,
      'quantity' => $cart[$productId]['quantity'],
      'subtotal' => $cart[$productId]['unit_price'] * $cart[$productId]['quantity'],
      'message' => '数量を更新しました'
    ]);
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