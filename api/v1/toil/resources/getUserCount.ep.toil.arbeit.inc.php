<?php
namespace Toil {
    require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
    use Toil\EP;
    use Arbeitszeit\Arbeitszeit;
    use Arbeitszeit\DB;

    class getUserCount implements EPInterface
    {
        public function __construct()
        {

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
            $db = new DB;
            $sql = "SELECT COUNT(id) FROM `users`;";
            $res = $db->simpleQuery($sql)->execute();
            header('Content-Type: application/json');
            if ($res) {
                echo json_encode(array("users" => $res->fetch(\PDO::FETCH_ASSOC)["COUNT(id)"]));
            } else {
                echo json_encode(array(["error" => "Failed to retrieve user count."]));
            }
        }

        public function post($post = null)
        {

        }

        public function delete()
        {

        }

        public function put()
        {

        }
    }
}


?>