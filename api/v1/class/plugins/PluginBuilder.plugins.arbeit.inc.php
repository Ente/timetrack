<?php

declare(strict_types=1);
namespace Arbeitszeit{
    require_once $_SERVER["DOCUMENT_ROOT"] . "/vendor/autoload.php";
    use Symfony\Component\Yaml\Yaml;

    use Exception;
    use Arbeitszeit\Exceptions;


    interface PluginInterface {
        public function onLoad(): void;
        public function onEnable(): void;
        public function onDisable(): void;
    }
    class PluginBuilder{

        /**
         * If set on true, testing mode is enabled, see more in the app.ini
         */
        private bool $testing;

        /**
         * Set the functionality either to true or false
         *
         * @var boolean
         */
        private bool $plugins = true;

        /**
         * Path of the plugin directory specified in the app.ini
         */
        private string $basepath = "/api/v1/class/plugins/plugins";

        /**
         * Variable saving name displayed in the log
         */
        public string $la = "[PluginBuilder]";

        /**
         * Configuration as an array, e.g. from app.ini
         */
        public array $config = [
            "plugins" => true,
            "path" => "/api/v1/plugins/plugins",
            "data" => "data",
            "testing" => true
        ];

        public array $additional_payload;

        /**
         * sets basepath and testing variable
         * 
         * @return void
         */
        public function __construct(){
            $this->set_basepath();
            $this->set_testing();
        }

        /**
         * set_basepath()
         * 
         * Sets the basepath
         * 
         * @return void
         */
        public function set_basepath(): void{
            $this->basepath = Arbeitszeit::get_app_ini()["plugins"]["path"];
        }

        /**
         * get_basepath()
         * 
         * Returns the basepath as a string
         * 
         * @return string $this->basepath
         */
        public function get_basepath(): string{
            return (string) $this->basepath;
        }

        /**
         * set_testing()
         * 
         * Sets the testing variable
         * 
         * @return void
         */
        public function set_testing(): void{
            $this->testing = (bool) Arbeitszeit::get_app_ini()["plugins"]["testing"];
        }

        /**
         * get_testing()
         * 
         * Returns the testing variable as a boolean
         * 
         * @return bool
         */
        public static function get_testing(): bool{
            return (bool) self::$testing;
        }

        /**
         * load_class
         * 
         * Loads a plugin class
         * 
         * @param string $class The class name
         * @param string $name Namespace of the class
         */
        final public function load_class($class, $name): void{
            try {
                require_once $_SERVER["DOCUMENT_ROOT"] . $this->basepath . "/" . $name . "/" . $class . ".php";
            } catch (Exception $e){
                if($e == strpos($e->getMessage(), "require_once()")){
                    throw new Exception("Class could not be loaded!");
                } else {
                    throw new Exception("Unknown error.");
                }
            }
        }

        /**
         * initialize_plugins Loads and enables all plugins
         * 
         * @return bool|void If everything went ok, void. If an error occurs false bool.
         */
        final public function initialize_plugins(): ?bool {
            if ($this->testing == true) {
                $plugins = $this->get_plugins();
                if ($plugins == false) {
                    $this->logger("{$this->la} Could not get plugins. Please verify the plugin path given in the app.ini");
                    return false;
                }
        
                if (is_array($plugins["plugins"])) {  // Hier wird überprüft, ob $plugins["plugins"] ein Array ist
                    foreach ($plugins["plugins"] as $plugin => $keys) {
                        $this->load_class($keys["main"], $plugin . "/src");
                        $class = $keys["namespace"] . "\\" . $keys["main"];
                        if (!class_exists($class)) {
                            $this->logger("{$this->la} Class '{$class}' not found!");
                            trigger_error("Fatal error: Class {$class} not found! Plugin not loading.");
                            die();
                        }
                        $this->logger("{$this->la} Plugin '{$class}' loading...");
                        $class = new $class;
                        $class->onLoad();
                    }
                }
        
                return true;
            } elseif ($this->testing == false) {
                // Keine Aktion notwendig
            } else {
                return false;
            }
        }
        

