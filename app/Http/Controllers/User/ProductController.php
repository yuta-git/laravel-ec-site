<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * ユーザー画面: 商品一覧
     */
    public function index(Request $request)
    {
        $categories = Category::getOrderedCategories();
        
        // 検索条件を取得
        $search = $request->input('search');
        $categoryId = $request->input('category_id');
        
        // 商品を検索・取得
        $products = Product::search($search, $categoryId)
            ->with(['mainImage'])
            ->orderByDesc('updated_at')
            ->paginate(15);
        
        return view('user.products.index', compact('products', 'categories', 'categoryId', 'search'));
    }
    
    /**
     * ユーザー画面: 商品詳細
     */
    public function show(string $uuid)
    {
        $product = Product::with(['category', 'productImages'])
            ->where('uuid', $uuid)
            ->firstOrFail();
        
        return view('user.products.show', compact('product'));
    }
}