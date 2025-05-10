<?php
namespace Toil {
    require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
    use Toil\EP;
    use Toil\Tokens;

    class refreshToken implements EPInterface
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
            /**
             * Empty, required by interface
             *
             */
        }

        public function post($post = null)
        {
            header('Content-Type: application/json');

            $authHeader = $_SERVER["HTTP_AUTHORIZATION"] ?? null;
            if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
                echo json_encode(["error" => true, "reason" => "Missing or invalid access token"]);
                return;
            }
            $accessToken = $matches[1];
            $t = file_get_contents("php://input");
            $data = json_decode($t, true);
            $refreshToken = $data["refreshToken"];

            if (!$refreshToken) {
                echo json_encode(["error" => true, "reason" => "Missing refresh token"]);
                return;
            }

            $tokens = new Tokens();

            if (!$tokens->validate_token($accessToken)) {
                echo json_encode(["error" => true, "reason" => "Invalid access token"]);
                return;
            }

            $tokenData = $tokens->get_token_from_token($accessToken);
            if (!$tokenData) {
                echo json_encode(["error" => true, "reason" => "Token not found"]);
                return;
            }

            if ($tokenData["refresh_token"] !== $refreshToken) {
                echo json_encode(["error" => true, "reason" => "Refresh token does not match"]);
                return;
            }
            $newTokens = $tokens->refresh_token($refreshToken);

            echo json_encode($newTokens);
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