        function platformSlashes($path) {
            if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
                $path = str_replace('/', '\\', $path);
            }
            return $path;
        }

        /**
         * read_plugin_configuration() Reads the plugin yaml into an php array
         * 
         * 
         * @param string $name Class name of the plugin
         * @param bool $raw If set to true, the raw yaml is returned
         * @return array|bool|string Returns an array. False on failure
         */
        final public function read_plugin_configuration($name, $raw = false): array|string|bool{
           $la = $this->la;
           $path = $_SERVER["DOCUMENT_ROOT"] . "". $this->basepath . "/" . $name . "/plugin.yml";
           if(file_exists($_SERVER["DOCUMENT_ROOT"] . "" . $this->basepath . "/" . $name . "/plugin.yml") == true){
                try {
                    if($raw == true){
                        $this->logger("{$la} Reading raw plugin configuration for plugin '{$name}'...");
                        return file_get_contents($this->platformSlashes($path));
                    }
                    $this->logger("{$la} Reading plugin configuration for plugin '{$name}'...");
                    $yaml = Yaml::parseFile($this->platformSlashes($path));
                } catch(Exception $e){
                    Exceptions::error_rep($e);
                    throw new \Exception($e->getMessage());
                }
                $yaml["path"] = $path;
                return (array)$yaml;
           } else {
                Exceptions::error_rep("{$la} Could not read plugin configuration for plugin '{$name}' - Path: " . $_SERVER["DOCUMENT_ROOT"] . "" . $this->basepath . "/" . $name . "/plugin.yml");
                return null;
                
           }
        }

        /**
         * get_plugins() Reads all plugins into an array and returns it
         * 
         * @return array|void Returns and array on success. Nothing otherwise
         */
        final public function get_plugins(): array{
            $this->logger("{$this->la} Getting all plugins...");
            $dir = array_diff(scandir($_SERVER["DOCUMENT_ROOT"]. "" . $this->get_basepath()), array(".", "..", "data"));
            if($dir == false){
                $this->logger("{$this->la} Could not scan directory for plugins!");
                return false;
            } else {
                foreach($dir as $plugin){
                    $configuration = $this->read_plugin_configuration($plugin);
                    $data["plugins"][$plugin] = $configuration;

                }

                $data_json = json_encode($data);
                if($data_json != false){
                    $this->logger("{$this->la} Returning all plugins...");
                    return $data;
                }
            }

        }

        /**
         * memorize_plugins() Creates persistance for all plugins
         * 
         * This function creates a file in which the class is being stored, preventing loading the plugin completely new after each access.
         * A mp1 file is being created, containing the class data
         * 
         * 
         * @return void|Exception Void on success, Exception on failure
         */
        final public function memorize_plugins(): void{
            Exceptions::deprecated(__FUNCTION__, "This function is not supported anymore.");
            $this->logger("{$this->la} Memorizing all plugins...");
            $plugins = $this->get_plugins();
            foreach($plugins["plugins"] as $plugin => $data){
                try{
                    $this->load_class($data["main"], $plugin . "/src");
                    
                    $class = $data["namespace"] . "\\" . $data["main"];
                    $cl = new $class;
                    $cl = serialize($cl);
                    # move serialized class to the _data directory inside the plugins
                    $handle = fopen($_SERVER["DOCUMENT_ROOT"] . "/" . $this->basepath . "/" . "data/" . $data["main"] . ".tp1", "w+");
                    fwrite($handle, $cl, strlen($cl) + 5);
                    fclose($handle);
                    
                } catch(Exception $e){
                    Exceptions::error_rep($e);
                }
                
            }
        }

