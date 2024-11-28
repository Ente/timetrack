<?php
declare(strict_types=1);
namespace qrclock;
use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Benutzer;
use Arbeitszeit\PluginBuilder;
use Arbeitszeit\PluginInterface;
use \QRCode as QRCode;
class qrclock extends PluginBuilder implements PluginInterface {
    private string $log_append;

    private array $plugin_configuration;

    private array $default_payload = [
        "id" => null,
        "username" => null,
        "token" => null,
        "status" => "clockin"
    ];

    private string $mastertoken;

    public function set_log_append(): void{
        $v = $this->read_plugin_configuration("qrclock")["version"];
        $this->log_append = "[qrclock v{$v}]";
    }

    public function get_log_append(): string{
        return $this->log_append;
    }

    public function set_plugin_configuration(): void{
        $this->plugin_configuration = $this->read_plugin_configuration("qrclock");
    }

    public function get_plugin_configuration(): array{
        return $this->plugin_configuration;
    }

    public function __construct(){
        require_once __DIR__ . "/phpqrcode/qrlib.php";
        if(!class_exists("QrCode")){
            throw new \Exception("QRCode class not found", 1);
        }
        $this->set_log_append();
        $this->set_plugin_configuration();
        if(!file_exists(dirname(__DIR__) . "/data/token")){
            $mastertoken = bin2hex(random_bytes(64));
            file_put_contents(dirname(__DIR__) . "/data/token", $mastertoken);
            // restrict rights to file
            chmod(dirname(__DIR__) . "/data/token", 0600);
        } else {
            $this->mastertoken = file_get_contents(dirname(__DIR__) . "/data/token");
        }
    }

    public function generateDynamicToken(string $masterToken, string $userid): string{
        $time = time();
        $data = $userid . "|" . $time;
        $dynamicToken = hash_hmac("sha256", $data, $masterToken);

        return base64_encode($dynamicToken . "|" . $time);
    }

    //TODO: Rewrite function to use qrcode.lib
    public function generateQRCodeContents(string $userid): string {
        $arbeit = new Arbeitszeit();
        $dynamicToken = $this->generateDynamicToken($this->mastertoken, $userid);
        $rand = bin2hex(random_bytes(8));        
        $active = Arbeitszeit::check_easymode_worktime_finished($_SESSION["username"]);
        $worktime = Arbeitszeit::get_worktime_by_id($active);
        if($active === false){
            throw new \Exception("An error occured while checking.");
        } elseif(!$worktime && $active === true){
            throw new \Exception("An error occured while checking.");
        } elseif($active === -1){
            $qrcode = @QRcode::png("http://" . Arbeitszeit::get_app_ini()["general"]["base_url"] . "/suite/plugins/index.php?pn=qrclock&p_view=views/index.php&action=clockin&payload=" . base64_encode(json_encode(["id" => $userid, "token" => $dynamicToken, "action" => "clockin", "username" => $_SESSION["username"]])), dirname(__DIR__, 1) . "/data/qrcode-{$rand}.png");
        } else {
            $qrcode = @QRcode::png("http://" . Arbeitszeit::get_app_ini()["general"]["base_url"] . "/suite/plugins/index.php?pn=qrclock&p_view=views/index.php&action=clockout&payload=" . base64_encode(json_encode(["id" => $userid, "token" => $dynamicToken, "action" => "clockout", "username" => $_SESSION["username"]])), dirname(__DIR__, 1) . "/data/qrcode-{$rand}.png");
        }
        return "/api/v1/class/plugins/plugins/qrclock/data/qrcode-{$rand}.png";
    }

    public function validateDynamicToken(string $token, string $masterToken, string $userId): bool{
        $decoded = base64_decode($token);
        $masterToken = $this->mastertoken;
        [$hash, $timestamp] = explode("|", $decoded);

        if(time() - $timestamp > 300){
            return false;
        }

        $expectedHash = hash_hmac("sha256", $userId . "|" . $timestamp, $masterToken);
        return hash_equals($expectedHash, $hash);
    }

    public function getStatus(): string{
        try{
            $arbeit = new Arbeitszeit();
            $status = $arbeit->check_easymode_worktime_finished($_SESSION["username"]);
            if($status === -1){
                return "clockin";
            } else {
                return "clockout";
            }
        } catch (\TypeError $e){
            return "clockin";
        }
    }

    public function onLoad(): void{
        $this->set_log_append();
        $this->set_plugin_configuration();
    }

    public function onDisable(): void{
        $this->set_log_append();
        $this->set_plugin_configuration();
    }

    public function onEnable(): void{
        $this->set_log_append();
        $this->set_plugin_configuration();
    }
}