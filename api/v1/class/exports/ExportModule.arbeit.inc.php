<?php
namespace Arbeitszeit;
use Arbeitszeit\Events\EventDispatcherService;
use Arbeitszeit\Events\generatedExportEvent;

class ExportModule extends Arbeitszeit {
    public function __construct() {
        parent::__construct();
    }

    /**
     * `export()` - Runs the specified export module
     * @param mixed $args Arguments to pass to the export module, required: $args['module'], e.g. `PDFExportModule`
     * @return mixed
     */
    public function export($args) {
        Exceptions::error_rep("Exporting data using module: {$args['module']}", 1, "N/A");
        $module = $this->getExportModule($args['module']);
        if ($module) {
            EventDispatcherService::get()->dispatch(new generatedExportEvent($_SESSION["username"], $args['module']), generatedExportEvent::NAME);
            return $module->export($args);
        }
        return false;
    }

    /**
     * `getExportModule()` - Returns an instance of the specified export
     * @param mixed $moduleName The name of the export module to return
     * @return bool|object
     */
    public function getExportModule($moduleName) {
        Exceptions::error_rep("Loading export module: {$moduleName}", 1, "N/A");
        $moduleClass = 'Arbeitszeit\\ExportModule\\' . $moduleName;
        $classFilePath = __DIR__ . "/modules/{$moduleName}/{$moduleName}.em.arbeit.inc.php";
        if (file_exists($classFilePath)) {
            require_once $classFilePath;
            if (class_exists($moduleClass)) {
                Exceptions::error_rep("Export module loaded: {$moduleName}", 1, "N/A");
                return new $moduleClass();
            }
        }
        Exceptions::error_rep("Could not load export module: {$moduleName}", 1, "N/A");
        return false;
    }

    /**
     * `getExportModules()` - Returns an array of available export modules
     * @return array
     */
    public function getExportModules() {
        Exceptions::error_rep("Loading export modules...", 1, "N/A");
        $modules = array();
        $basePath = __DIR__ . '/modules/';
        $directories = glob($basePath . '*ExportModule', GLOB_ONLYDIR);

        foreach ($directories as $dir) {
            $moduleName = basename($dir);
            $classFilePath = "{$dir}/{$moduleName}.em.arbeit.inc.php";
            $moduleClass = 'Arbeitszeit\\ExportModule\\' . $moduleName;

            if (file_exists($classFilePath)) {
                require_once $classFilePath;
                if (class_exists($moduleClass)) {
                    $moduleInstance = new $moduleClass();
                    $modules[] = array(
                        'name' => $moduleInstance->getName(),
                        'extension' => $moduleInstance->getExtension(),
                        'mimeType' => $moduleInstance->getMimeType(),
                        'version' => $moduleInstance->getVersion(),
                        'i18n' => $moduleInstance->geti18n(),
                        'classFile' => $classFilePath
                    );
                }
            }
        }
        return $modules;
    }
}
