<?php
namespace Arbeitszeit{
    class MailVerification extends Auth{
        public function mail_verification($id){
            global $conn;
            $sql = "SELECT * FROM `users` WHERE id = '{$id}';";
            $res = mysqli_query($conn, $sql);
            $count = mysqli_num_rows($res);

            if($count == 1){
                $data = mysqli_fetch_assoc($res);
            } else {
                return [
                    "error" => [
                        "error_code" => 10,
                        "error_message" => "Error while fetching user data (results > 1 or 0)"
                    ]
                ];
            }
            $token = rand(111111, PHP_INT_MAX);
            $email_urlencoded = urlencode($data["email"]);
            $from = "test";
            $subject = "Bitte bestätige deine Email!";
            $text = <<< DATA

        Hallo,

        bitte bestätige deine Email-Adresse, indem du auf den Link unten drückst.

        Link: http://adresse.de/api/v1/validation/email_confirm.php?fake=true&email=$url_email&token=$token

        vielen Dank!

DATA;

        mail($data["email"], $subject, $text);
        }
    }
}


?>