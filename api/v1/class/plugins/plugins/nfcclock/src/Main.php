<?php

declare(strict_types=1);

namespace NFCClock;

use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Benutzer;
use Arbeitszeit\Exceptions;
use Arbeitszeit\PluginBuilder;
use Arbeitszeit\PluginInterface;
use Toil\CustomRoutes;

class NFCClock extends PluginBuilder implements PluginInterface {

    public string $log_append;
    private array $plugin_configuration;
    public $setup;
    public $code;

    public function __construct() {
        $this->set_plugin_configuration();
        $this->set_log_append();

        $this->setup();
    }

    public function setup(): void {
        $this->register_routes();
    }

    public function register_routes(): void {
        Exceptions::error_rep("{$this->log_append} Registering custom routes...");
        CustomRoutes::registerCustomRoute("nfcclock", "/api/v1/class/plugins/plugins/nfcclock/views/routes/nfcclock.ep.toil.arbeit.inc.php", 2);
        CustomRoutes::registerCustomRoute("nfcclocksettings", "/api/v1/class/plugins/plugins/nfcclock/views/routes/settings.nfcclock.ep.toil.arbeit.inc.php", 2);
    }

    public function set_log_append(): void {
        $v = $this->read_plugin_configuration("nfcclock")["version"] ?? "unknown";
        $this->log_append = "[nfcclock v{$v}]";
    }

    public function get_log_append(): string {
        return $this->log_append;
    }

    public function set_plugin_configuration(): void {
        $this->plugin_configuration = $this->read_plugin_configuration("nfcclock");
    }

    public function get_plugin_configuration(): array {
        return $this->plugin_configuration;
    }

    public function onDisable(): void {
        $this->log_append = $this->get_log_append();
    }

    public function onEnable(): void {}

    public function onLoad(): void {}

}
