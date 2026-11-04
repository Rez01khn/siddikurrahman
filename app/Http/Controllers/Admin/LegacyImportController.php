<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LegacyImportBatch;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LegacyImportController extends Controller
{
    public function index(): View
    {
        return view('admin.legacy.index', ['title' => 'Legacy Content Review', 'batches' => LegacyImportBatch::latest()->paginate(15)]);
    }

    public function show(Request $request, LegacyImportBatch $batch): View
    {
        $f = $request->validate(['section' => ['nullable', 'string', 'max:80'], 'state' => ['nullable', 'in:review,draft,linked,archived']]);
        $q = $batch->entries();
        foreach (['section', 'state'] as $key) {
            if (! empty($f[$key])) {
                $q->where($key, $f[$key]);
            }
        }

        return view('admin.legacy.show', ['title' => 'Legacy Content Review', 'batch' => $batch, 'entries' => $q->orderBy('id')->paginate(20)->withQueryString(), 'sections' => $batch->entries()->distinct()->orderBy('section')->pluck('section')]);
    }

    public function download(LegacyImportBatch $batch): BinaryFileResponse
    {
        abort_unless(preg_match('/\A[a-f0-9]{64}\z/', $batch->source_sha256), 404);
        $path = storage_path('app/private/legacy/sources/'.$batch->source_sha256.'.json');
        abort_unless(is_file($path) && hash_equals($batch->source_sha256, hash_file('sha256', $path)), 404);

        return response()->download($path, $batch->original_filename, ['Content-Type' => 'application/json', 'Cache-Control' => 'no-store, private']);
    }
}
