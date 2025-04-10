<?php

declare(strict_types=1);

namespace Example;

use Arbeitszeit;
use Arbeitszeit\PluginBuilder;

class MyPlugin extends PluginBuilder {


    private string $log_append;

    private array $plugin_configuration;

    public function set_log_append(): void{
        $v = $this->read_plugin_configuration("example")["version"];
        $this->log_append = "[ExamplePlugin v{$v}]";
    }

    public function get_log_append(): string{
        return $this->log_append;
    }

    public function set_plugin_configuration(): void{
        $this->plugin_configuration = $this->read_plugin_configuration("Example");
    }

    public function get_plugin_configuration(): array{
        return $this->plugin_configuration;
    }

    public function __construct(){
        $this->set_log_append();
        $this->set_plugin_configuration();
    }

    public function onLoad(): void{
        $lga = $this->get_log_append();
        $this->logger("{$lga} Loading example plugin...");
    }

    public function onEnable(): void{
        
    }

    public function onDisable(): void{
        
    }
}


?>