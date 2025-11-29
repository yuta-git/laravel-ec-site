<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      購入手続き
    </h2>
  </x-slot>

  <!-- バリデーションエラー（フィールド単位） -->
  @if ($errors->any())
  <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 pt-4">
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
      <strong class="font-bold">入力内容にエラーがあります:</strong>
      <ul class="mt-2 list-disc list-inside">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  </div>
  @endif

  <!-- システムエラー（例外処理など） -->
  @if (session('error'))
  <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 pt-4">
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
      <span class="block sm:inline">{{ session('error') }}</span>
    </div>
  </div>
  @endif

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
          <section class="text-gray-600 body-font">
            <!-- 戻るボタン -->
            <div class="flex mb-4 justify-start w-full">
              <a href="{{ route('user.cart.index') }}"
                class="flex text-white bg-gray-500 border-0 py-2 px-6 focus:outline-none hover:bg-gray-600 rounded">
                戻る
              </a>
            </div>

            <div class="container px-5 py-24 mx-auto">
              <form action="{{ route('user.orders.store') }}" method="POST">
                @csrf

                <div class="lg:w-full mx-auto flex flex-wrap">

                  <!-- 左側: 配送情報フォーム -->
                  <div class="lg:w-1/2 w-full lg:pr-10 lg:py-6 mb-6 lg:mb-0">
                    <h2 class="text-sm title-font text-gray-500 tracking-widest mb-4">配送先情報</h2>

                    <!-- お名前 -->
                    <div class="mb-6">
                      <label for="customer_name" class="text-gray-700 text-sm font-medium block mb-2">
                        お名前 <span class="text-red-500">*</span>
                      </label>
                      <input type="text" id="customer_name" name="customer_name" value="{{ old('customer_name') }}"
                        class="w-full border border-gray-300 rounded px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('customer_name') border-red-500 @enderror"
                        placeholder="山田 太郎">
                      @error('customer_name')
                      <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                      @enderror
                    </div>

                    <!-- 電話番号 -->
                    <div class="mb-6">
                      <label for="phone_number" class="text-gray-700 text-sm font-medium block mb-2">
                        電話番号 <span class="text-red-500">*</span>
                      </label>
                      <input type="text" id="phone_number" name="phone_number" value="{{ old('phone_number') }}"
                        class="w-full border border-gray-300 rounded px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('phone_number') border-red-500 @enderror"
                        placeholder="090-1234-5678">
                      @error('phone_number')
                      <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                      @enderror
                    </div>

                    <!-- ご住所 -->
                    <div class="mb-6">
                      <label for="address" class="text-gray-700 text-sm font-medium block mb-2">
                        ご住所 <span class="text-red-500">*</span>
                      </label>
                      <textarea id="address" name="address" rows="4"
                        class="w-full border border-gray-300 rounded px-4 py-3 leading-relaxed focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('address') border-red-500 @enderror"
                        placeholder="〒123-4567&#10;東京都渋谷区〇〇1-2-3&#10;〇〇マンション101号室">{{ old('address') }}</textarea>
                      @error('address')
                      <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                      @enderror
                    </div>
                  </div>

                  <!-- 右側: 注文内容確認 -->
                  <div class="lg:w-1/2 w-full lg:pl-10 lg:py-6">
                    <h2 class="text-sm title-font text-gray-500 tracking-widest mb-4">ご注文内容</h2>

                    <div class="border border-gray-200 rounded-lg overflow-hidden mb-6">
                      @foreach($cart as $productId => $item)
                      <div class="flex items-center p-4 {{ !$loop->last ? 'border-b border-gray-200' : '' }}">
                        <!-- 商品画像 -->
                        <img class="w-20 h-20 object-cover object-center rounded border border-gray-200"
                          src="{{ $item['image_path'] ? Storage::url($item['image_path']) : asset('images/products/no-image.jpg') }}"
                          alt="{{ $item['product_name'] }}">

                        <!-- 商品情報 -->
                        <div class="ml-4 flex-1">
                          <h3 class="text-gray-900 text-sm font-medium">{{ $item['product_name'] }}</h3>
                          <p class="text-gray-500 text-xs mt-1">{{ $item['category_name'] }}</p>
                          <div class="flex items-center justify-between mt-2">
                            <span class="text-gray-600 text-sm">数量: {{ $item['quantity'] }}</span>
                            <span
                              class="text-gray-900 font-semibold">{{ number_format($item['unit_price'] * $item['quantity']) }}円</span>
                          </div>
                        </div>
                      </div>
                      @endforeach
                    </div>

                    <!-- 合計金額表示 -->
                    <div class="bg-gray-50 p-6 rounded-lg mb-6">
                      <div class="flex justify-between items-center mb-3">
                        <span class="text-gray-700 font-medium">商品点数</span>
                        <span class="text-gray-900">{{ $totalQuantity }}点</span>
                      </div>
                      <div class="border-t border-gray-200 pt-3">
                        <div class="flex justify-between items-center">
                          <span class="text-lg font-semibold text-gray-900">合計金額</span>
                          <span class="text-2xl font-bold text-indigo-600">{{ number_format($totalPrice) }}円</span>
                        </div>
                      </div>
                    </div>

                    <!-- 購入するボタン -->
                    <button type="submit"
                      class="w-full text-white bg-indigo-500 border-0 py-3 px-6 focus:outline-none hover:bg-indigo-600 rounded text-lg font-medium">
                      購入する
                    </button>
                  </div>

                </div>
              </form>
            </div>
          </section>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>