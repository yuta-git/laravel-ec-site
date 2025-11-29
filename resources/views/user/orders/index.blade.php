<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      購入履歴
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
        <div class="p-6">
          <section class="text-gray-600 body-font">
            <div class="container px-4 py-24 mx-auto">
              <div class="lg:w-full w-full mx-auto overflow-auto">

                <table class="table-auto w-full text-left whitespace-nowrap">
                  <thead>
                    <tr>
                      <th
                        class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl">
                        注文日時
                      </th>
                      <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                        お名前
                      </th>
                      <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                        電話番号
                      </th>
                      <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                        配送先住所
                      </th>
                      <th
                        class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 text-center">
                        商品点数
                      </th>
                      <th
                        class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 text-right rounded-tr rounded-br">
                        合計金額
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($orders as $order)
                    <tr class="border-b border-gray-200">
                      <td class="px-4 py-3 text-sm">
                        {{ $order->ordered_at->format('Y年m月d日 H:i') }}
                      </td>
                      <td class="px-4 py-3">
                        {{ $order->customer_name }}
                      </td>
                      <td class="px-4 py-3">
                        {{ $order->phone_number }}
                      </td>
                      <td class="px-4 py-3">
                        <div class="max-w-xs truncate" title="{{ $order->address }}">
                          {{ $order->address }}
                        </div>
                      </td>
                      <td class="px-4 py-3 text-center">
                        {{ $order->total_quantity }}点
                      </td>
                      <td class="px-4 py-3 text-right font-semibold">
                        {{ number_format($order->total_price) }}円
                      </td>
                    </tr>
                    @empty
                    <tr>
                      <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                        購入履歴はありません
                      </td>
                    </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>

            <!-- ページネーション -->
            <div class="mt-6">
              {{ $orders->links() }}
            </div>
          </section>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>