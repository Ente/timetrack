<?php
declare(strict_types=1);
namespace CodeClock;
use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Benutzer;
use Arbeitszeit\PluginBuilder;
use Arbeitszeit\PluginInterface;
use Toil\CustomRoutes;
class codeclock extends PluginBuilder implements PluginInterface {

    public string $log_append;

    private array $plugin_configuration;

    private array $default_payload = [
        "id" => null,
        "username" => null,
        "pin" => null,
    ];

    private string $mastertoken;

    public $setup;

    public $code;

    public function set_log_append(): void{
        $v = $this->read_plugin_configuration("codeclock")["version"];
        $this->log_append = "[codeclock v{$v}]";
    }

    public function get_log_append(): string{
        return $this->log_append;
    }

    public function set_plugin_configuration(): void{
        $this->plugin_configuration = $this->read_plugin_configuration("codeclock");
    }

    public function get_plugin_configuration(): array{
        return $this->plugin_configuration;
    }

    public function __construct(){

        #$this->code = new Code();
        #$this->setup = new Setup();

        Setup::done();
    }

    public function onDisable(): void{
        $this->log_append = $this->get_log_append();

    }

    public function onEnable(): void{
    }

    public function onLoad(): void{
    }
}