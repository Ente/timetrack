<?php
declare(strict_types=1);

namespace utility;

use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Benutzer;
use Arbeitszeit\DB;
use Arbeitszeit\Exceptions;
use Arbeitszeit\PluginBuilder;
use Arbeitszeit\PluginInterface;
use Arbeitszeit\StatusMessages;

class utility extends PluginBuilder implements PluginInterface
{

    private string $log_append;

    private array $plugin_configuration;

    private array $userData;

    private string $data;

    private string $filename = "export.json";

    private DB $db;

    public function set_log_append(): void
    {
        $v = $this->read_plugin_configuration("utility")["version"];
        $this->log_append = "[Utility] v{$v}";
    }

    public function get_log_append(): string
    {
        return $this->log_append;
    }

    public function set_plugin_configuration(): void
    {
        $this->plugin_configuration = $this->read_plugin_configuration("userdetail");
    }

    public function get_plugin_configuration(): array
    {
        return $this->plugin_configuration;
    }

    public function __construct()
    {
        $this->set_log_append();
        $this->set_plugin_configuration();
        $this->db = new DB();
    }

    public function onLoad(): void
    {
        $lga = $this->get_log_append();
        $this->logger($lga . " Loading utility plugin...");
    }

    public function onEnable(): void
    {

    }

    public function onDisable(): void
    {

    }

    public function set_userData($data): void {
        $this->userData = $data;
    }

    public function checkIfuserDataisSet(){
        try{
            if(isset($this->userData)){
                return true;
            } else {
                return false;
            }
        } catch(\Exception $e){
            StatusMessages::redirect("error");
        }
    }

    public function exportAll($username): self{
        $userData = Benutzer::get_user($username);
        $this->userData = $userData;
        $arr = [];
        $arr["worktimes"] = $this->exportUserWorktimes();
        $arr["sickness"] = $this->exportUserSickness();
        $arr["vacations"] = $this->exportUserVacations();

        $this->data = json_encode($arr, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $this->filename = "export_user_" . $userData["username"] . "_" . date("Y-m-d_H-i-s") . ".json";

        return $this;
    }

    public function download(): void{
        if(!isset($this->data)){
            StatusMessages::redirect("error");
        }

        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $this->filename . '"');
        header('Content-Length: ' . strlen($this->data));

        echo $this->data;
        exit;
    }

    public function getString(): string{
        return $this->data;
    }

    public function exportUserWorktimes(): array|bool{
        $this->checkIfuserDataisSet();
        $sql = "SELECT * FROM `arbeitszeiten` WHERE name = ?";
        $res = $this->db->sendQuery($sql);
        $res->execute([$this->userData["username"]]);

        $worktimes = [];
        if($res->rowCount() >= 1){
            while($data = $res->fetch(\PDO::FETCH_ASSOC)){
                array_push($worktimes, $data);
            }
            return $worktimes;
        } else {
            return false;
        }
    }

    public function exportUserSickness(): array|bool{
        $this->checkIfuserDataisSet();
        $sql = "SELECT * FROM `sick` WHERE username = ?";
        $res = $this->db->sendQuery($sql);
        $res->execute([$this->userData["username"]]);

        $sickness = [];

        if($res->rowCount() >= 1){
            while($data = $res->fetch(\PDO::FETCH_ASSOC)){
                array_push($sickness, $data);
            }
            return $sickness;
        } else {
            return false;
        }
    }

        public function exportUserVacations(): array|bool{
        $this->checkIfuserDataisSet();
        $sql = "SELECT * FROM `vacation` WHERE username = ?";
        $res = $this->db->sendQuery($sql);
        $res->execute([$this->userData["username"]]);

        $vacation = [];

        if($res->rowCount() >= 1){
            while($data = $res->fetch(\PDO::FETCH_ASSOC)){
                array_push($vacation, $data);
            }
            return $vacation;
        } else {
            return false;
        }
    }
}