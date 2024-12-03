<?php
namespace Toil;
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
use Toil\EP;
use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Auth;
use Arbeitszeit\Benutzer;
use Arbeitszeit\Projects;
class PluginManager implements EPInterface {
    public function get(){
        require_once dirname(__DIR__, 2) . "/views/index.php";
    }

    public function post($post = null){
        require_once dirname(__DIR__, 2) . "/views/index.php";
    }

    public function put(){
        return true;
    }

    public function delete(){
        return true;
    }

    public function __construct(){
        return true;
    }

    public function __set($name, $value){
        $this->$name = $value;
    }

    public function __get($name){
        return $this->$name;
    }

}