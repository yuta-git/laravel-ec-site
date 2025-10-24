<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Category;
use App\Http\Requests\CategoryStoreRequest;

use Livewire\WithPagination;
use Illuminate\Support\Facades\Validator;


class CategoryTable extends Component
{

  use WithPagination;

  // === プロパティ ===
  // 例: もし内部でのみ使用するプロパティがあれば protected にする
  // protected array $internalCache = [];
  public bool $showToast = false;
  public string $toastMessage = '';
  public bool $isCreating = false;

  public string $name = '';

  public ?int $sortOrder = 0;

  // 各カテゴリごとの編集中の値を保持
  public array $editingCategories = [];


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


  public function updateField($categoryId, $field, $value)
  {
    // 存在しないデータへの不正アクセスは404エラーとして返す
    $category = Category::findOrFail($categoryId);

    // ★★★ 初回アクセス時に updated_at を記録 ★★★
    if (!isset($this->editingCategories[$categoryId])) {
      $this->editingCategories[$categoryId] = [
        'original_updated_at' => $category->updated_at->toJSON(),
      ];
    }

    $this->editingCategories[$categoryId][$field] = $value;

    // 入力値の前処理
    if ($field === 'name') {
      $value = trim($value);
    }

    // バリデーション用の値を取得
    $nameValue = $this->editingCategories[$categoryId]['name'] ?? $category->name;
    $sortOrderValue = $this->editingCategories[$categoryId]['sort_order'] ?? $category->sort_order;
    

    // バリデーション
    $validator = Validator::make([
      'name' => $nameValue,
      'sortOrder' => $sortOrderValue,
    ], $this->rules(), [], $this->validationAttributes());


    if ($validator->fails()) {
      foreach ($validator->errors()->messages() as $key => $messages) {
        $errorKey = "category.{$categoryId}.{$key}";
        $this->addError($errorKey, $messages[0]);
      }
      return;
    }

    // ★★★ 楽観ロックチェック ★★★
    $category->refresh();
    $savedOriginalUpdatedAt = $this->editingCategories[$categoryId]['original_updated_at'];

    if ($category->updated_at->toJSON() !== $savedOriginalUpdatedAt) {
      $this->addError("category.{$categoryId}.conflict", '他のユーザーによって更新されています。画面を再読み込みしてください。');
      unset($this->editingCategories[$categoryId]);
      return;
    }

    // 保存
    if ($field === 'sort_order') {
      $category->sort_order = (int)$value;
    } else {
      $category->name = $value;
    }

    $category->save();

    $this->editingCategories[$categoryId]['original_updated_at'] =
      $category->updated_at->format('Y-m-d H:i:s');

    $this->resetErrorBag("category.{$categoryId}");

    $this->showToast = true;
    $this->toastMessage = 'カテゴリを更新しました';
  }


  // === 新規作成関連 ===
  public function startCreating()
  {
    $this->isCreating = true;
    $this->reset(['name', 'sortOrder']);
  }

  public function saveCategory()
  {
    $this->validate();

    Category::create([
      'name' => $this->name,
      'sort_order' => $this->sortOrder,
    ]);

    // 作成モードを終了
    $this->isCreating = false;
    $this->reset(['name', 'sortOrder']);

    // トースト表示
    $this->showToast = true;
    $this->toastMessage = 'カテゴリを作成しました';
  }

  public function cancelCreating()
  {
    $this->isCreating = false;
    $this->reset(['name', 'sortOrder']);
  }

  // === 削除関連 ===
  public function deleteCategory($categoryId)
  {
    $category = Category::find($categoryId);

    if ($category) {

      // 関連する商品が存在するかチェック
      if ($category->products()->exists()) {
        $this->showToast = true;
        $this->toastMessage = 'このカテゴリに関連する商品が存在するため削除できません';
        return;
      }

      $category->delete();

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
    $categories = Category::query()->orderBy('sort_order')->paginate(10);
    return view('livewire.category-table', compact('categories'));
  }
}