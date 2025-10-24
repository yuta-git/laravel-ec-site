<?php

namespace App\Traits;

trait SearchableTrait
{
  // スペース区切りで配列化
  private function splitSpaceToArray($string)
  {
    $input = str_replace('　', ' ', $string); // 全角スペースを半角に統一
    $input = trim($input);

    $splited = preg_split('/[\s]+/', $input, -1, PREG_SPLIT_NO_EMPTY);

    return collect($splited)
      ->filter(fn($v) => mb_strlen($v) <= 20) // 20文字以下に制限
      ->unique()
      ->take(10)
      ->values() // インデックスを0から振り直し
      ->all(); // コレクションを配列に変換
  }

  // LIKE検索用のワイルドカード文字をエスケープする
  private function escapeLikeWildcards($value)
  {
    $value = str_replace('\\', '\\\\', $value);  // バックスラッシュをエスケープ
    $value = str_replace('%',  '\%',  $value);
    $value = str_replace('_',  '\_',  $value);

    return $value;
  }
}