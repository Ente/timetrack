<?php

declare(strict_types=1);

namespace NFClogin;

use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Benutzer;
use Arbeitszeit\Exceptions;
use Arbeitszeit\PluginBuilder;
use Arbeitszeit\PluginInterface;
use Toil\CustomRoutes;
use \COM;

class NFClogin extends PluginBuilder implements PluginInterface {

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
        CustomRoutes::registerCustomRoute("readNfc", "/api/v1/class/plugins/plugins/nfclogin/src/routes/readNfc.ep.toil.arbeit.inc.php", 1);
        CustomRoutes::registerCustomRoute("writeNfc", "/api/v1/class/plugins/plugins/nfclogin/src/routes/writeNfc.ep.toil.arbeit.inc.php", 1);
        CustomRoutes::registerCustomRoute("readBlock4", "/api/v1/class/plugins/plugins/nfclogin/src/routes/readBlock4.ep.toil.arbeit.inc.php", 1);
    }

    public function set_log_append(): void {
        $v = $this->read_plugin_configuration("nfclogin")["version"] ?? "unknown";
        $this->log_append = "[nfclogin v{$v}]";
    }

    public function get_log_append(): string {
        return $this->log_append;
    }

    public function set_plugin_configuration(): void {
        $this->plugin_configuration = $this->read_plugin_configuration("nfclogin");
    }

    public function get_plugin_configuration(): array {
        return $this->plugin_configuration;
    }

    public function onDisable(): void {
        $this->log_append = $this->get_log_append();
    }

    public function onEnable(): void {}

    public function onLoad(): void {}

    public function readCard(): ?array {
        $scriptPath = __DIR__ . '/read_nfc_uid.py';
        exec("python3 " . escapeshellarg($scriptPath) . " 2>&1", $output, $status);

        if ($status !== 0) {
            return [
                "error" => "Helper script execution failed",
                "output" => $output
            ];
        }

        $json = implode("", $output);
        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                "error" => "Invalid JSON response from helper script",
                "raw_output" => $output
            ];
        }

        return $data;
    }

    public function assignCard($username){
        $scriptPath = __DIR__ . "/write_nfc.py";
        exec("python3 " . escapeshellarg($scriptPath) . " " . escapeshellarg($username) . " 2>&1", $output, $status);
        if ($status !== 0) {
            return [
                "error" => "Helper script execution failed",
                "output" => $output
            ];
        }
        $json = implode("", $output);
        $data = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                "error" => "Invalid JSON response from helper script",
                "raw_output" => $output
            ];
        }
        return $data;
    }

    public function readBlock4() {
        $scriptPath = __DIR__ . "/read_nfc_block.py";
        exec("python3 " . escapeshellarg($scriptPath) . " 2>&1", $output, $status);
        if ($status !== 0) {
            return [
                "error" => "Helper script execution failed",
                "output" => $output
            ];
        }
        $json = implode("", $output);
        $data = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                "error" => "Invalid JSON response from helper script",
                "raw_output" => $output
            ];
        }
        return $data;
    }
}
