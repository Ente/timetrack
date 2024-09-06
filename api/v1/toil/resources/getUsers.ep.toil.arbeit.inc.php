<?php
namespace Toil {
    require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
    use Toil\EP;
    use Arbeitszeit\Arbeitszeit;
    use Arbeitszeit\DB;

    class getUsers implements EPInterface
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
            $sql = "SELECT username,id FROM `users`;";
            $res = $db->sendQuery($sql);
            $res->execute();
            header('Content-Type: application/json');
            $arr = [];
            if ($res) {
                while ($row = $res->fetch(\PDO::FETCH_ASSOC)) {
                    $arr[$row["id"]] = $row["username"];
                }
                echo json_encode($arr);
                die();
            } else {
                echo json_encode(["error" => true]);
            }
        }

        public function post()
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