<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Category;
use App\Http\Requests\CategoryStoreRequest;

class CategoryTable extends Component
{
  // === プロパティ ===
  public $categories;
  public $showToast = false;
  public $toastMessage = '';
  public $isCreating = false;

  public $name = '';
  public $sort_order = '';


  // === バリデーション ===
  public function rules()
  {
    return (new CategoryStoreRequest())->rules();
  }

  public function messages()
  {
    return (new CategoryStoreRequest())->messages();
  }

  public function validationAttributes()
  {
    return (new CategoryStoreRequest())->attributes();
  }

  // === ライフサイクルフック ===
  public function mount()
  {
    $categories = Category::getOrderedCategories();

    // IDをキーにした連想配列に変換
    $this->categories = $categories->keyBy('id')->toArray();
  }

  public function updated($propertyName)
  {
    if (str_starts_with($propertyName, 'categories.')) {

      $this->validateOnly($propertyName);

      preg_match('/categories\.(\d+)\.(\w+)/', $propertyName, $matches);

      if (count($matches) === 3) {
        $categoryId = $matches[1];
        $field = $matches[2];

        $category = Category::find($categoryId);
        $category->$field = $this->categories[$categoryId][$field];
        $category->save();

        // ソート順が変更された場合のみ再取得
        if ($field === 'sort_order') {
          $categories = Category::getOrderedCategories();
          $this->categories = $categories->keyBy('id')->toArray();
        }

        // トーストを表示
        $this->showToast = true;
        $this->toastMessage = 'カテゴリを更新しました';
      }
    }
  }

  // === 新規作成関連 ===
  public function startCreating()
  {
    $this->isCreating = true;
    $this->reset(['name', 'sort_order']);
  }

  public function saveCategory()
  {
    $this->validate();

    Category::create([
      'name' => $this->name,
      'sort_order' => $this->sort_order,
    ]);

    // カテゴリリストを再取得
    $categories = Category::getOrderedCategories();
    $this->categories = $categories->keyBy('id')->toArray();

    // 作成モードを終了
    $this->isCreating = false;
    $this->reset(['name', 'sort_order']);

    // トースト表示
    $this->showToast = true;
    $this->toastMessage = 'カテゴリを作成しました';
  }

  public function cancelCreating()
  {
    $this->isCreating = false;
    $this->reset(['name', 'sort_order']);
  }

  // === 削除関連 ===
  public function deleteCategory($categoryId)
  {
    $category = Category::find($categoryId);

    if ($category) {

      // 関連する商品が存在するかチェック
      if ($category->products()->count() > 0) {
        $this->showToast = true;
        $this->toastMessage = 'このカテゴリに関連する商品が存在するため削除できません';
        return;
      }

      $category->delete();

      // カテゴリリストを再取得
      $categories = Category::getOrderedCategories();
      $this->categories = $categories->keyBy('id')->toArray();

      // トースト表示
      $this->showToast = true;
      $this->toastMessage = 'カテゴリを削除しました';
    }
  }

  // === UI制御 ===
  public function hideToast()
  {
    $this->showToast = false;
  }

  // === レンダリング ===
  public function render()
  {
    return view('livewire.category-table');
  }
}