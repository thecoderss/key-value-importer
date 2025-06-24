<?php

use Illuminate\Support\Facades\Route;
use Company\PackageB\Http\Controllers\ImportController;

Route::middleware(['web', 'auth'])->prefix('import')->group(function () {
    Route::get('{target}', [ImportController::class, 'showImportForm'])->name('import.form');
    Route::post('{target}', [ImportController::class, 'handleImport'])->name('import.handle');
});