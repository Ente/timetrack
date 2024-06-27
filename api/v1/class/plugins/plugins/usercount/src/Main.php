<?php

declare(strict_types=1);

namespace userdetail;

use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\PluginBuilder;

class Main extends PluginBuilder {


    private string $log_append;

    private array $plugin_configuration;

    private self $data;

    public function set_log_append(): void{
        $v = $this->plugin_configuration["version"];
        $this->log_append = "[ExamplePlugin v{$v}]";
    }

    public function get_log_append(): string{
        return $this->log_append;
    }

    public function set_plugin_configuration(): void{
        $this->plugin_configuration = $this->read_plugin_configuration("usercount");
    }

    public function get_plugin_configuration(): array{
        return $this->plugin_configuration;
    }

    public function __construct(){
        $this->set_plugin_configuration();
        $this->set_log_append();
    }

    public function onLoad(): void{
        $lga = $this->get_log_append();
        $this->logger("{$lga} Loading userdetail plugin...");
    }

    public function onEnable(): void{
        
    }

    public function onDisable(): void{
        
    }

    public function load_data(){
        $this->data = $this->unmemorize_plugin("userdetail");
    }

    public function save_data(){
        $this->memorize_plugin("userdetail");
    }

    public function saveDataDisk(array $payload = null){
        if($payload == null){
            $payload = $this->additional_payload;
        }
        $handle = fopen($_SERVER["DOCUMENT_ROOT"] . parent::get_basepath() . "/data/userdetail.json", "w+");
        if(fwrite($handle, json_encode($payload))){
            fclose($handle);
            return true;
        }
    }

    public function getDataDisk(){
        return json_decode(file_get_contents($_SERVER["DOCUMENT_ROOT"] . parent::get_basepath() . "/data/userdetail.json"), true);
    }

    public function get_users(){
        try {
            $sql = "SELECT COUNT(*) FROM `users`;";
            $result = mysqli_query(Arbeitszeit::get_conn(), $sql);

            $r = mysqli_fetch_array($result);

            return $r[0];
        } catch (\Exception $e){
            throw new \Exception((string)$e);
        }
    }
}


?>