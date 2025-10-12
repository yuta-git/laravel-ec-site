<div>
  <!-- トースト通知 -->
  @if($showToast)
  <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => { show = false; $wire.hideToast(); }, 3000)"
    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-2"
    x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
    class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
    {{ $toastMessage }}
  </div>
  @endif

  <section class="text-gray-600 body-font">
    <div class="container px-4 py-24 mx-auto">
      <div class="lg:w-2/3 w-full mx-auto overflow-auto">

        <!-- 作成ボタン -->
        <div class="flex mb-8 justify-start w-full">
          <button wire:click="startCreating"
            class="flex text-white bg-green-500 border-0 py-2 px-6 focus:outline-none hover:bg-green-600 rounded">
            作成</button>
        </div>

        <table class="table-auto w-full text-left whitespace-no-wrap">
          <thead>
            <tr>
              <th
                class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl">
                カテゴリ名
              </th>
              <th
                class="w-1/4 px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 text-center">
                ソート順
              </th>
              <th
                class="w-1/4 px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 text-center">
              </th>
            </tr>
          </thead>
          <tbody>
            <!-- 新規作成 -->
            @if($isCreating)
            <tr class="bg-blue-50">
              <td class="px-4 py-3">
                <input type="text" wire:model="name" placeholder=""
                  class="w-full border-gray-300 rounded focus:border-blue-500 focus:ring focus:ring-blue-200">
                @error('name')
                <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
              </td>
              <td class="px-4 py-3 text-center">
                <input type="number" wire:model="sort_order" placeholder=""
                  class="w-full border-gray-300 rounded text-center focus:border-blue-500 focus:ring focus:ring-blue-200">
                @error('sort_order')
                <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
              </td>
              <td class="px-4 py-3 text-center">
                <div class="flex gap-2 justify-center">
                  <!-- 保存ボタン -->
                  <button wire:click="saveCategory"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm">
                    保存
                  </button>
                  <!-- キャンセルボタン -->
                  <button wire:click="cancelCreating"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded text-sm">
                    キャンセル
                  </button>
                </div>
              </td>
            </tr>
            @endif

            <!-- 作成済のカテゴリ一覧 -->
            @foreach($categories as $id => $category)
            <tr wire:key="category-{{ $id }}">
              <td class="px-4 py-3">
                <input type="text" wire:model.live.debounce.2000ms="categories.{{ $id }}.name"
                  class="w-full border-gray-300 rounded focus:border-blue-500 focus:ring focus:ring-blue-200">
                @error('categories.' . $id . '.name')
                <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
              </td>
              <td class="px-4 py-3 text-center">
                <input type="number" wire:model.live.debounce.2000ms="categories.{{ $id }}.sort_order"
                  class="w-full border-gray-300 rounded text-center focus:border-blue-500 focus:ring focus:ring-blue-200">
                @error('categories.' . $id . '.sort_order')
                <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
              </td>
              <td class="px-4 py-3 text-center">
                <button wire:click="deleteCategory({{ $id }})"
                  class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">
                  削除
                </button>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </section>
</div>