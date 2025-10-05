<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      商品編集
    </h2>
  </x-slot>
  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">

          <section class="text-gray-600 body-font overflow-hidden">
            <!-- 戻るボタン -->
            <div class="flex mb-4 justify-start w-full">
              <a href="{{ route('products.show', $product->uuid) }}"
                class="flex text-white bg-gray-500 border-0 py-2 px-6 focus:outline-none hover:bg-gray-600 rounded">
                戻る</a>
            </div>
            <div class="container px-5 py-24 mx-auto">
              <form action="{{ route('products.update', $product->uuid) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="lg:w-[95%] mx-auto flex flex-wrap">
                  <!-- 画像エリア: 左側に小さい2つ、右側に大きい1つ -->
                  <div class="lg:w-[60%] w-full flex aspect-[4/3]">
                    <!-- 左側: 小さい2つの画像を縦並び -->
                    <div class="flex flex-col w-[25%]">
                      <div class="md:p-2 p-1">
                        <label for="sub_image_1" class="cursor-pointer block">
                          <img id="preview_sub_image_1" alt="サブ画像1"
                            class="w-full aspect-square object-cover object-center block rounded border-2 border-dashed border-gray-300 hover:border-gray-400"
                            src="{{ $product->subImage1 ? Storage::url($product->subImage1->image_path) : asset('images/products/no-image.jpg') }}">
                          <input type="file" id="sub_image_1" name="sub_image_1" accept="image/*" class="hidden"
                            onchange="previewImage(event, 'preview_sub_image_1')">
                        </label>
                        @error('sub_image_1')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">サブ画像1</p>
                      </div>
                      <div class="md:p-2 p-1">
                        <label for="sub_image_2" class="cursor-pointer block">
                          <img id="preview_sub_image_2" alt="サブ画像2"
                            class="w-full aspect-square object-cover object-center block rounded border-2 border-dashed border-gray-300 hover:border-gray-400"
                            src="{{ $product->subImage2 ? Storage::url($product->subImage2->image_path) : asset('images/products/no-image.jpg') }}">
                          <input type="file" id="sub_image_2" name="sub_image_2" accept="image/*" class="hidden"
                            onchange="previewImage(event, 'preview_sub_image_2')">
                        </label>
                        @error('sub_image_2')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">サブ画像2</p>
                      </div>
                    </div>
                    <!-- 右側: 大きい1つの画像 -->
                    <div class="md:p-2 p-1 w-[75%]">
                      <label for="main_image" class="cursor-pointer block h-full">
                        <img id="preview_main_image" alt="メイン画像"
                          class="w-full h-full object-cover object-center block rounded border-2 border-dashed border-gray-300 hover:border-gray-400"
                          src="{{ $product->mainImage ? Storage::url($product->mainImage->image_path) : asset('images/products/no-image.jpg') }}">
                        <input type="file" id="main_image" name="main_image" accept="image/*" class="hidden"
                          onchange="previewImage(event, 'preview_main_image')">
                      </label>
                      @error('main_image')
                      <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                      @enderror
                      <p class="text-xs text-gray-500 mt-1">メイン画像</p>
                    </div>
                  </div>

                  <!-- 商品情報エリア -->
                  <div class="lg:w-[40%] w-full lg:pl-10 lg:py-6 mt-6 lg:mt-0">
                    <!-- カテゴリ選択 -->
                    <div class="mb-4">
                      <label for="category_id" class="text-sm title-font text-gray-500 tracking-widest block mb-2">
                        カテゴリ <span class="text-red-500">*</span>
                      </label>
                      <select id="category_id" name="category_id"
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('category_id') border-red-500 @enderror">
                        <option value="">カテゴリを選択してください</option>
                        @foreach($categories ?? [] as $category)
                        <option value="{{ $category->id }}"
                          {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                          {{ $category->name }}
                        </option>
                        @endforeach
                      </select>
                      @error('category_id')
                      <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                      @enderror
                    </div>

                    <!-- 商品名 -->
                    <div class="mb-4">
                      <label for="name" class="text-gray-900 text-3xl title-font font-medium block mb-2">
                        商品名 <span class="text-red-500">*</span>
                      </label>
                      <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}"
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('name') border-red-500 @enderror"
                        placeholder="商品名を入力してください">
                      @error('name')
                      <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                      @enderror
                    </div>

                    <!-- 商品説明 -->
                    <div class="mb-4">
                      <label for="description" class="text-gray-700 text-sm font-medium block mb-2">
                        商品説明
                      </label>
                      <textarea id="description" name="description" rows="5"
                        class="w-full border border-gray-300 rounded px-3 py-2 leading-relaxed focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('description') border-red-500 @enderror"
                        placeholder="商品の説明を入力してください">{{ old('description', $product->description) }}</textarea>
                      @error('description')
                      <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                      @enderror
                    </div>

                    <div class="border-b-2 border-gray-100 mb-5"></div>

                    <!-- 価格と在庫 -->
                    <div class="flex gap-4 mb-6">
                      <div class="flex-1">
                        <label for="price" class="text-gray-700 text-sm font-medium block mb-2">
                          価格（円） <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="price" name="price" value="{{ old('price', $product->price) }}" min="0"
                          class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('price') border-red-500 @enderror"
                          placeholder="例: 1980">
                        @error('price')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                      </div>
                      <div class="flex-1">
                        <label for="stock" class="text-gray-700 text-sm font-medium block mb-2">
                          在庫数 <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="stock" name="stock" value="{{ old('stock', $product->stock) }}" min="0"
                          class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('stock') border-red-500 @enderror"
                          placeholder="0">
                        @error('stock')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                      </div>
                    </div>

                    <!-- 更新ボタン -->
                    <div class="flex mt-8 justify-end gap-3">
                      <button type="submit"
                        class="flex text-white bg-indigo-500 border-0 py-2 px-6 focus:outline-none hover:bg-indigo-600 rounded">
                        更新</button>
                    </div>
                  </div>
                </div>
              </form>
            </div>
          </section>
        </div>
      </div>
    </div>
  </div>

  <!-- 画像プレビュー用JavaScript -->
  <script>
  function previewImage(event, previewId) {
    const file = event.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function(e) {
        document.getElementById(previewId).src = e.target.result;
      }
      reader.readAsDataURL(file);
    }
  }
  </script>
</x-app-layout>