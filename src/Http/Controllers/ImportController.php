<?php

namespace TCoders\KeyValueImporter\Http\Controllers;

use App\Models\File as DbFile;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

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
        $file = $request->file('import_file');
        $collection = Excel::toCollection(null, $file)->first();

        if ($collection->isEmpty()) {
            return redirect()->back()->with('error', 'The import file is empty.');
        }

        $header = $collection->first(); // First row is header
        $collection->shift(); // Remove header row from data

        if (count($header) < 3 || strtolower(trim($header[0])) !== 'personalities') {
            return redirect()->back()->with('error', 'Header must start with "personalities", followed by other keys.');
        }
        $normalizedKeys = collect($header)->map(function ($key) {
            return trim($key);
        })->filter(function ($key) {
            return strpos($key, '!') === false; // Exclude keys with '!'
        })->map(function ($key) {
            return strtolower(str_replace(' ', '-', $key));
        })->values()->toArray(); 
        
        $validIndexes = collect($header)->keys()->filter(function ($index) use ($header) {
            return strpos($header[$index], '!') === false;
        })->values()->toArray();
        $result = [];

        foreach ($collection as $row) {
            $tag = trim($row[0] ?? '');

            if (!$tag) {
                continue; // Skip if tag is empty
            }

            $result[$tag] = [];

            foreach ($validIndexes as $i => $originalIndex) {
                $key = $normalizedKeys[$i];
                $result[$tag][$key] = $row[$originalIndex] ?? null;
            }

        }

        $store = Storage::disk('s3')->put("imports/{$target}.json", json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        DbFile::insert([
            'name' => $file->getClientOriginalName(),    
            'tag' => $request->tag ?: 'upload',    
            'path' => $store,
        ]);
        return redirect()->to('/');
    }
}
