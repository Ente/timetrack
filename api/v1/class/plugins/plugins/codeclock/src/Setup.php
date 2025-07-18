<?php
declare(strict_types=1);
namespace CodeClock;
use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Benutzer;
use Arbeitszeit\PluginBuilder;
use Arbeitszeit\PluginInterface;
use Arbeitszeit\Exceptions;
use Toil\Toil;
use Toil\CustomRoutes;
class Setup extends codeclock {
    public static function done(): bool{
        Exceptions::error_rep("[CodeClock] Setting up CodeClock plugin...");
        $arbeit = new Arbeitszeit();
        Exceptions::error_rep("[CodeClock] Registering custom routes...");
        CustomRoutes::registerCustomRoute("code", "/api/v1/class/plugins/plugins/codeclock/views/routes/Code.ep.toil.arbeit.inc.php", 0);
        if(!file_exists(dirname(__DIR__) . "/data/token")){
            Exceptions::error_rep(" No token file found, creating one now...");
            $masterToken = bin2hex(random_bytes(64));

            if(!file_put_contents(dirname(__DIR__) . "/data/token", $masterToken)){
                Exceptions::failure("Could not create token file", "ERROR-PLUGIN-SETUP-CREATE-TOKEN", "N/A");
                return false;
            }

            chmod(dirname(__DIR__) . "/data/token", 0600);

            // create PIN 4-numeric file for each user
            $users = $arbeit->benutzer()->get_all_users();
            $existingPins = [];
            foreach($users as $user){
                Exceptions::error_rep("Creating PIN file for user {$user["username"]}");
                $pinString = (string)random_int(1000, 9999);
                do {
                    $pinString = (string) random_int(1000, 9999);
                } while (in_array($pinString, $existingPins));
                $existingPins[] = $pinString;
                file_put_contents(dirname(__DIR__) . "/data/pin/{$user["username"]}_c", $pinString);
                $pin = password_hash($pinString, PASSWORD_DEFAULT);
                if(!file_put_contents(dirname(__DIR__) . "/data/pin/{$user["username"]}", $pin)){
                    Exceptions::failure("Could not create PIN file for user {$user["username"]}", "ERROR-PLUGIN-SETUP-CREATE-PIN", $user["id"]);
                    return false;
                }
                chmod(dirname(__DIR__) . "/data/pin/{$user["username"]}", 0600);
            }

            Exceptions::error_rep("Token file created successfully");

            // register route
            CustomRoutes::registerCustomRoute("code", "/api/v1/class/plugins/plugins/codeclock/views/routes/Code.ep.toil.arbeit.inc.php", 0);
            return true;
        } else {
            Exceptions::error_rep("Token file found");
            return true;
        }
    }
}