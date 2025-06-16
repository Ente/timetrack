<?php
namespace Toil {
    require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
    use Toil\EP;
    use Toil\Tokens;
    use Arbeitszeit\Arbeitszeit;

    class deleteToken implements EPInterface
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

            $authHeader = $_SERVER["HTTP_AUTHORIZATION"] ?? null;

            if ($authHeader && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
                $token = $matches[1];
                $tokens = new Tokens();

                if ($tokens->validate_token($token)) {
                    $t = $tokens->get_token_from_token($token);
                    $userId = $t["user_id"];

                    $result = $tokens->delete_token($userId, $token);
                    echo json_encode($result);
                    return;
                }
            }

            echo json_encode(["error" => true, "reason" => "Invalid or missing token"]);
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