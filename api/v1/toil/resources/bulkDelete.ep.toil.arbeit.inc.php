<?php
namespace Toil {
    require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
    use Toil\EP;
    use Arbeitszeit\Arbeitszeit;
    use Arbeitszeit\Exceptions;
    use Arbeitszeit\Auth;

    class bulkDelete implements EPInterface
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
            header(header: 'Content-Type: application/json');
            echo json_encode(array("error" => "Bulk delete endpoint only supports POST method."));
            die();

        }

        public function post($post = null)
        {
            # supported types: worktimes, vacations, sicknesses
            header(header: 'Content-Type: application/json');
            $arbeit = new Arbeitszeit();

            # get data from POST['data']
            $data = json_decode($post['data'], true);
            if(!isset($data["type"])){
                echo json_encode(array("error" => "No type specified."));
                die();
            }

            if(!in_array($data["type"], array("worktimes", "vacations", "sicknesses"))){
                echo json_encode(array("error" => "Invalid type specified."));
                die();
            }

            $type = $data["type"];
            $ids = $data["ids"] ?? array();
            # admin check
            if(!$arbeit->benutzer()->current_user_is_admin()){
                echo json_encode(array("error" => "You are not authorized to perform this action."));
                die();
            }

            $user = $arbeit->benutzer()->get_current_user();
            $username = $user["username"] ?? "unknown";
            Exceptions::error_rep("[BULK DELETE] User '{$username}' is performing bulk delete for type '{$type}' on IDs: " . implode(", ", $ids));

            if($type === "worktimes"){
                $deleted_count = 0;
                $not_found_count = 0;
                $not_found = array();
                $deleted_ids = array();
                foreach($ids as $id){
                    try {
                        if($arbeit->delete_worktime($id)){
                            $deleted_count++;
                            $deleted_ids[] = $id;
                            Exceptions::error_rep("[WORKTIME] Deleted worktime with ID {$id} via bulk delete.");
                        } else {
                            $not_found_count++;
                            $not_found[] = $id;
                        }
                    } catch (\Exception $e){
                        echo json_encode(array("error" => $e->getMessage()));
                        die();
                    }
                }

                echo json_encode(array(
                    "deleted_count" => $deleted_count,
                    "not_found_count" => $not_found_count,
                    "deleted_ids" => $deleted_ids,
                    "not_found_ids" => $not_found
                ));
            } elseif($type === "vacations"){
                $deleted_count = 0;
                $not_found_count = 0;
                $not_found = array();
                $deleted_ids = array();

                foreach($ids as $id){
                    try {
                        if($arbeit->vacation()->remove_vacation($id)){
                            $deleted_count++;
                            $deleted_ids[] = $id;
                            Exceptions::error_rep("[VACATION] Deleted vacation with ID {$id} via bulk delete.");
                        } else {
                            $not_found_count++;
                            $not_found[] = $id;
                        }
                    } catch (\Exception $e){
                        echo json_encode(array("error" => $e->getMessage()));
                        die();
                    }
                }

                echo json_encode(array(
                    "deleted_count" => $deleted_count,
                    "not_found_count" => $not_found_count,
                    "deleted_ids" => $deleted_ids,
                    "not_found_ids" => $not_found
                ));
            } elseif($type === "sicknesses"){
                $deleted_count = 0;
                $not_found_count = 0;
                $not_found = array();
                $deleted_ids = array();
                foreach($ids as $id){
                    try {
                        if($arbeit->sickness()->remove_sickness($id)){
                            $deleted_count++;
                            $deleted_ids[] = $id;
                            Exceptions::error_rep("[SICKNESS] Deleted sickness with ID {$id} via bulk delete.");
                        } else {
                            $not_found_count++;
                            $not_found[] = $id;
                        }
                    } catch (\Exception $e){
                        echo json_encode(array("error" => $e->getMessage()));
                        die();
                    }
                }
                echo json_encode(array(
                    "deleted_count" => $deleted_count,
                    "not_found_count" => $not_found_count,
                    "deleted_ids" => $deleted_ids,
                    "not_found_ids" => $not_found
                ));
            }
            die();
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