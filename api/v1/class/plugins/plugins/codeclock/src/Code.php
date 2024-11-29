<?php
declare(strict_types=1);
namespace CodeClock;
use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Benutzer;
use Arbeitszeit\PluginBuilder;
use Arbeitszeit\PluginInterface;
class Code extends codeclock {

    public function readPINfile($username): string{
        $file = dirname(__DIR__) . "/data/pin/{$username}";
        if(file_exists($file)){
            return file_get_contents($file);
        } else {
            return "";
        }
    }

    public function validatePIN($username, $pin): bool{
        $file = dirname(__DIR__) . "/data/pin/{$username}";
        if(file_exists($file)){
            $file_pin = file_get_contents($file);
            try {
                return password_verify($pin, $file_pin);
            } catch (\Throwable $e){
                return false;
            }
        } else {
            return false;
        }
    }

    public function getUserPIN($username): string{
        $file = dirname(__DIR__, 1) . "/data/pin/{$username}_c";
        if(file_exists($file)){
            return file_get_contents($file);
        } else {
            return "";
        }
    }

    public function getUserbyPIN($pin): string{
        $files = glob(dirname(__DIR__, 1) . "/data/pin/*_c");
        foreach($files as $file){
            if(file_get_contents($file) == $pin){
                return basename($file, "_c");
            }
        }
        return "";
    }
}