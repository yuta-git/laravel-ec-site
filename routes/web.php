<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;


Route::middleware(['auth', 'verified'])->group(function () {

  // ダッシュボード(管理者用)
  Route::get('/admin/dashboard', function () {
    return view('dashboard');
  })->name('dashboard');

  // 商品(管理者用)
  Route::prefix('admin/products')
    ->group(function () {
      Route::controller(ProductController::class)
        ->name('products.')
        ->group(function () {
          Route::get('/', 'index')->name('index');
          Route::get('/create', 'create')->name('create');
          Route::post('/', 'store')->name('store');
          Route::prefix('/{uuid}')
            ->group(function () {
              Route::get('', 'show')->name('show');
              Route::get('/edit', 'edit')->name('edit');
              Route::put('', 'update')->name('update');
              Route::delete('', 'destroy')->name('destroy');
            });
        });
    });

  // カテゴリ(管理者用)
  Route::prefix('admin/categories')
    ->group(function () {
      Route::controller(CategoryController::class)
        ->name('categories.')
        ->group(function () {
          Route::get('/', 'index')->name('index');
        });
    });
    
});


// 一般ユーザー用ダッシュボード（認証不要）
Route::get('/', function () {
  return view('user.dashboard');
})->name('user.dashboard');


Route::middleware('auth')->group(function () {
  Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
  Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
  Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});



require __DIR__ . '/auth.php';