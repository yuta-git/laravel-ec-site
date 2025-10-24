<div class="p-6">
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

        <table class="table-auto w-full text-left whitespace-nowrap">
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
                <span class="block text-red-500 text-xs">{{ $message }}</span>
                @enderror
              </td>
              <td class="px-4 py-3 text-center">
                <input type="number" wire:model.number.defer="sortOrder" min="0" step="1" inputmode="numeric"
                  placeholder=""
                  class="w-full border-gray-300 rounded text-center focus:border-blue-500 focus:ring focus:ring-blue-200">
                @error('sortOrder')
                <span class="block text-red-500 text-xs">{{ $message }}</span>
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
            @forelse($categories as $category)
            <tr wire:key="category-{{ $category->id }}">
              <td class="px-4 py-3">
                <div>
                  <input type="text" value="{{ $editingCategories[$category->id]['name'] ?? $category->name }}"
                    wire:change="updateField({{ $category->id }}, 'name', $event.target.value)"
                    class=" w-full border-gray-300 rounded focus:border-blue-500 focus:ring focus:ring-blue-200">
                  @error('category.' . $category->id . '.name')
                  <span class="block text-red-500 text-xs">{{ $message }}</span>
                  @enderror
                </div>
              </td>
              <td class="px-4 py-3 text-center">
                <div>
                  <input type="number"
                    value="{{ $editingCategories[$category->id]['sort_order'] ?? $category->sort_order }}"
                    wire:change="updateField({{ $category->id }}, 'sort_order', $event.target.value)" min="0" step="1"
                    inputmode="numeric" class="w-full border-gray-300 rounded text-center focus:border-blue-500
                  focus:ring focus:ring-blue-200">
                  @error('category.' . $category->id . '.sortOrder')
                  <span class="block text-red-500 text-xs">{{ $message }}</span>
                  @enderror
                  <!-- 楽観ロックのエラー -->
                  @error('category.' . $category->id . '.conflict')
                  <span class="block text-red-500 text-xs">{{ $message }}</span>
                  @enderror
                </div>
              </td>
              <td class="px-4 py-3 text-center">
                <button x-on:click.prevent="if(confirm('削除しますか?')) $wire.deleteCategory('{{ $category->id }}')"
                  wire:loading.attr="disabled" wire:target="deleteCategory"
                  wire:loading.class="opacity-50 pointer-events-none"
                  class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">
                  削除
                </button>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="3" class="text-center text-gray-500">カテゴリはありません</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
    {{ $categories->links() }}
  </section>
</div>