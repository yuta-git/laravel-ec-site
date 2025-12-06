<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ImportController;
use App\Http\Controllers\User\CartController;
use App\Http\Controllers\User\OrderController;

use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\User\ProductController as UserProductController;

Route::middleware(['auth', 'verified'])->group(function () {

  Route::prefix('admin')->name('admin.')->group(function () {

    // ダッシュボード(管理者用)
    Route::get('dashboard', function () {
      return view('dashboard');
    })->name('dashboard');

    // 商品(管理者用)
    Route::prefix('products')
      ->group(function () {
        Route::controller(AdminProductController::class)
          ->name('products.')
          ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');

            // CSV インポート用ルート
            Route::post('/import', 'import')->name('import');

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
    Route::prefix('categories')
      ->controller(CategoryController::class)
      ->name('categories.')
      ->group(function () {
        Route::get('/', 'index')->name('index');
      });

    // CSVインポートの進捗確認API用ルート
    Route::get('/imports/{import}/progress', [ImportController::class, 'progress'])
      ->name('imports.progress');

    // 注文機能
    Route::prefix('orders')
      ->controller(OrderController::class)
      ->name('orders.')
      ->group(function () {
        Route::get('/', 'index')->name('index');
      });
  });
});


Route::name('user.')->group(function () {
  // 一般ユーザー用ダッシュボード（認証不要）
  Route::get('/', function () {
    return view('user.dashboard');
  })->name('dashboard');

  // 商品(一般ユーザー用)
  Route::prefix('products')
    ->controller(UserProductController::class)
    ->name('products.')
    ->group(function () {
      Route::get('/', 'index')->name('index');
      Route::get('/{uuid}', 'show')->name('show');
    });

  // カート機能
  Route::prefix('cart')
    ->controller(CartController::class)
    ->name('cart.')
    ->group(function () {
      Route::get('/', 'index')->name('index');
      Route::post('/add', 'add')->name('add');
      Route::post('/{productId}/increment', 'increment')->name('increment');
      Route::post('/{productId}/decrement', 'decrement')->name('decrement');
      Route::delete('/remove/{productId}', 'remove')->name('remove');
      Route::get('/count', 'count')->name('count');
    });

  // 注文機能
  Route::prefix('orders')
    ->controller(OrderController::class)
    ->name('orders.')
    ->group(function () {
      Route::get('/create', 'create')->name('create');
      Route::post('/', 'store')->name('store');
      Route::get('/complete', 'complete')->name('complete'); 
    });
});


Route::middleware('auth')->group(function () {
  Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
  Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
  Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});



require __DIR__ . '/auth.php';