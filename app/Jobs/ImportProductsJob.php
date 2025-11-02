<?php

namespace App\Jobs;

use App\Models\Product;
use App\Models\Import;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use League\Csv\Reader;

class ImportProductsJob implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  public $importId;
  public $filePath;


  /**
   * Create a new job instance.
   */
  public function __construct($importId, $filePath)
  {
    $this->importId = $importId;
    $this->filePath = $filePath;
  }

  /**
   * Execute the job.
   */
  public function handle(): void
  {
    try {
      DB::beginTransaction();

      // インポートレコード取得
      $import = Import::findOrFail($this->importId);

      // ステータスを「処理中」に更新
      $import->update(['status' => 'processing', 'progress' => 0]);

      // StorageからCSVファイルの実際のパスを取得
      $fullPath = Storage::path($this->filePath);

      // League CSVでファイルを読み込む
      $csv = Reader::from($fullPath, 'r');

      // データ行を連想配列形式で1行ずつ処理できる状態にする(イテレータに変換)
      $csv->setHeaderOffset(0);

      // 文字エンコード変換フィルタを追加(Shift-JIS対応)
      $inputBom = $csv->getInputBOM();
      if ($inputBom === Reader::BOM_UTF16_LE || $inputBom === Reader::BOM_UTF16_BE) {
        $csv->appendStreamFilterOnRead('convert.iconv.UTF-16/UTF-8');
      }

      // Shift-JISの場合
      $sample = file_get_contents($fullPath, false, null, 0, 1024);
      if (mb_detect_encoding($sample, 'UTF-8, SJIS, SJIS-win', true) === 'SJIS-win') {
        $csv->appendStreamFilterOnRead('convert.iconv.SJIS-win/UTF-8');
      }


      $importCount = 0;
      $errorMessages = [];
      $insertData = [];

      // SplFileObjectで行数だけカウント（メモリ節約）
      $file = new \SplFileObject($fullPath);
      $file->seek(PHP_INT_MAX);
      $total = $file->key();

      // ヘッダーをキーにした連想配列をループで回す
      foreach ($csv->getRecords() as $index => $row) {
        // 空行スキップ。array_filter($row): 空でない要素のみ残す
        if (empty(array_filter($row))) {
          continue;
        }

        // CSVファイル上の実際の行番号(ヘッダー=1行目、データ開始=2行目)
        $rowNumber = $index + 2;

        // 各行のバリデーション
        $validator = Validator::make($row, [
          'name' => 'required|string|max:255',
          'description' => 'nullable|string|max:2000',
          'price' => 'required|integer|min:0|max:99999999',
          'stock' => 'required|integer|min:0|max:999999',
          'category_id' => 'required|exists:categories,id',
        ]);

        if ($validator->fails()) {
          foreach ($validator->errors()->all() as $error) {
            $errorMessages[] = "{$rowNumber}行目: {$error}";
          }
          continue;
        }

        // バルクインサートのため配列で取得しておく
        $insertData[] = array_merge($row, [
          'uuid' => Str::uuid()->toString(),
          'created_at' => now(),
          'updated_at' => now(),
        ]);
        $importCount++;

        // 進捗更新（10行ごとなど、適度な間隔で更新）
        if ($index % 10 === 0 || $index === $total - 1) {
          $import->update(['progress' => floor(($index + 1) / $total * 100)]);
        }
      }

      // エラーが1件でもある場合、トランザクションをロールバックし失敗として記録
      if (!empty($errorMessages)) {
        DB::rollBack();
        $import->update([
          'status' => 'failed',
          'error_message' => 'CSVのインポート中にエラーが発生しました:' . "\n" . implode("\n", $errorMessages)
        ]);
        Log::error('CSV Import failed', ['errors' => $errorMessages]);
        return;
      }

      // データ挿入
      if (!empty($insertData)) {
        try {
          Product::insert($insertData);
        } catch (\Exception $e) {
          DB::rollBack();
          Log::error('Bulk Insert Error: ' . $e->getMessage());
          $import->update([
            'status' => 'failed',
            'error_message' => 'データベース挿入に失敗しました: ' . $e->getMessage()
          ]);
          return;
        }
      }

      // インポート件数が0の場合
      if ($importCount === 0) {
        DB::rollBack();
        $import->update([
          'status' => 'failed',
          'error_message' => 'インポート可能なデータがありませんでした'
        ]);
        return;
      }

      DB::commit();

      // 完了ステータス更新
      $import->update([
        'status' => 'done',
        'progress' => 100,
        'imported_count' => $importCount
      ]);

      Log::info("CSV Import completed: {$importCount} products imported");
    } catch (\Exception $e) {
      DB::rollBack();
      Log::error('CSV Import Job Error: ' . $e->getMessage());

      $import = Import::find($this->importId);
      if ($import) {
        $import->update([
          'status' => 'failed',
          'error_message' => $e->getMessage()
        ]);
      }
    }
  }
}