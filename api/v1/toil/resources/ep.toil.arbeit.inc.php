<?php

namespace Toil;

interface EPInterface {
    public function get();
    public function post();
    public function put();
    public function delete();
    public function __construct();
    public function __set($name, $value);
    public function __get($name);
}
class EP extends Toil {

}