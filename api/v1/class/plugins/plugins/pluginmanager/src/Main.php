<?php
declare(strict_types=1);
namespace pluginmanager;
use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Benutzer;
use Arbeitszeit\Exceptions;
use Arbeitszeit\ExportModule;
use Arbeitszeit\PluginBuilder;
use Arbeitszeit\PluginInterface;
use Toil\CustomRoutes;
use Symfony\Component\Yaml\Yaml;

class pluginmanager extends PluginBuilder implements PluginInterface {

    public string $log_append;

    private array $plugin_configuration;

    private $arbeitszeit;


    public function set_log_append(): void{
        $v = $this->read_plugin_configuration("pluginmanager")["version"];
        $this->log_append = "[pluginmanager v{$v}]";
    }

    public function get_log_append(): string{
        return $this->log_append;
    }

    public function set_plugin_configuration(): void{
        $this->plugin_configuration = $this->read_plugin_configuration("pluginmanager");
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

        $this->arbeitszeit = new Arbeitszeit();
        CustomRoutes::registerCustomRoute("pluginmanager", "/api/v1/class/plugins/plugins/pluginmanager/src/routes/pluginmanager.ep.toil.arbeit.inc.php", 1);

    }

    public function getPluginsHtml(): array{
        Exceptions::error_rep("{$this->log_append} Rendering plugins html...");
        $pl = new PluginBuilder();
        // [pluginname] => array plugin configuration
        $plugins = $pl->get_plugins();

        $html = [];
        $counter = 0;
        foreach ($plugins["plugins"] as $plugin_name => $plugin_config) {
            Exceptions::error_rep("{$this->log_append} Plugin: {$plugin_config["name"]} rendering...");
            if ($counter % 3 == 0) {
            if ($counter > 0) {
                $html[] = '</div>';
            }
            $html[] = '<div class="card-group border rounded-0">';
            }

            $html[] = '<div class="card">';
            $html[] = '<div class="card-body">';
            $html[] = '<h4 class="card-title">' . htmlspecialchars((string)$plugin_config["name"]) . '</h4>';
            $html[] = '<p class="card-text">' . htmlspecialchars($plugin_config['description']) . '<br><br>Version: ' . htmlspecialchars($plugin_config['version']) . ' | Author: ' . htmlspecialchars($plugin_config['author']) . '<br>Enabled: ' . htmlspecialchars((bool)$plugin_config["enabled"] ? "true" : "false") . '</p>';
            $html[] = '<a class="btn btn-primary" role="button" href="/api/v1/toil/pluginmanager?edit=' . htmlspecialchars((string)$plugin_config["name"]) .'">Edit</a>';
            $html[] = '</div>';
            $html[] = '</div>';

            $counter++;
        }

        if ($counter > 0) {
            $html[] = '</div>';
        }

        return $html;
    }

    public function getPluginYaml(string $plugin_name): string{
        Exceptions::error_rep("{$this->log_append} Reading plugin configuration: {$plugin_name}...");
        $pl = new PluginBuilder();
        return $pl->read_plugin_configuration($plugin_name, true);
    }

    public function enablePlugin(string $plugin_name){
        Exceptions::error_rep("{$this->log_append} Enabling plugin: {$plugin_name}...");
        $pl = new PluginBuilder();

        if($pl->read_plugin_configuration($plugin_name)){
            Exceptions::error_rep("{$this->log_append} Plugin: {$plugin_name} found, enabling...");
            $conf = $pl->read_plugin_configuration($plugin_name);
            $conf["enabled"] = true;
            file_put_contents($conf["path"], Yaml::dump(input: $conf, flags: Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK));
        }
    }

    public function disablePlugin(string $plugin_name){
        Exceptions::error_rep("{$this->log_append} Disabling plugin: {$plugin_name}...");
        $pl = new PluginBuilder();

        if($pl->read_plugin_configuration($plugin_name)){
            Exceptions::error_rep("{$this->log_append} Plugin: {$plugin_name} found, disabling...");
            $conf = $pl->read_plugin_configuration($plugin_name);
            $conf["enabled"] = false;
            file_put_contents($conf["path"], Yaml::dump(input: $conf, flags: Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK));
        }
    }

    public function pluginIsEnabledHtml(string $plugin_name){
        Exceptions::error_rep("{$this->log_append} Checking if plugin: {$plugin_name} is enabled...");
        $pl = new PluginBuilder();
        $conf = $pl->read_plugin_configuration($plugin_name);
        if($conf["enabled"]){
            return 'checked';
        }else{
            return '';
        }
    }

    public function pluginIsDisabledHtml(string $plugin_name){
        Exceptions::error_rep("{$this->log_append} Checking if plugin: {$plugin_name} is disabled...");
        $pl = new PluginBuilder();
        $conf = $pl->read_plugin_configuration($plugin_name);
        if(!$conf["enabled"]){
            return 'checked';
        }else{
            return '';
        }
    }

}