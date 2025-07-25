<?php
namespace Arbeitszeit {
    /**
     * How the class is being used:
     * 
     * - hook() is called in the arbeitszeit class to display the status message at the top of the page. It can also be called in any other file to display the status message.
     * - message($id) is called to get the status message with the given ID directly.
     * - URIBuilder($key, $path) is used to build the URI for the status message, which can be used in links or redirects, e.g. when logging out or performing actions that require a status message.
     */
    class StatusMessages extends Arbeitszeit
    {
        public $db;
        public array $messages = [];
        public array $base64Keys_messages = [];
        public string $custom_path = "";

        public function __construct($key = null, $path = null)
        {
            $this->db = new DB;

            // check if directly wanting to display message
            if ($key != null) {
                Exceptions::error_rep("StatusMessages constructor called with key: " . $key, 1, "N/A");
                $this->loadStatusMessages($path);
            }
        }

        /**
         * This function loads the status messages based of the existing i18n files.
         * This behavior can be changed by setting $file_path in the loadStatusMessages function. Otherwise the standard file will be used.
         * It is recommended to place custom files within the /data directory.
         * @param string|null $file_path The path to the status messages file relative to the document root.
         * @return void
         */
        public function loadStatusMessages($file_path = null)
        {
            if ($file_path == null) {
                Exceptions::error_rep("No file path provided, using default status messages file.", 1, "N/A");
                $this->messages = $this->i18n()->loadLanguage(null, "status");
            } else {
                try {
                    $json_data = file_get_contents($_SERVER["DOCUMENT_ROOT"] . $file_path);
                    $decoded_data = json_decode($json_data, true);
                    Exceptions::error_rep("Status messages loaded from file: " . $file_path, 1, "N/A");
                    if (is_array($decoded_data)) {
                        Exceptions::error_rep("Status messages decoded successfully.", 1, "N/A");
                        $this->messages = $decoded_data;
                    }

                    $this->custom_path = $file_path;
                } catch (\Exception $e) {
                    Exceptions::error_rep($e->getMessage(), 1, "N/A");
                    return;
                }
            }
        }

        /**
         * Converts the keys of the status messages to base64 so they can be reversed and then used to determine the key.
         *
         * @return void
         */
        public function base64KeysConverter()
        {
            Exceptions::error_rep("Converting status message keys to base64.", 1, "N/A");
            $this->base64Keys_messages = [];
            foreach (array_keys($this->messages) as $key) {
                $this->base64Keys_messages[base64_encode($key)] = $key;
            }
        }


        /**
         * Loads the status message from specified file with the given ID.
         * @param mixed $id
         * @return string|null
         */
        public function message($id)
        {
            if (isset($this->messages[$id])) {
                Exceptions::error_rep("Status message with ID '$id' found.", 1, "N/A");
                $type = $this->types[$id] ?? "info"; // z. B. "error", "warn", "info"
                return "<div class='status-message {$type}'>
                    <span class='dismiss-button' onclick='this.parentElement.classList.add(\"dismissed\")'>&times;</span>
                    " . $this->i18n()->sanitizeOutput($this->messages[$id]) . "
                    </div>";
            } else {
                Exceptions::error_rep("Status message with ID '$id' not found.", 1, "N/A");
                return null;
            }
        }

        public function convertBase64Key($key)
        {
            if (isset($this->base64Keys_messages[$key])) {
                Exceptions::error_rep("Converting base64 key: " . $key, 1, "N/A");
                return $this->base64Keys_messages[$key];
            } else {
                Exceptions::error_rep("Base64 key '$key' not found in messages.", 1, "N/A");
                return null;
            }
        }


        /**
         * Use this function to build the URI for the status message.
         * @param mixed $key The key of the status message.
         * @param mixed $path Optional path to the status message, if needed.
         * @return string Returns the URI string with the status message encoded in base64 ("status=....", without URI begin (?) or separator (&)).
         */
        public function URIBuilder($key, $path = null)
        {
            // This function is used to build the URI for the status message.
            $uri = "status=" . base64_encode(json_encode([
                "key" => base64_encode($key),
                "path" => $path
            ]));
            Exceptions::error_rep("URIBuilder called with key: " . $key . " and path: " . $path, 1, "N/A");
            return $uri;
        }

        public function hook()
        {
            // This function is used in the arbeitszeit class to hook it into the system directly, ensuring it will be displayed at the top and exactly as before v7.13.
            Exceptions::error_rep("StatusMessages hook called", 1, "N/A");
            if (isset($_GET["status"])) {
                Exceptions::error_rep("StatusMessages hook called with status parameter", 1, "N/A");
                $status = base64_decode($_GET["status"]);
                $status = json_decode($status, true);
                Exceptions::error_rep("Decoded status: " . print_r($status, true), 1, "N/A");

                if (is_array($status) && isset($status["key"])) {
                    $this->loadStatusMessages();
                    Exceptions::error_rep("StatusMessages loaded successfully", 1, "N/A");
                    Exceptions::error_rep("messages: " . print_r($this->messages, true), 1, "N/A");
                    $this->base64KeysConverter();
                    Exceptions::error_rep("Base64 keys converted successfully", 1, "N/A");
                    Exceptions::error_rep("Base64 keys: " . print_r($this->base64Keys_messages, true), 1, "N/A");
                    Exceptions::error_rep("Converting base64 key: " . $status["key"], 1, "N/A");

                    $key = $this->convertBase64Key($status["key"]);
                    Exceptions::error_rep("Converted key: " . $key . "| base64 original: " . $status["key"], 1, "N/A");
                    return $this->message($key);
                } else {
                    Exceptions::error_rep("StatusMessages hook called without valid status parameter", 1, "N/A");
                    Exceptions::error_rep("Status parameter: " . print_r($status, true), 1, "N/A");
                    return "";
                }
            } else {
                Exceptions::error_rep("StatusMessages hook called without status parameter", 1, "N/A");
                return "";
            }

        }
    }
}