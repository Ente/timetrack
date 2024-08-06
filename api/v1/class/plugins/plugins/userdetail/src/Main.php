<?php

declare(strict_types=1);

namespace Userdetail;

use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Benutzer;
use Arbeitszeit\Exceptions;
use Arbeitszeit\PluginBuilder;
use Arbeitszeit\Hooks;
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
        "department" => null
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
        #Hooks::addHook("create_user", "callback", function(){echo "hi";}, "userdetail");
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
            throw new \Exception($e);
        }
    }

    public function compute_user_nav(){
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
                   "department" => null
                ]);
            }
            $name = $user["username"];
            $html .= "<li><a href='/suite/plugins/index.php?pn=userdetail&p_view=views/user.php&user={$name}'>{$name}</a></li>";
        }

        $html .= "</ul>";
        return $html;
    }

    public function add($name, $value, $user){

    }

    public function save_employee_data($payload){
        $handle = fopen(dirname(__DIR__, 1) . "/data/" . $payload["username"] . ".json", "w+");
        $toJson = json_encode($payload);
        fwrite($handle, $toJson);
        fclose($handle);
        return true;
    }

    public function get_employee_data($username){
        if(json_decode(@file_get_contents(dirname(__DIR__, 1) . "/data/" . $username . ".json"), true) != false){
            return json_decode(file_get_contents(dirname(__DIR__, 1) . "/data/" . $username . ".json"), true);
        } else {
            return false;
        }
    }

    public function check_employee_file($username){
        if(file_exists(dirname(__DIR__, 1) . "/data/" . $username . ".json")){
            return true;
        } else {
            return false;
        }
    }

    public function create_user_callback($username, $name, $email, $password, $isAdmin){
        echo "Successfully created user account...";
    }
}


?>