<?php
namespace Arbeitszeit;

use InvalidArgumentException;

class Hooks {
    private static array $hooks = [];
    private const HOOKS_FILE = __DIR__ . "/hooks.json";

    public static function initialize(): void {
        if (file_exists(self::HOOKS_FILE)) {
            $json = file_get_contents(self::HOOKS_FILE);
            $decoded = json_decode($json, true) ?? [];
            self::$hooks = self::convertCallbacksFromJson($decoded);
        } else {
            self::$hooks = [];
        }
    }

    private static function saveHooks(): void {
        $data = [];
        foreach (self::$hooks as $hookName => $timings) {
            foreach ($timings as $timing => $callbacks) {
                foreach ($callbacks as $callbackData) {
                    if (isset($callbackData['closure']) && $callbackData['closure']) {
                        $data[$hookName][$timing][] = ['closure' => true];
                    } else {
                        $data[$hookName][$timing][] = [
                            'closure' => false,
                            'class' => $callbackData['class'] ?? null,
                            'method' => $callbackData['method'] ?? null
                        ];
                    }
                }
            }
        }
        file_put_contents(self::HOOKS_FILE, json_encode($data, JSON_PRETTY_PRINT));
    }

    public static function addHook($event, $type, callable $callback, $pluginName = null) {
        if ($pluginName) {
            // Plugin-Klasse dynamisch laden
            $pluginBuilder = new PluginBuilder();
            $pluginBuilder->loadPluginClass($pluginName);
        }
        // Hook registrieren
        self::$hooks[$event][$type][] = $callback;
        self::saveHooks();
    }

    public static function executeHook(string $hookName, ...$args) {
        if (isset(self::$hooks[$hookName])) {
            foreach (self::$hooks[$hookName]['callback'] as $callbackData) {
                if (isset($callbackData['closure']) && $callbackData['closure']) {
                    $callback = $callbackData['callback'];
                    if (is_callable($callback)) {
                        call_user_func_array($callback, $args);
                    } else {
                        throw new InvalidArgumentException("Callback for hook '$hookName' is not callable.");
                    }
                } else {
                    $className = $callbackData['class'];
                    $methodName = $callbackData['method'];
                    if (class_exists($className) && method_exists($className, $methodName)) {
                        $instance = new $className();
                        if (is_callable([$instance, $methodName])) {
                            call_user_func_array([$instance, $methodName], $args);
                        } else {
                            throw new InvalidArgumentException("Method '$methodName' does not exist in class '$className'.");
                        }
                    } else {
                        throw new InvalidArgumentException("Class '$className' does not exist.");
                    }
                }
            }
        }
    }

    public static function executeWithHooks(string $hookName, callable $originalFunction, ...$args) {
        $result = null;

        if (isset(self::$hooks[$hookName])) {
            foreach (self::$hooks[$hookName]['around'] as $callbackData) {
                if (isset($callbackData['closure']) && $callbackData['closure']) {
                    $callback = $callbackData['callback'];
                    if (is_callable($callback)) {
                        $result = call_user_func_array($callback, [$originalFunction, ...$args]);
                        return $result;
                    } else {
                        throw new InvalidArgumentException("Callback for hook '$hookName' is not callable.");
                    }
                }
            }

            foreach (self::$hooks[$hookName]['before'] as $callbackData) {
                if (isset($callbackData['closure']) && $callbackData['closure']) {
                    $callback = $callbackData['callback'];
                    if (is_callable($callback)) {
                        call_user_func_array($callback, $args);
                    } else {
                        throw new InvalidArgumentException("Callback for hook '$hookName' is not callable.");
                    }
                }
            }

            $result = call_user_func_array($originalFunction, $args);

            foreach (self::$hooks[$hookName]['after'] as $callbackData) {
                if (isset($callbackData['closure']) && $callbackData['closure']) {
                    $callback = $callbackData['callback'];
                    if (is_callable($callback)) {
                        call_user_func_array($callback, $args);
                    } else {
                        throw new InvalidArgumentException("Callback for hook '$hookName' is not callable.");
                    }
                }
            }
        } else {
            $result = call_user_func_array($originalFunction, $args);
        }

        self::executeHook($hookName, ...$args);

        return $result;
    }

    public static function removeHook(string $hookName, string $timing, callable $callback = null): void {
        if (isset(self::$hooks[$hookName][$timing])) {
            if ($callback === null) {
                self::$hooks[$hookName][$timing] = [];
            } else {
                foreach (self::$hooks[$hookName][$timing] as $index => $callbackData) {
                    if (isset($callbackData['closure']) && $callbackData['closure']) {
                        if ($callbackData['callback'] === $callback) {
                            unset(self::$hooks[$hookName][$timing][$index]);
                        }
                    } else {
                        if ($callbackData['class'] === (is_object($callback[0]) ? get_class($callback[0]) : $callback[0]) &&
                            $callbackData['method'] === $callback[1]) {
                            unset(self::$hooks[$hookName][$timing][$index]);
                        }
                    }
                }

                self::$hooks[$hookName][$timing] = array_values(self::$hooks[$hookName][$timing]);

                if (empty(self::$hooks[$hookName]['before']) && empty(self::$hooks[$hookName]['after']) && empty(self::$hooks[$hookName]['around']) && empty(self::$hooks[$hookName]['callback'])) {
                    unset(self::$hooks[$hookName]);
                }
            }

            self::saveHooks();
        }
    }

    private static function convertCallbacksFromJson(array $data): array {
        $converted = [];

        foreach ($data as $hookName => $timings) {
            foreach ($timings as $timing => $callbacks) {
                foreach ($callbacks as $callbackData) {
                    if (isset($callbackData['closure']) && $callbackData['closure']) {
                        $converted[$hookName][$timing][] = ['closure' => true];
                    } else {
                        $className = $callbackData['class'] ?? null;
                        $methodName = $callbackData['method'] ?? null;

                        if ($className && $methodName) {
                            $converted[$hookName][$timing][] = [
                                'closure' => false,
                                'class' => $className,
                                'method' => $methodName
                            ];
                        } else {
                            $converted[$hookName][$timing][] = [
                                'closure' => false,
                                'class' => null,
                                'method' => null
                            ];
                        }
                    }
                }
            }
        }

        return $converted;
    }

    private static function createCallableFromClass(string $className, string $methodName): callable {
        if (!class_exists($className)) {
            throw new InvalidArgumentException("Class '$className' does not exist.");
        }

        $instance = new $className();
        if (!method_exists($instance, $methodName)) {
            throw new InvalidArgumentException("Method '$methodName' does not exist in class '$className'.");
        }

        return [$instance, $methodName];
    }
}
?>
