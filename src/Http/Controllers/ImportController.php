<?php

namespace TCoders\KeyValueImporter\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;

class ImportController extends Controller
{
    public function showImportForm($target='key-value')
    {
        return view('importer::import-form', compact('target'));
    }

    public function handleImport(Request $request, $target)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:xlsx,csv,txt',
        ]);

        $collection = Excel::toCollection(null, $request->file('import_file'))->first();

        if ($collection->isEmpty()) {
            return redirect()->back()->with('error', 'The import file is empty.');
        }

        $header = $collection->first(); // First row is header
        $collection->shift(); // Remove header row from data

        if (count($header) < 3 || strtolower(trim($header[0])) !== 'personalities') {
            return redirect()->back()->with('error', 'Header must start with "personalities", followed by other keys.');
        }

        $result = [];

        foreach ($collection as $row) {
            $tag = trim($row[0] ?? '');

            if (!$tag) {
                continue; // Skip if tag is empty
            }

            $result[$tag] = [];
            $result[$tag]['summary'] = $row[1];
            $result[$tag]['description'] = $row[2];
        }

        $filePath = config('importer.storage_path') . "/{$target}.php";

        if (!File::exists(dirname($filePath))) {
            File::makeDirectory(dirname($filePath), 0755, true);
        }
        Cache::rememberForever(config('importer.cache_prefix'.$target), function () use ($result) {
            return $result;
        });

        File::put($filePath, "<?php\n\nreturn " . var_export($result, true) . ";\n");

        if (config('importer.cache_enabled')) {
            Cache::forget(config('importer.cache_prefix') . $target);
        }

        return redirect()->to('/');
    }
}
