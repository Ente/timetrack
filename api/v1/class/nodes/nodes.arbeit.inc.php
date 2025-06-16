<?php
namespace Arbeitszeit;

class Nodes extends Arbeitszeit
{

    private function isSystemMode(): bool
    {
        return \defined("SYSTEM_MODE") && \constant("SYSTEM_MODE") === true;
    }


    function checkNode(string $file, string $function, bool $panel = false): bool
    {
        $jsonPath = __DIR__ . "/json/nodes.json";

        if (!file_exists($jsonPath)) {
            Exceptions::error_rep("checkNode: JSON file not found at $jsonPath");
            return false;
        }

        $data = json_decode(file_get_contents($jsonPath), true);
        if (!$data) {
            Exceptions::error_rep("checkNode: Failed to decode JSON");
            return false;
        }

        if ($this->isSystemMode()) {
            $userIsAdmin = 1;
        } else {
            $username = $_SESSION["username"] ?? null;
            $user = $this->benutzer()->get_user($username);
            $userIsAdmin = $this->benutzer()->is_admin($user) ? 1 : 0;
        }

        $nodeSet = $panel
            ? $data["nodes"]["panels"][$file] ?? null
            : $data["nodes"]["functions"][$file] ?? null;

        if (!$nodeSet || !is_array($nodeSet)) {
            Exceptions::error_rep("checkNode: Invalid node set for $file, expected array, got " . gettype($nodeSet));
            return false;
        }

        $baseKey = $function;
        $permKey = $function . '_P';

        $allowed = $nodeSet[$baseKey] ?? false;

        if ($allowed === false) {
            Exceptions::error_rep("checkNode: Access denied for function $function in file $file");
            return false;
        }

        if (!array_key_exists($permKey, $nodeSet)) {
            Exceptions::error_rep("checkNode: Permission key $permKey not found in node set for $file");
            return $allowed === true;
        }

        $level = $nodeSet[$permKey];
        if (!in_array($level, [0, 1, 2], true)) {
            Exceptions::error_rep("checkNode: Invalid permission level for $function");
            return false;
        }

        if ($level === 2) {
            Exceptions::error_rep("checkNode: Access granted for function $function in file $file, user level: $userIsAdmin, required level: $level");
            return true;
        }

        if ($level === 1 && $userIsAdmin === 1) {
            Exceptions::error_rep("checkNode: Access granted for function $function in file $file, user level: $userIsAdmin, required level: $level");
            return true;
        }

        if ($level === 0 && ($userIsAdmin === 0 || $userIsAdmin === 1)) {
            Exceptions::error_rep("checkNode: Access granted for function $function in file $file, user level: $userIsAdmin, required level: $level");
            return true;
        }

        Exceptions::error_rep("checkNode: Access denied for function $function in file $file, user level: $userIsAdmin, required level: $level");
        return false;
    }
}
