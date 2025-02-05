<?php

namespace Arbeitszeit\Mails\Templates;

use Arbeitszeit\Mails\MailsTemplateInterface;
use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Exceptions;
class SicknessPendingTemplate implements MailsTemplateInterface {
    public function render(array $data): array {
            $arbeit = new Arbeitszeit();
            $i18n = $arbeit->i18n();
            $loc = $i18n->loadLanguage(null, "emails/sickness/pending");
            $base_url = Arbeitszeit::get_app_ini()["general"]["base_url"];
            $conn = $arbeit->db();
            $sql = "SELECT * FROM `users` WHERE username = ?;";
            $res = $conn->sendQuery($sql);
            $res->execute([$data["username"]]);
            $count = $res->rowCount();
            $ii = Arbeitszeit::get_app_ini()["general"]["app_name"];

            if ($count == 1) {
                $data1 = $res->fetch(\PDO::FETCH_ASSOC);
            } else {
                Exceptions::error_rep("An error occured while fetching user data from database for user '{$data["username"]}'. See previous message for more information.");
                return [
                    "error" => [
                        "error_code" => 10,
                        "error_message" => "Error while fetching user data (results > 1 or 0)"
                    ]
                ];
            }

            $sql1 = "SELECT * FROM `sick` WHERE id = ?;";
            $res1 = $conn->sendQuery($sql1);
            $res1->execute([$data["id"]]);
            if ($res1 != false) {
                $worktime_data = $res1->fetch(\PDO::FETCH_ASSOC);
            } else {
                Exceptions::error_rep("An error occured while fetching sickness data from database for id '{$data["id"]}'. See previous message for more information.");
                return [
                    "error" => [
                        "error_code" => 11,
                        "error_message" => "Error while fetching worktime data"
                    ]
                ];
            }

            $text = <<<DATA
                <style>
  table {
    width: 100%;
    border-collapse: collapse;
  }

  th, td {
    border: 1px solid #dddddd;
    text-align: left;
    padding: 8px;
  }

  th {
    background-color: #f2f2f2;
  }

  tr:nth-child(even) {
    background-color: #f2f2f2;
  }
</style>
                <p>{$loc["greetings"]} {$data1["name"]},</p>

                <p>{$loc["message"]}:</p>
                <p>({$loc["note"]})</p>

                <table>
                    <tr>
                        <th>{$loc["id"]}</th>
                        <th>{$loc["username"]}</th>
                        <th>{$loc["day"]}</th>
                        <th>{$loc["send"]}</th>
                        <th></th>
                    <tr>
                    <tr>
                        <td>{$worktime_data["id"]}</td>
                        <td>{$worktime_data["username"]}</td>
                        <td>{$worktime_data["start"]}</td>
                        <td>{$worktime_data["stop"]}</td>
                        <td>{$worktime_data["status"]}</td>
                    </tr>
                </table>
            <br>
            <p>{$loc["end"]}</p>
            <br>
            <b>({$loc["noreply"]})</b>

            <hr>

            <i>{$loc["gdpr"]}: https://{$base_url}/privacy_policy</i>

            <span style="color:red"><b>{$loc["confidential"]}</b></span>
DATA;
        return [
            "subject" => "{$loc["subject"]} | {$ii}",
            "body" => $text,
            "username" => $data["username"]
        ];
    }
}
