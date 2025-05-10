<?php
namespace Toil {
    require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
    use Toil\EP;
    use Toil\Tokens;

    class createToken implements EPInterface
    {
        public function __construct()
        {
            /**
             * Empty, required by interface
             *
             */
        }

        public function __set($name, $value)
        {
            $this->$name = $value;
        }

        public function __get($name)
        {
            return $this->$name;
        }

        public function get()
        {
            header('Content-Type: application/json');
            $username = $_SERVER["PHP_AUTH_USER"] ?? die(json_encode(["error" => true]));
            $expiration = $_GET["expiration"] ?? 7200;
            $isUpdate = $_GET["isUpdate"] ?? false;

            if ($username != null) {
                $token = new Tokens();
                $result = $token->createToken($username, $expiration, $isUpdate);
                echo json_encode($result);
            } else {
                echo json_encode(["error" => true]);
            }
        }

        public function post($post = null)
        {
            /**
             * Empty, required by interface
             *
             */
        }

        public function delete()
        {
            /**
             * Empty, required by interface
             *
             */
        }

        public function put()
        {
            /**
             * Empty, required by interface
             *
             */
        }
    }
}


?>