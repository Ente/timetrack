<?php
namespace Arbeitszeit {
    class Exceptions extends \Exception
    {
        public function __construct($message, $code = 0, \Exception $previous = null)
        {
            $this->error_rep($message);
            parent::__construct($message, $code, $previous);
        }
        public function __toString(): string
        {
            return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
        }

        public static function error_rep($message, $method = NULL)
        {
            $error_file = self::getSpecificLogFilePath(); // file on your fs, e.g. /var/www/html/error.log
            $version = @file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/VERSION"); //optional value
            if ($method == NULL) {
                $method = $_SERVER["REQUEST_METHOD"];
            }
            $time = date("[d.m.Y | H:i:s]");
            error_log("{$time} \"{$message}\"\nURL: {$_SERVER["HTTP_HOST"]}{$_SERVER["REQUEST_URI"]} \nVersion: {$version} - Server Name: {$_SERVER["SERVER_NAME"]} - Request Method: '{$method}'\nRemote Address: {$_SERVER["REMOTE_ADDR"]} - Remote Port: {$_SERVER["REMOTE_PORT"]} - Script Name: '{$_SERVER["SCRIPT_FILENAME"]}'\n=======================\n", 3, $error_file);

        }

        public static function getSpecificLogFilePath($date = null)
        {
            if ($date == null) {
                $date = date("Y-m-d");
                return $_SERVER["DOCUMENT_ROOT"] . "/data/logs/log-{$date}.log";
            } else {
                preg_match("/^\d{4}-\d{2}-\d{2}$/m", $date, $match);
                if ($match[0] == null) {
                    self::getSpecificLogFilePath();
                }
                Exceptions::error_rep("Trying to get log file for date '$date'");
                return $_SERVER["DOCUMENT_ROOT"] . "/data/logs/log-{$match[0]}.log";
            }
        }

        public static function failure($code, $error, $stack)
        {
            Exceptions::error_rep("[EXCEPTIONS] A critical error occurred. | Message: " . $error);
            $parms = http_build_query(array("code" => $code, "error" => $error, "stack" => base64_encode($stack)));
            header("Location: /errors/500.php?$parms");
        }

        public static function deprecated($function_name, $additional_message)
        {
            $message = "The function '{$function_name}' is deprecated. {$additional_message}";

            trigger_error($message, E_USER_DEPRECATED);
            Exceptions::error_rep($message);
            return $message;
        }

        public static function getLastLines($filePath, $lines = 100)
        {
            if (!is_readable($filePath))
                return "Error retrieving log file!";

            $file = new \SplFileObject($filePath, 'r');
            $file->seek(PHP_INT_MAX);
            $lastLine = $file->key();

            $linesArr = [];
            for ($i = max(0, $lastLine - $lines); $i <= $lastLine; $i++) {
                $file->seek($i);
                $linesArr[] = $file->current();
            }

            return implode("", $linesArr);
        }


    }
}

?>