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
        CustomRoutes::registerCustomRoute("nfcclogin", "/api/v1/class/plugins/plugins/nfclogin/src/routes/nfcclogin.ep.toil.arbeit.inc.php", 2);
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

    public function removeCard($id){
        $data = json_decode(file_get_contents(__DIR__ . "/data/map.json"), true);
        if ($data === null) {
            return null;
        }
        if (isset($data[$id])) {
            unset($data[$id]);
            file_put_contents(__DIR__ . "/data/map.json", json_encode($data, JSON_PRETTY_PRINT));
            return $data;
        } else {
            return null;
        }
    }

    public function memorize($id, $user){
        $data = json_decode(file_get_contents(__DIR__ . "/data/map.json"), true);
        if ($data === null) {
            $data = [];
        }
        $data[$id] = $user["username"];
        file_put_contents(__DIR__ . "/data/map.json", json_encode($data, JSON_PRETTY_PRINT));
        return $data;
    }

    public function getUser($id){
        $data = json_decode(file_get_contents(__DIR__ . "/data/map.json"), true);
        if ($data === null) {
            return null;
        }
        // return array("id" => $user);
        if (isset($data[$id])) {
            return $data[$id];
        } else {
            return null;
        }
    }

    public function assignCard(int $id){
        $scriptPath = __DIR__ . "/write_nfc.py";
        exec("python3 " . escapeshellarg($scriptPath) . " " . escapeshellarg((string)$id) . " 2>&1", $output, $status);
        if ($status !== 0) {
            return [
                "error" => "Helper script execution failed",
                "output" => $output
            ];
        }
        $this->memorize($this->readCard()["uid"], Benutzer::get_user_from_id($id));
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

    public function nfcloginHtml(){
        $html = "<a class='button' href='/api/v1/toil/nfcclogin'>Login with NFC</a>";
        return $html;
    }

    public function allCardAssignmentsHtml(){
        $data = json_decode(file_get_contents(__DIR__ . "/data/map.json"), true);
        if ($data === null) {
            return "No cards.";
        }
        $html = "<table style='margin: 0px auto;'>";
        $html .= "<tr><th>Card ID</th><th>Username</th></tr>";
        foreach ($data as $id => $user) {
            $html .= "<tr><td>{$id}</td><td>{$user}</td></tr>";
        }
        $html .= "</table>";
        return $html;
    }
}
