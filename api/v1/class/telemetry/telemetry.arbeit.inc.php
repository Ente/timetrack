<?php
namespace Arbeitszeit {
    #require pluginbuilder
    require_once dirname(__DIR__, 2) . "/inc/arbeit.inc.php";
    require_once dirname(__DIR__, 1) . "/plugins/PluginBuilder.plugins.arbeit.inc.php";
    use Arbeitszeit\Arbeitszeit;
    use Arbeitszeit\PluginBuilder;

    class Telemetry extends Arbeitszeit
    {
        public array $i18n;
        public $db;
        public $pb;

        public function __construct()
        {
            $i18n = new i18n;
            $this->db = new DB;
            $this->pb = new PluginBuilder;
        }

        public function getAndSendTelemetryData(): void
        {

            # get uuid
            $uuid = $this->getInstanceUUID();
            # prepare data
            $data = [
                "instance_uuid" => $uuid,
                "time_track_version" => $this->getData("version", "VERSION"),
                "api_version" => $this->getData("api_version", "API_VERSION"),
                "php_version" => $this->getData("php_version", "PHP_VERSION"),
                "os_version_and_name" => $this->getData("os_version_and_name", "OS_VERSION_AND_NAME"),
                "total_plugins" => $this->getData("total_plugins", "PLUGINS"),
                "total_users" => $this->getData("total_users", "ALL_USERS"),
                "total_worktimes" => $this->getData("total_worktimes", "ALL_WORKTIMES"),
                "api_calls_total" => $this->getData("api_calls_total", "API_CALLS_TOTAL")
            ];
            $this->sendRequest($data);
        }

        public function getInstanceUUID(): string
        {
            $this->checkForInstanceUUID();

            $stmt = $this->db->sendQuery("SELECT instance_uuid FROM telemetry LIMIT 1");
            $stmt->execute();

            $data = $stmt->fetch(\PDO::FETCH_ASSOC);

            return $data["instance_uuid"];
        }

        public function isTelemetryEnabled(): bool
        {
            $ini = $this->get_app_ini();
            return isset($ini["general"]["telemetry"]) && $ini["general"]["telemetry"] === "enabled";
        }



public function sendRequest($data)
{
    $ini = $this->get_app_ini();
    $debugMode = isset($ini["telemetry"]["debug"]) && $ini["telemetry"]["debug"] === true;

    $url = $ini["general"]["telemetry_server_url"] ?? "https://telemetry.openducks.org/timetrack/submit";
    $payload = json_encode($data);

    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_HTTPHEADER => [
            "Content-Type: application/json",
            "Content-Length: " . strlen($payload)
        ],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_FAILONERROR => false,
        CURLOPT_VERBOSE => $debugMode,
    ]);

    $verbose = null;
    if ($debugMode) {
        $verbose = fopen('php://temp', 'w+');
        curl_setopt($ch, CURLOPT_STDERR, $verbose);
    }

    $response = curl_exec($ch);
    $error    = curl_error($ch);
    $info     = curl_getinfo($ch);

    $debugOutput = null;
    if ($debugMode) {
        rewind($verbose);
        $debugOutput = stream_get_contents($verbose);
        fclose($verbose);
    }

    curl_close($ch);

    if ($debugMode) {
        $log = "=== RESPONSE ===\n" . var_export($response, true) .
               "\n\n=== ERROR ===\n" . var_export($error, true) .
               "\n\n=== INFO ===\n" . var_export($info, true) .
               "\n\n=== VERBOSE ===\n" . $debugOutput .
               "\n\n=== PAYLOAD ===\n" . $payload .
               "\n--------------------------------------------\n";

        Exceptions::error_rep($log, "POST-API");
    }

    return $response;
}





public function getData($name, $method = "DEFAULT")
{
    if ($method === "DEFAULT") {
        $allowed = [
            "instance_uuid",
            "api_calls_total",
            "time_track_version",
            "api_version",
            "php_version",
            "os_version_and_name",
            "total_plugins",
            "total_users",
            "total_worktimes"
        ];

        if (!in_array($name, $allowed, true)) {
            return null;
        }

        $sql = "SELECT $name FROM telemetry LIMIT 1";

        $stmt = $this->db->sendQuery($sql);
        $stmt->execute();
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $data[$name] ?? null;
    }

    if ($method === "VERSION") {
        return $this->getTimeTrackVersion();
    } elseif ($method === "API_VERSION") {
        return $this->getToilVersion();
    } elseif ($method === "PHP_VERSION") {
        return phpversion();
    } elseif ($method === "PLUGINS") {
        return $this->pb->countPlugins();
    } elseif ($method === "ALL_USERS") {
        $stmt = $this->db->sendQuery("SELECT COUNT(id) as total FROM users");
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC)["total"];
    } elseif ($method === "ALL_WORKTIMES") {
        $stmt = $this->db->sendQuery("SELECT COUNT(id) as total FROM arbeitszeiten");
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC)["total"];
    } elseif ($method === "OS_VERSION_AND_NAME") {
        return php_uname();
    } elseif ($method === "API_CALLS_TOTAL") {
        $stmt = $this->db->sendQuery("SELECT api_calls_total FROM telemetry LIMIT 1");
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC)["api_calls_total"];
    }
}


        public function incrementAPICalls(): void
        {
            $sql = "UPDATE telemetry SET api_calls_total = api_calls_total + 1";
            $this->db->sendQuery($sql)->execute();
        }

        public function checkForInstanceUUID(): void
        {
            $stmt = $this->db->sendQuery("SELECT instance_uuid FROM telemetry");
            $stmt->execute();

            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            if (empty($rows)) {
                $uuid = $this->generateUUID();
                $insert = $this->db->sendQuery(
                    "INSERT INTO telemetry (instance_uuid, api_calls_total) VALUES (?, 0)"
                );
                $insert->execute([$uuid]);
                return;
            }

            if (count($rows) > 1) {
                $keepUuid = $rows[0]["instance_uuid"];

                $cleanup = $this->db->sendQuery(
                    "DELETE FROM telemetry WHERE instance_uuid != ?"
                );
                $cleanup->execute([$keepUuid]);
            }

        }



        private function generateUUID(): string
        {
            $data = random_bytes(16);
            $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
            $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);

            return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
        }
    }
}