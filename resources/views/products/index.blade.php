<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      商品一覧
    </h2>
  </x-slot>

  @if (session('success'))
  <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 pt-4">
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
      <span class="block sm:inline">{{ session('success') }}</span>
    </div>
  </div>
  @endif

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
          <!-- 戻るボタン -->
          <div class="flex mb-4 justify-start w-full">
            <a href="{{ route('products.create') }}"
              class="flex text-white bg-green-500 border-0 py-2 px-6 focus:outline-none hover:bg-green-600 rounded">
              作成</a>
          </div>
          <section class="text-gray-600 body-font">
            <div class="container px-5 py-24 mx-auto">
              <div class="flex flex-wrap -m-4">
                @foreach($products as $product)
                <div class="p-4 md:w-1/3">
                  <div class="h-full border-2 border-gray-200 border-opacity-60 rounded-lg overflow-hidden">
                    <img class="lg:h-48 md:h-36 w-full object-cover object-center"
                      src="{{ $product->mainImage ? Storage::url($product->mainImage->image_path) : asset('images/products/no-image.jpg') }}"
                      alt="商品画像">
                    <div class="p-6">
                      <h2 class="tracking-widest text-xs title-font font-medium text-gray-400 mb-1">
                        {{$product->category->name}}
                      </h2>
                      <h1 class="title-font text-lg font-medium text-gray-900 mb-3">{{ $product->name }}</h1>
                      <p class="leading-relaxed mb-3">
                        {{ number_format($product->price) }} 円
                      </p>
                      <div class="flex items-center flex-wrap ">
                        <a class="text-indigo-500 inline-flex items-center md:mb-2 lg:mb-0"
                          href="{{ route('products.show', [ 'uuid' => $product->uuid ]) }}"> 詳細
                          <svg class="w-4 h-4 ml-2" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                            fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12h14"></path>
                            <path d="M12 5l7 7-7 7"></path>
                          </svg>
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
                @endforeach
              </div>
            </div>
            {{ $products->links() }}
          </section>

        </div>
      </div>
    </div>
  </div>
</x-app-layout>