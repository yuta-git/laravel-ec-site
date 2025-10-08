<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      商品詳細
    </h2>
  </x-slot>

  @if (session('success'))
  <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 pt-4">
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
      <span class="block sm:inline">{{ session('success') }}</span>
    </div>
  </div>
  @endif

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

          <section class="text-gray-600 body-font overflow-hidden">
            <!-- 戻るボタン -->
            <div class="flex mb-4 justify-start w-full">
              <a href="{{ route('products.index') }}"
                class="flex text-white bg-gray-500 border-0 py-2 px-6 focus:outline-none hover:bg-gray-600 rounded">
                戻る</a>
            </div>
            <div class="container px-5 py-24 mx-auto">

              <div class="lg:w-[95%] mx-auto flex flex-wrap">

                <!-- 画像エリア: 左側に小さい2つ、右側に大きい1つ -->
                <div class="lg:w-[60%] w-full flex aspect-[4/3]">
                  <!-- 左側: 小さい2つの画像を縦並び -->
                  <div class="flex flex-col w-[25%]">
                    <div class="md:p-2 p-1">
                      <img alt="ecommerce" class="w-full aspect-square object-cover object-center block rounded"
                        src="{{ $product->subImage1 ? Storage::url($product->subImage1->image_path) : asset('images/products/no-image.jpg') }}">
                    </div>
                    <div class="md:p-2 p-1">
                      <img alt="ecommerce" class="w-full aspect-square object-cover object-center block rounded"
                        src="{{ $product->subImage2 ? Storage::url($product->subImage2->image_path) : asset('images/products/no-image.jpg') }}">
                    </div>
                  </div>
                  <!-- 右側: 大きい1つの画像 -->
                  <div class="md:p-2 p-1 w-[75%]">
                    <img alt="ecommerce" class="w-full h-full object-cover object-center block rounded"
                      src="{{ $product->mainImage ? Storage::url($product->mainImage->image_path) : asset('images/products/no-image.jpg') }}">
                  </div>
                </div>

                <!-- 商品情報エリア -->
                <div class="lg:w-[40%] w-full lg:pl-10 lg:py-6 mt-6 lg:mt-0">
                  <h2 class="text-sm title-font text-gray-500 tracking-widest">{{ $product->category->name }}</h2>
                  <h1 class="text-gray-900 text-3xl title-font font-medium mb-1">{{ $product->name }}</h1>
                  <div class="flex mb-4">

                  </div>
                  <p class="leading-relaxed">{{ $product->description }}</p>
                  <div class="flex mt-6 items-center pb-5 border-b-2 border-gray-100 mb-5">

                  </div>
                  <div class="flex">
                    <span class="title-font font-medium text-2xl text-gray-900">{{ number_format( $product->price ) }}
                      円</span>
                  </div>
                  <!-- 編集ボタン -->
                  <div class="flex mt-8 justify-end gap-3">
                    <a href="{{ route('products.edit', [ 'uuid' => $product->uuid ]) }}"
                      class="flex text-white bg-indigo-500 border-0 py-2 px-6 focus:outline-none hover:bg-indigo-600 rounded">
                      編集</a>
                  </div>
                  <!-- 削除ボタン -->
                  <div class="flex mt-12 justify-end w-full">
                    <form action="{{ route('products.destroy', $product->uuid) }}" method="POST"
                      onsubmit="return confirm('この商品を削除しますか？削除すると元に戻せません。')">
                      @csrf
                      @method('DELETE')
                      <button type="submit"
                        class="flex text-white bg-red-500 border-0 py-2 px-6 focus:outline-none hover:bg-red-600 rounded">
                        削除</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </section>

        </div>
      </div>
    </div>
  </div>
</x-app-layout>