<?php

namespace Company\PackageB\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\File;

class ImportController extends Controller
{
    public function showImportForm($target)
    {
        return view('importer::import-form', compact('target'));
    }

    public function handleImport(Request $request, $target)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:xlsx,csv,txt',
        ]);

        $collection = Excel::toCollection(null, $request->file('import_file'))->first();

        $data = [];

        foreach ($collection as $row) {
            if (isset($row[0], $row[1])) {
                $data[$row[0]] = $row[1];
            }
        }

        $filePath = config('importer.storage_path') . "/{$target}.php";

        if (!File::exists(dirname($filePath))) {
            File::makeDirectory(dirname($filePath), 0755, true);
        }

        File::put($filePath, "<?php\n\nreturn " . var_export($data, true) . ";\n");

        if (config('importer.cache_enabled')) {
            cache()->forget(config('importer.cache_prefix') . $target);
        }

        return redirect()->back()->with('success', 'Import completed.');
    }
}
