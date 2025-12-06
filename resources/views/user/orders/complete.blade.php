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
            <div class="p-12 text-center">

              <p class="text-2xl font-semibold text-gray-800 mb-10">
                注文が完了しました！
              </p>

              <p class="text-gray-600 mb-20">
                ご利用いただきありがとうございます。
              </p>

              <div class="w-full flex justify-center">
                <a href="{{ route('user.dashboard') }}" class="block w-1/3">
                  <div
                    class="h-full flex items-center border-gray-200 border p-4 rounded-lg hover:bg-gray-100 hover:shadow-md transition-all duration-300">
                    <div class="flex-grow text-center">
                      <p class="text-gray-900 title-font font-medium">ダッシュボードへ</p>
                    </div>
                  </div>
                </a>
              </div>


            </div>
          </div>
        </section>

      </div>
    </div>
  </div>
</x-app-layout>