        /**
         * memorize_plugin() Creates persitance for a plugin
         * 
         * Same as the memorize_plugins()-function, but this function is only designed for one plugin at a time
         * 
         * @param string $name Class name of the plugin
         * @param array $additional_payload Additional data to save
         * @return bool|Exception Return true on success. Exception on failure
         */
        final public function memorize_plugin($name, $additional_payload = null): bool{
            Exceptions::deprecated(__FUNCTION__, "This function is not supported anymore.");
            $this->logger("{$this->la} Memorizing plugin '{$name}'...");
            $plugin = $this->read_plugin_configuration($name);
            try{
                $this->load_class($plugin["main"], $plugin["namespace"] . "/src");
                $class = $plugin["namespace"] . "\\" . $plugin["main"];
                $c1 = new $class;
                if($additional_payload != null){
                    $c1->additional_payload = $additional_payload;
                }
                $cl = serialize($c1);
                $handle = fopen($_SERVER["DOCUMENT_ROOT"] . "" . $this->basepath . "/" . "data/" . $plugin["main"] . ".tp1", "w+");
                fwrite($handle, $cl, strlen($cl) + 5);
                $this->logger("{$this->la} File '{$plugin["main"]}.tp1' created!");
                return true;
            } catch(Exception $e){
               Exceptions::error_rep($e);
               return false;
            }
        }

