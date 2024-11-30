<?php
namespace Arbeitszeit;

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
        $module = $this->getExportModule($args['module']);
        if ($module) {
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
        $moduleClass = 'Arbeitszeit\\ExportModule\\' . $moduleName;
        $classFilePath = __DIR__ . "/modules/{$moduleName}/{$moduleName}.em.arbeit.inc.php";
        if (file_exists($classFilePath)) {
            require_once $classFilePath;
            if (class_exists($moduleClass)) {
                return new $moduleClass();
            }
        }
        return false;
    }

    /**
     * `getExportModules()` - Returns an array of available export modules
     * @return array
     */
    public function getExportModules() {
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
