<?php
declare(strict_types=1);
namespace CodeClock;
use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Benutzer;
use Arbeitszeit\PluginBuilder;
use Arbeitszeit\PluginInterface;
use Arbeitszeit\Exceptions;

class Code extends codeclock {

    public function readPINfile($username): string{
        Exceptions::error_rep("[CodeClock] Reading PIN file for {$username}");
        $file = dirname(__DIR__) . "/data/pin/{$username}";
        if(file_exists($file)){
            return file_get_contents($file);
        } else {
            Exceptions::error_rep("[CodeClock] PIN file not found for {$username}");
            return "";
        }
    }

    public function validatePIN($username, $pin): bool{
        Exceptions::error_rep("[CodeClock] Validating PIN for {$username}");
        $file = dirname(__DIR__) . "/data/pin/{$username}";
        if(file_exists($file)){
            $file_pin = file_get_contents($file);
            try {
                return password_verify($pin, $file_pin);
            } catch (\Throwable $e){
                Exceptions::error_rep("[CodeClock] Error validating PIN for {$username}");
                return false;
            }
        } else {
            Exceptions::error_rep("[CodeClock] PIN file not found for {$username}");
            return false;
        }
    }

    public function getUserPIN($username): string{
        Exceptions::error_rep("[CodeClock] Getting PIN for {$username}");
        $file = dirname(__DIR__, 1) . "/data/pin/{$username}_c";
        if(file_exists($file)){
            return file_get_contents($file);
        } else {
            Exceptions::error_rep("[CodeClock] PIN file not found for {$username}...Generating new PIN.");
            $pinString = (string)random_int(1000, 9999);
            file_put_contents($file, $pinString);
            chmod($file, 0600);
            Exceptions::error_rep("[CodeClock] New PIN generated for {$username}");
            return "";
        }
    }

    public function getUserbyPIN($pin): string{
        Exceptions::error_rep("[CodeClock] Getting user by PIN");
        $files = glob(dirname(__DIR__, 1) . "/data/pin/*_c");
        foreach($files as $file){
            if(file_get_contents($file) == $pin){
                return basename($file, "_c");
            }
        }
        Exceptions::error_rep("[CodeClock] PIN not found.");
        return "";
    }
}