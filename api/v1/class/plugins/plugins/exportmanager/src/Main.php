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

    function scan_worktime_sheets() {
        $search_path = $_SERVER["DOCUMENT_ROOT"] . "/data/exports";
        $allowed_extensions = [
            "csv" => "Spreadsheet (CSV)",
            "pdf" => "PDF Document",
            "docx" => "Word-Document",
            "png" => "Picture (PNG)",
            "jpg" => "Picture (JPG)",
            "jpeg" => "Picture (JPEG)"
        ];
    
        $files = [];

        $rii = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($search_path, \FilesystemIterator::SKIP_DOTS));
    
        foreach ($rii as $file) {
            if ($file->isFile()) {
                $ext = strtolower($file->getExtension());
    
                if (isset($allowed_extensions[$ext])) {
                    $relative_path = str_replace($_SERVER["DOCUMENT_ROOT"], '', $file->getPathname());
                    
                    $path_parts = explode(DIRECTORY_SEPARATOR, str_replace($search_path . DIRECTORY_SEPARATOR, '', $file->getPathname()));
                    
                    if (count($path_parts) >= 2) {
                        $username = $path_parts[1];
                    } else {
                        $username = "Unknown";
                    }
    
                    $files[] = [
                        "filename" => $file->getFilename() . " - " . $username,
                        "extension" => $allowed_extensions[$ext],
                        "download_url" => "http://" . Arbeitszeit::get_app_ini()["general"]["base_url"] . $relative_path
                    ];
                }
            }
        }
    
        return $files;
    }

    public static function get_user_worktime_sheets() {
        $search_path = $_SERVER["DOCUMENT_ROOT"] . "/data/exports";
        $allowed_extensions = [
            "csv" => "Spreadsheet (CSV)",
            "pdf" => "PDF Document",
            "docx" => "Word-Document",
            "png" => "Picture (PNG)",
            "jpg" => "Picture (JPG)",
            "jpeg" => "Picture (JPEG)"
        ];
    
        $files = [];
        $current_user = $_SESSION["username"] ?? null;
    
        if (!$current_user) {
            Exceptions::error_rep("No user logged in?");
            return [];
        }
    
        $rii = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($search_path, \FilesystemIterator::SKIP_DOTS));
    
        foreach ($rii as $file) {
            if ($file->isFile()) {
                $ext = strtolower($file->getExtension());
    
                if (isset($allowed_extensions[$ext])) {
                    $relative_path = str_replace($_SERVER["DOCUMENT_ROOT"], '', $file->getPathname());
                        $path_parts = explode(DIRECTORY_SEPARATOR, str_replace($search_path . DIRECTORY_SEPARATOR, '', $file->getPathname()));
    
                    if (count($path_parts) >= 2) {
                        $username = $path_parts[1];
                    } else {
                        $username = "Unknown";
                    }
                        if ($username === $current_user) {
                        $files[] = [
                            "filename" => $file->getFilename() . " - " . $username,
                            "extension" => $allowed_extensions[$ext],
                            "download_url" => "http://" . Arbeitszeit::get_app_ini()["general"]["base_url"] . $relative_path
                        ];
                    }
                }
            }
        }
    
        return $files;
    }
    

    function generate_worktime_table($files) {
        if (empty($files)) {
            return "<tr><td colspan='3'>No Files found</td></tr>";
        }
    
        $html = "";
    
        foreach ($files as $file) {
            $html .= "<tr>
                        <td>{$file['filename']}</td>
                        <td>{$file['extension']}</td>
                        <td><a class='btn btn-primary' role='button' href='{$file['download_url']}' target='_blank'>Download</a></td>
                      </tr>";
        }
    
        return $html;
    }
    
    
}