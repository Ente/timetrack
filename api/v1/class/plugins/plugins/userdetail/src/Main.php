<?php

declare(strict_types=1);

namespace Userdetail;

use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Benutzer;
use Arbeitszeit\Exceptions;
use Arbeitszeit\PluginBuilder;
use Arbeitszeit\PluginInterface;

class Userdetail extends PluginBuilder implements PluginInterface {


    private string $log_append;

    private array $plugin_configuration;

    private array $default_payload = [
        "id" => null,
        "username" => null,
        "reset_pw" => null,
        "notes" => null,
        "position" => null,
        "employee-id" => null,
        "department" => null,
        "email" => null,
    ];

    public function set_log_append(): void{
        $v = $this->read_plugin_configuration("userdetail")["version"];
        $this->log_append = "[Userdetail v{$v}]";
    }

    public function get_log_append(): string{
        return $this->log_append;
    }

    public function set_plugin_configuration(): void{
        $this->plugin_configuration = $this->read_plugin_configuration("userdetail");
    }

    public function get_plugin_configuration(): array{
        return $this->plugin_configuration;
    }

    public function __construct(){
        $this->set_log_append();
        $this->set_plugin_configuration();
        $this->check_folder();
    }

    public function onLoad(): void{
        $lga = $this->get_log_append();
        $this->logger("{$lga} Loading userdetail plugin...");
    }

    public function onEnable(): void{
        
    }

    public function onDisable(): void{
        
    }

    public function get_employee(string $name): ?array{
        try {
            $benutzer = new Benutzer;
            $user = $benutzer->get_user($name);
            return $user;
        } catch (\Exception $e){
            throw new \Exception($e->getMessage());
        }
    }

    public function compute_user_nav(){
        $this->logger("[userdetail] Computing user navigation...");
        $benutzer = new Benutzer;
        $users = $benutzer->get_all_users();
        $html = "<ul>";
        foreach($users as $user){
            if(!$this->check_employee_file($user["username"])){ // if an file does not already exists, it creates one to prevent exceptions
                $this->save_employee_data([
                   "id" => $user["id"],
                   "username" => $user["username"],
                   "reset_pw" => null,
                   "notes" => null,
                   "position" => null,
                   "employee-id" => null,
                   "department" => null,
                   "email" => null,
                ]);
            }
            $name = $user["username"];
            $html .= "<li><a href='/suite/plugins/index.php?pn=userdetail&p_view=views/user.php&user={$name}'>{$name}</a></li>";
        }

        $html .= "</ul>";
        return $html;
    }

    public function save_employee_data($payload){
        $this->logger("[userdetail] Saving employee data...");
        $handle = fopen(dirname(__DIR__, 1) . "/data/" . $payload["username"] . ".json", "w+");
        $toJson = json_encode($payload);
        fwrite($handle, $toJson);
        fclose($handle);
        return true;
    }

    public function get_employee_data($username){
        $this->logger("[userdetail] Getting employee data for employee: {$username}...");
        $path = @file_get_contents(dirname(__DIR__, 1) . "/data/" . $username . ".json");
        if($path == null || $path == false){
            header("Location: /suite/plugins/index.php?pn=userdetail&p_view=views/user.php&user={$username}&nuser=true");
        }
        if(json_decode($path, true) != false){
            return json_decode(file_get_contents(dirname(__DIR__, 1) . "/data/" . $username . ".json"), true);
        } else {
            $this->logger("[userdetail] No data found for employee: {$username}...");
            return false;
        }
    }

    public function check_employee_file($username){
        $this->logger("[userdetail] Checking employee file for employee: {$username}...");
        if(file_exists(dirname(__DIR__, 1) . "/data/" . $username . ".json")){
            $this->logger("[userdetail] Employee file for employee: {$username} exists...");
            return true;
        } else {
            $this->logger("[userdetail] Employee file for employee: {$username} does not exist...");
            return false;
        }
    }

    public function check_folder(){
        if(!file_exists(dirname(__DIR__, 1) . "/data/")){
            mkdir(dirname(__DIR__, 1) . "/data/");
        }
    }
}


?>