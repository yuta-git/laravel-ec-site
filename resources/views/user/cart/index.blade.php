<x-app-layout>
  <x-slot name="header">
    <div class="flex justify-between items-center">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        カート一覧
      </h2>

      <a href="{{ route('user.cart.index') }}"
        class="relative text-white bg-green-500 border-0 py-2 px-6 focus:outline-none hover:bg-green-600 rounded">
        カート
        @php
        $totalQuantity = 0;
        if(session('cart')) {
        foreach(session('cart') as $item) {
        $totalQuantity += $item['quantity'];
        }
        }
        @endphp
        @if($totalQuantity > 0)
        <span id="cart-badge"
          class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full h-6 w-6 flex items-center justify-center">
          {{ $totalQuantity }}
        </span>
        @endif
      </a>

    </div>
  </x-slot>

  @if (session('success'))
  <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 pt-4">
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
      <span class="block sm:inline">{{ session('success') }}</span>
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

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">

          <section class="text-gray-600 body-font">
            <!-- 戻るボタン -->
            <div class="flex mb-4 justify-start w-full">
              <a href="{{ route('user.products.index') }}"
                class="flex text-white bg-gray-500 border-0 py-2 px-6 focus:outline-none hover:bg-gray-600 rounded">
                戻る</a>
            </div>
            <div class="container px-5 py-24 mx-auto">
              <div class="flex flex-wrap -m-4">
                @foreach($cart as $productId => $item)
                <div class="p-4 md:w-1/3">
                  <div class="h-full border-2 border-gray-200 border-opacity-60 rounded-lg overflow-hidden">
                    <img class="lg:h-48 md:h-36 w-full object-cover object-center"
                      src="{{ $item['image_path'] ? Storage::url($item['image_path']) : asset('images/products/no-image.jpg') }}"
                      alt="商品画像">
                    <div class="p-6">
                      <h2 class="tracking-widest text-xs title-font font-medium text-gray-400 mb-1">
                        {{ $item['category_name'] }}
                      </h2>
                      <h1 class="title-font text-lg font-medium text-gray-900 mb-3">{{ $item['product_name'] }}</h1>
                      <p class="leading-relaxed mb-3">
                        {{ number_format($item['unit_price']) }} 円
                      </p>

                      <div class="mb-3">
                        <div class="flex items-center space-x-2">
                          <button onclick="updateQuantity(this)" data-product-id="{{ $productId }}"
                            data-action="decrement"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-1 px-3 rounded"
                            id="decrement-{{ $productId }}" {{ $item['quantity'] <= 1 ? 'disabled' : '' }}>
                            -
                          </button>

                          <span class="text-base font-nomal w-5 text-center" id="quantity-{{ $productId }}">
                            {{ $item['quantity'] }}
                          </span>

                          <button onclick="updateQuantity(this)" data-product-id="{{ $productId }}"
                            data-action="increment"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-1 px-3 rounded"
                            id="increment-{{ $productId }}">
                            +
                          </button>
                        </div>
                      </div>

                      <p class="leading-relaxed mb-3 font-semibold" id="subtotal-{{ $productId }}">
                        小計: {{ number_format($item['unit_price'] * $item['quantity']) }} 円
                      </p>


                      <div class="flex items-center justify-between">
                        <form method="POST" action="{{ route('user.cart.remove', ['productId' => $productId]) }}"
                          class="inline">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="text-red-500 inline-flex items-center md:mb-2 lg:mb-0">
                            削除
                          </button>
                        </form>

                      </div>
                    </div>
                  </div>
                </div>
                @endforeach
              </div>
            </div>
          </section>

        </div>
      </div>
    </div>
  </div>

  <script>
  function updateQuantity(button) {
    const productId = button.dataset.productId;
    const action = button.dataset.action;

    const quantityElement = document.getElementById(`quantity-${productId}`);
    const subtotalElement = document.getElementById(`subtotal-${productId}`);
    const decrementButton = document.getElementById(`decrement-${productId}`);

    fetch(`{{ url('cart/update') }}/${productId}`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
          action: action
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          quantityElement.textContent = data.quantity;
          subtotalElement.textContent = `小計: ${data.subtotal.toLocaleString()} 円`;

          if (data.quantity <= 1) {
            decrementButton.disabled = true;
            decrementButton.classList.add('opacity-50', 'cursor-not-allowed');
          } else {
            decrementButton.disabled = false;
            decrementButton.classList.remove('opacity-50', 'cursor-not-allowed');
          }

          // バッジを更新
          updateCartBadge();
        } else {
          alert(data.message);
        }
      })
      .catch(error => {
        console.error('エラー:', error);
        alert('更新に失敗しました。もう一度お試しください。');
      });
  }

  // バッジを更新する関数
  function updateCartBadge() {
    fetch('{{ route("user.cart.count") }}')
      .then(response => response.json())
      .then(data => {
        const badge = document.getElementById('cart-badge');
        if (badge) {
          if (data.count > 0) {
            badge.textContent = data.count;
            badge.style.display = 'flex'; // 表示
          } else {
            badge.style.display = 'none'; // 非表示
          }
        }
      })
      .catch(error => {
        console.error('バッジ更新エラー:', error);
      });
  }
  </script>
</x-app-layout>