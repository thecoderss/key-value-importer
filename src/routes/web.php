<?php

use Illuminate\Support\Facades\Route;
use TCoders\KeyValueImporter\Http\Controllers\ImportController;

Route::middleware(['web', 'auth'])->prefix('import')->group(function () {
    Route::get('key-value-to-file/{target?}', [ImportController::class, 'showImportForm'])->name('import.form');
    Route::post('key-value-to-file/{target?}', [ImportController::class, 'handleImport'])->name('import.handle');
});