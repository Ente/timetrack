<?php
namespace Arbeitszeit\Telemetry\Server;

class Server {

    public function __construct() {
        $this->listenForTelemetry();
    }

    public function listenForTelemetry() {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo "Method not allowed. You have to use POST";
            return;
        }

        $data = json_decode(file_get_contents("php://input"), true);

        if (!$data) {
            http_response_code(400);
            echo "Invalid JSON";
            return;
        }

        $this->processTelemetry($data);

        http_response_code(200);
        echo "OK";
    }

    public function processTelemetry($data) {
        if ($this->isExistingInstance($data["instance_uuid"])) {
            $this->updateTelemetry($data);
        } else {
            $this->insertTelemetry($data);
        }
    }

    public function isExistingInstance($uuid): bool {
        $db = new \Arbeitszeit\DB();
        $sql = "SELECT COUNT(*) AS count FROM telemetry_server WHERE instance_uuid = :uuid";
        $stmt = $db->sendQuery($sql);
        $stmt->execute(["uuid" => $uuid]);
        $res = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $res["count"] > 0;
    }

    public function insertTelemetry($data): void {
        $db = new \Arbeitszeit\DB();
        $sql = "INSERT INTO telemetry_server
            (instance_uuid, time_track_version, api_version, php_version,
             os_version_and_name, total_plugins, total_users, total_worktimes, api_calls_total)
            VALUES
            (:instance_uuid, :time_track_version, :api_version, :php_version,
             :os_version_and_name, :total_plugins, :total_users, :total_worktimes, :api_calls_total)";
        $db->sendQuery($sql)->execute($data);
    }

    public function updateTelemetry($data): void {
        $db = new \Arbeitszeit\DB();
        $sql = "UPDATE telemetry_server SET
                time_track_version = :time_track_version,
                api_version = :api_version,
                php_version = :php_version,
                os_version_and_name = :os_version_and_name,
                total_plugins = :total_plugins,
                total_users = :total_users,
                total_worktimes = :total_worktimes,
                api_calls_total = :api_calls_total,
                updated_at = CURRENT_TIMESTAMP
                WHERE instance_uuid = :instance_uuid";

        $db->sendQuery($sql)->execute($data);
    }
}