        /**
         * unmemorize_plugin() Brings a class into a readable php format
         * 
         * Since tp1 files are just serialized variables, it get's re-read back into a format php understands (in this case an object)
         * To prevent only loading back variables etc., the allowed_classes flag is set
         * 
         * @param string $name Class name of the plugin
         * @return object|bool|Exception Returns the class on success and either false or an Exception on failure
         */
        final public function unmemorize_plugin($name): object{
            Exceptions::deprecated(__FUNCTION__, "This function is not supported anymore.");
            $this->logger("{$this->la} Unmemorizing plugin '{$name}'...");
            $plugin = $this->read_plugin_configuration($name);
            try{
                $this->load_class($plugin["main"], $plugin["namespace"] . "/src");
                $class = $plugin["namespace"] . "\\" . $plugin["main"];
                $c1 = new $class;
                $class = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "" . $this->basepath . "/" . "data/" . $plugin["main"] . ".tp1");
                $cl = unserialize($class, array("allowed_classes" => true));

                if($cl instanceof $c1){
                    return $cl;
                } else {
                    $this->logger("{$this->la} Could not unmemorize plugin '{$name}'");
                    return false;
                }
            } catch(Exception $e){
                Exceptions::error_rep($e);
                return false;
            }
        }

        /**
         * check_persistance() Checks if a plugin is already persistant
         * 
         * Inside the data folder specified inside the app.ini plugins section it checks if the plugin is already persistant.
         * If not it creates the persistance, if it fails to do so it returns false
         * 
         * @return bool
         */
        final public function check_persistance(): bool{
            Exceptions::deprecated(__FUNCTION__, "This function is not supported anymore.");
            $this->logger("{$this->la} Checking persistance for all plugins...");
            $plugins = $this->get_plugins();
            foreach($plugins["plugins"] as $plugin => $data){
                $dir = array_diff(scandir($_SERVER["DOCUMENT_ROOT"] . "" . $this->basepath . "/"), array(".", "..", "_data"));
                if(!in_array($plugin, $dir)){
                    if(!$this->memorize_plugin($data["main"])){
                        $this->logger("{$this->la} Could not create persistance for plugin '{$plugin}'");
                        return false;
                    }
                } else {
                    $this->logger("{$this->la} Persistance for plugin '{$plugin}' already exists");
                    return false;
                }
            }

            return true;
        }

        /**
         * create_skeletton() Creates a sample plugin directory structure
         * 
         * @param string $name
         */
        final static public function create_skeletton($name){
            self::logger("[PluginBuilder] Creating plugin skeletton '{$name}'");
            $path = $_SERVER["DOCUMENT_ROOT"] . "/" . self::$basepath . "/" . $name;
            mkdir($path);
            $yml = fopen($path . "/plugin.yml", "w+");
            fwrite($yml, file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/api/v1/class/plugins/template/plugin.yml"));
            fclose($yml);

            mkdir($path . "/src");
            touch($path . "/src/" . $name . ".php");
            self::logger("[PluginBuilder] Created plugin skeletton '{$name}'");
            return true;
        }

        final public static function logger($message): void{
            Exceptions::error_rep($message);
        }

        final public static function check_plugins_enabled(){
            if(!isset(self::$plugins)){
                self::$plugins = Arbeitszeit::get_app_ini()["plugins"]["plugins"];
            }
            if(self::$plugins == true){
                return true;
            } else {
                return false;
            }
        }

        final public function get_plugin_nav($name) {
            $this->logger("{$this->la} Getting nav links for plugin '{$name}'");
            $conf = $this->read_plugin_configuration($name);
            if (isset($conf["nav_links"]) && is_array($conf["nav_links"])) {  
                return $conf["nav_links"];
            }
            $this->logger("{$this->la} Plugin '{$name}' has no nav links");
            return [];  
        }

        final public function get_plugin_nav_html($plugin_name) {
            $links = $this->get_plugin_nav($plugin_name);
            $html = "";
            $conf = $this->read_plugin_configuration($plugin_name);
            
            if (isset($conf["enabled"]) && !$conf["enabled"]) { 
                $this->logger("{$this->la} Plugin '{$plugin_name}' is disabled");
                return null;
            }
        
            if (is_array($links)) { 
                foreach ($links as $n => $v) {
                    $html .= "<li><a href='/suite/plugins/index.php?pn={$plugin_name}&p_view={$v}'>[{$plugin_name}] $n</a></li>";
                }
            }
            $this->logger("{$this->la} Plugin '{$plugin_name}' has no nav links");
            return $html;
        }
        

        final public function load_plugin_view($plugin_name, $view){
            # $view shall be the nav link value
            try{
                $this->logger("{$this->la} Loading view '{$view}' for plugin '{$plugin_name}'");
                require $_SERVER["DOCUMENT_ROOT"] . $this->get_basepath() . "/" . $plugin_name . "/" . $view;
            } catch (\Error $e){
                Exceptions::error_rep("An error occured while loading view '$view' for plugin '$plugin_name' - Message: {$e}");
                return false;
            }
            $this->logger("{$this->la} Loaded view '{$view}' for plugin '{$plugin_name}'");
            return true;
        }

        public function getPluginClassPath($pluginName) {
            $this->logger("{$this->la} Getting plugin class path for '{$pluginName}'...");
            $config = $this->read_plugin_configuration($pluginName);
            $srcDir = $config['src'] ?? 'src';
            $mainClass = $config['main'] ?? '';
            if ($mainClass && $srcDir) {
                return $_SERVER["DOCUMENT_ROOT"] . $this->basepath . "/" . $pluginName . "/" . $srcDir . "/" . $mainClass . ".php";
            }
            $this->logger("{$this->la} Main class not found in plugin configuration for '{$pluginName}'");
            return '';
        }
    
        /**
         * Lädt die Plugin-Klasse basierend auf der `plugin.yml`.
         *
         * @param string $pluginName Der Name des Plugins.
         * @return void
         */
        public function loadPluginClass($pluginName) {
            $this->logger("{$this->la} Loading plugin class for '{$pluginName}'...");
            $classPath = $this->getPluginClassPath($pluginName);
            if (file_exists($classPath)) {
                require_once $classPath;
            } else {
                $this->logger("{$this->la} Class file not found: {$classPath}");
                throw new Exception("Class file not found: " . $classPath);
            }
        }

        /* Plugin section */

        protected function onLoad(): void{

        }

        protected function onDisable(): void{

        }

        protected function onEnable(): void{
            
        }
    }
}



?>