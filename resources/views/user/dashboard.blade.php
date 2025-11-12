<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      {{ __('Dashboard') }}
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">


        <section class="text-gray-600 body-font">
          <div class="container px-5 py-24 mx-auto">
            <div class="flex flex-wrap -m-2">

              <a href="{{ route('user.products.index') }}" class="p-2 lg:w-1/3 md:w-1/2 w-full">
                <div
                  class="h-full flex items-center border-gray-200 border p-4 rounded-lg hover:bg-gray-100 hover:shadow-md transition-all duration-300">
                  <div class="flex-grow">
                    <p class="text-gray-900 title-font font-medium">商品一覧</p>
                  </div>
                </div>
              </a>

              <a href="" class="p-2 lg:w-1/3 md:w-1/2 w-full">
                <div
                  class="h-full flex items-center border-gray-200 border p-4 rounded-lg hover:bg-gray-100 hover:shadow-md transition-all duration-300">
                  <div class="flex-grow">
                    <p class="text-gray-900 title-font font-medium">カテゴリ一覧</p>
                  </div>
                </div>
              </a>

            </div>
          </div>
        </section>


      </div>
    </div>
  </div>
</x-app-layout>