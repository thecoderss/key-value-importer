<?php

namespace TCoders\KeyValueImporter;

class ImportServiceProvider {
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/importer.php' => config_path('importer.php'),
        ], 'config');

        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        $this->loadViewsFrom(__DIR__.'/views', 'importer');

        \Blade::directive('importButton', function ($target) {
            return "<?php echo view('importer::button', ['target' => $target]); ?>";
        });
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/config/importer.php', 'importer');
    }
}
