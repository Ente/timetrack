<?php
namespace Arbeitszeit {
    require_once $_SERVER["DOCUMENT_ROOT"] . "/vendor/autoload.php";
    use \Cryptolens_PHP_Client\Cryptolens;
    use \Cryptolens_PHP_Client\Key;

    class License extends Arbeitszeit
    {

        private $c;

        private $k;

        private $license_key;

        public function __construct()
        {
            $this->c = new Cryptolens("WyI3MTY0MzQ3NyIsIjZyUU4ydmkwMzZzQ1NOK2k4Y0NxNTJ0L1BBb3lWK0JFN0dMdzdIK0EiXQ==", 23504, Cryptolens::CRYPTOLENS_OUTPUT_PHP);
            Cryptolens::loader();
            $this->k = new Key($this->c);
            $this->license_key = Arbeitszeit::get_app_ini()["general"]["license"];
            $this->validate();
        }

        public function compute_license(){
            $usercount = $this->calculate_users();
            $license_key = $this->license_key;
            $lic = $this->get_license();
            Exceptions::error_rep("[LIC] Computing license information!");
            $array = [
                "user_count" => $usercount,
                "license_key" => $license_key,
                "license_data" => $lic
            ];

            return $array;
        }

        public function validate()
        {
            $base = Arbeitszeit::get_app_ini()["general"]["base_url"];

            # get license

            $license_key = Arbeitszeit::get_app_ini()["general"]["license"];
            if ($license_key == "GQQVR-FYTOM-XJDDV-IPGEJ") {
                # generate new key
                $new_key = $this->k->create_key(["F3" => true])["key"];
                $license_key = $new_key;
                if (!Arbeitszeit::change_settings(["license" => $new_key])) {
                    Exceptions::error_rep("[LIC] Failed to save settings for new license key with S package free. Please do this manually. License Key: '{$new_key}'");
                    return false;
                }
            }
            $license_data = $this->get_license();
            if (isset($license_data["error"]) || $license_data == false) {
                Exceptions::error_rep("[LIC] An error occured while validating your license. Please re-check your key!");
                return false;
            } elseif(!isset($license_data["license"])){
                return [
                    "error" => 15,
                    "error_message" => $license_data
                ];
            }
            

            ## get user count

            $arrContextOptions = array(
                "ssl" => array(
                    "verify_peer" => false,
                    "verify_peer_name" => false,
                ),
            );
            $count = (int) json_decode(file_get_contents("http://api:fi0V540orGRY8s7SUTzeZWN0alAi@{$base}/api/v1/toil/getUserCount"), true)["users"];
            # check countings on license model

            if ($count <= 25) {
                if ($license_data["F3"] != true) {
                    Exceptions::error_rep("[LIC] Exceeding user limit for current license! Please upgrade your license. | Count: {$count} - F3");
                    return false;
                } else {
                    return true;
                }
            } elseif ($count >= 25 && $count <= 50) {
                if ($license_data["F4"] != true) {
                    Exceptions::error_rep("[LIC] Exceeding user limit for current license! Please upgrade your license. | Count: {$count} - F4");
                    return false;
                } else {
                    return true;
                }
            } elseif ($count > 50) {
                if ($license_data["F5"] != true){
                    Exceptions::error_rep("[LIC] Exceeding user limit for current license! Please upgrade your license. Count: {$count} - F5");
                    return false;
                } else {
                    return true;
                }
            }

        }

        public function calculate_users()
        {
            $s = 10;
            $m = 25;
            $l = 50;

            $license_data = $this->get_license();
            $count = $this->get_userscount();

            if($license_data["F5"] == false && $license_data["F4"] == false && $license_data["F3"] == true && $count <= 10){
                return [
                    "slots" => $s,
                    "used" => $count,
                    "free" => ($s - $count)
                ];
            } elseif($license_data["F5"] == false && $license_data["F4"] == true && $count && $count <= 25){
                return [
                    "slots" => $m,
                    "used" => $count,
                    "free" => ($m - $count)
                ];
            } elseif($license_data["F5"] == true && $count <= 50){
                return [
                    "slots" => $l,
                    "used" => $count,
                    "free" => ($l - $count)
                ];
            } else {
                return [
                    "slots" => $s,
                    "used" => $count,
                    "free" => ($s - $count),
                ];
            }
        }

        public function calculate_users_html()
        {
            $s = 10;
            $m = 25;
            $l = 50;

            $license_data = $this->get_license();
            $count = $this->get_userscount();

            if($license_data["F5"] == false && $license_data["F4"] == false && $license_data["F3"] == true && $count <= 10){
                return [
                    "slots" => $s,
                    "used" => $count,
                    "free" => ($s - $count),
                    "percent" => ($count / $s * 100)
                ];
            } elseif($license_data["F5"] == false && $license_data["F4"] == true && $count && $count <= 25){
                return [
                    "slots" => $m,
                    "used" => $count,
                    "free" => ($m - $count),
                    "percent" => ($count / $m * 100)
                ];
            } elseif($license_data["F5"] == true && $count <= 50){
                return [
                    "slots" => $l,
                    "used" => $count,
                    "free" => ($l - $count),
                    "percent" => ($count / $l * 100)
                ];
            } else {
                return [
                    "slots" => $s,
                    "used" => $count,
                    "free" => ($s - $count),
                    "percent" => ($count / $s * 100)
                ];
            }
        }

        public function get_license(){
            $e = $this->k->get_key($this->license_key);
            if(!isset($e["licenseKey"])){
                return false;
            } else {
                return json_decode($e["licenseKey"], true);
            }
        }

        public function get_userscount(){

            $base = Arbeitszeit::get_app_ini()["general"]["base_url"];
            $sql = "SELECT COUNT(id) FROM `users`;";
            $res = mysqli_query(Arbeitszeit::get_conn(), $sql);
            if($res != false){
               $count = mysqli_fetch_assoc($res)["COUNT(id)"];
            } else {
                $count = -1;
                Exceptions::error_rep("Failed to fetch user count!");
            }
            #$count = json_decode(file_get_contents("http://api:fi0V540orGRY8s7SUTzeZWN0alAi@{$base}/api/v1/toil/getUserCount"), true);
            #$count = $count["users"];
            Exceptions::error_rep("Fetched current users, currently at '{$count}'");
            return (int) $count;
        }
    }
}


?>