<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;  
use App\Models\Import;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    public function progress(Import $import)
    {
        return response()->json([
            'progress' => $import->progress,
            'status' => $import->status,
            'imported_count' => $import->imported_count ?? 0,
            'error_message' => $import->error_message ?? null,
        ]);
    }
}