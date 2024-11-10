<?php
namespace Arbeitszeit;

class ExportModule extends Arbeitszeit {
    public function __construct() {
        parent::__construct();
    }

    public function export($args) {
        $module = $this->getExportModule($args['module']);
        if ($module) {
            return $module->export($args);
        }
        return false;
    }

    public function getExportModule($module) {
        $module = 'Arbeitszeit\\ExportModule\\' . $module;
        if (class_exists($module)) {
            return new $module();
        }
        return false;
    }

    public function getExportModules() {
        $modules = array();
        $path = __DIR__ . '/exports/modules/';
        $files = scandir($path);
        foreach ($files as $file) {
            if (is_file($path . $file)) {
                $module = pathinfo($file, PATHINFO_FILENAME);
                $module = 'Arbeitszeit\\ExportModule\\' . $module;
                if (class_exists($module)) {
                    $module = new $module();
                    $modules[] = array(
                        'name' => $module->getName(),
                        'extension' => $module->getExtension(),
                        'mimeType' => $module->getMimeType(),
                        'version' => $module->getVersion(),
                        'i18n' => $module->geti18n()
                    );
                }
            }
        }
        return $modules;
    }

}