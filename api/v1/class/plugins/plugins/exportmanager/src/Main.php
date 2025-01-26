<?php
declare(strict_types=1);
namespace exportmanager;
use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Benutzer;
use Arbeitszeit\Exceptions;
use Arbeitszeit\ExportModule;
use Arbeitszeit\PluginBuilder;
use Arbeitszeit\PluginInterface;
use Toil\CustomRoutes;

class exportmanager extends PluginBuilder implements PluginInterface {

    public string $log_append;

    private array $plugin_configuration;


    public function set_log_append(): void{
        $v = $this->read_plugin_configuration("exportmanager")["version"];
        $this->log_append = "[exportmanager v{$v}]";
    }

    public function get_log_append(): string{
        return $this->log_append;
    }

    public function set_plugin_configuration(): void{
        $this->plugin_configuration = $this->read_plugin_configuration("exportmanager");
    }

    public function get_plugin_configuration(): array{
        return $this->plugin_configuration;
    }

    public function onDisable(): void{
        $this->log_append = $this->get_log_append();
    }

    public function onEnable(): void{
    }

    public function onLoad(): void{
    }

    public function __construct(){
        $this->set_log_append();
        $this->set_plugin_configuration();

        $this->setup();

    }

    public function setup(): void{
        $this->register_routes();
    }

    public function register_routes(): void{
        Exceptions::error_rep("{$this->log_append} Registering custom routes...");
        CustomRoutes::registerCustomRoute("exportmanager", "/api/v1/class/plugins/plugins/exportmanager/src/routes/ExportManager.ep.toil.arbeit.inc.php", 1);
    }

    public function getAllExportModulesHtml(): void{
        Exceptions::error_rep("{$this->log_append} Rendering export modules html...");
        $exportModule = new ExportModule;
        foreach($exportModule->getExportModules() as $module){
            $module["classFile"] = "N/A";
            echo "<tr><td>{$module["name"]}</td><td>{$module["extension"]} / Export Plugin</td><td>{$module["classFile"]}</td><td><a class=\"btn btn-primary\" role=\"button\" href=\"?export=true&plugin={$module["name"]}&action=default\" target=\"_blank\">Export</a></td></tr>";
        }
    }

    public function defaultExport($plugin): void{
        Exceptions::error_rep("{$this->log_append} Starting default export for {$plugin}...");
        $exportModule = new ExportModule;
        if($plugin == "PDFExportModule"){
           echo $exportModule->export(["module" => $plugin, "year" => date("Y"), "month" => date("m"), "user" => $_SESSION["username"]]);
           die();
        }
        $exportModule->export(["module" => $plugin, "year" => date("Y"), "month" => date("m"), "user" => $_SESSION["username"]]);

    }
}