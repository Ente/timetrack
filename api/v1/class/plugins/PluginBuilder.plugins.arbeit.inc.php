<?php

declare(strict_types=1);
namespace Arbeitszeit{
    require_once $_SERVER["DOCUMENT_ROOT"] . "/vendor/autoload.php";
    use Symfony\Component\Yaml\Yaml;

    use Exception;


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
        final public function initialize_plugins(): ?bool{
            if($this->testing == true){
                $plugins = $this->get_plugins();
                if($plugins == false){
                    $this->logger("{$this->la} Could not get plugins. Please verify the plugin path given in the app.ini");
                    return false;
                }
                foreach($plugins["plugins"] as $plugin => $keys){
                    $this->load_class($keys["main"], $plugin . "/src");
                    $class = $keys["namespace"] . "\\" . $keys["main"];
                    if(!class_exists($class)){
                        $this->logger("{$this->la} Class '{$class}' not found!");
                        trigger_error("Fatal error: Class {$class} not found! Plugin not loading.");
                        die();
                    }
                    $this->logger("{$this->la} Plugin '{$class}' loading...");
                    $class = new $class;
                    $class->onLoad();

                }
                return true;
            } elseif($this->testing == false){
                
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
         * @return array|bool Returns an array. False on failure
         */
        final public function read_plugin_configuration($name): ?array{
           $la = $this->la;
           $path = $_SERVER["DOCUMENT_ROOT"] . "". $this->basepath . "/" . $name . "/plugin.yml";
           if(file_exists($_SERVER["DOCUMENT_ROOT"] . "" . $this->basepath . "/" . $name . "/plugin.yml") == true){
                try {
                    $yaml = Yaml::parseFile($this->platformSlashes($path));
                } catch(Exception $e){
                    Exceptions::error_rep($e);
                    throw new \Exception($e);
                }
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
            $plugins = $this->get_plugins();
            foreach($plugins["plugins"] as $plugin => $data){
                $dir = array_diff(scandir($_SERVER["DOCUMENT_ROOT"] . "" . $this->basepath . "/"), array(".", "..", "_data"));
                if(!in_array($plugin, $dir)){
                    if(!$this->memorize_plugin($data["main"])){
                        return false;
                    }
                } else {
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

        final public function get_plugin_nav($name){
            $conf = $this->read_plugin_configuration($name);
            if($conf["nav_links"] != false){
                return $conf["nav_links"];
            }
        }

        final public function get_plugin_nav_html($plugin_name){
            $links = $this->get_plugin_nav($plugin_name);
            $html = "";
            $conf = $this->read_plugin_configuration($plugin_name);
            if($conf["enabled"] == false){
                return null;
            }

            foreach($links as $n => $v){
                $html .= "<li><a href='/suite/plugins/index.php?pn={$plugin_name}&p_view={$v}'>[{$plugin_name}] $n</a></li>";
            }
            return $html;
        }

        final public function load_plugin_view($plugin_name, $view){
            # $view shall be the nav link value
            try{
                require $_SERVER["DOCUMENT_ROOT"] . $this->get_basepath() . "/" . $plugin_name . "/" . $view;
            } catch (\Error $e){
                Exceptions::error_rep("An error occured while loading view '$view' for plugin '$plugin_name' - Message: {$e}");
                return false;
            }
            return true;
        }

        public function getPluginClassPath($pluginName) {
            $config = $this->read_plugin_configuration($pluginName);
            $srcDir = $config['src'] ?? 'src';
            $mainClass = $config['main'] ?? '';
            if ($mainClass && $srcDir) {
                return $_SERVER["DOCUMENT_ROOT"] . $this->basepath . "/" . $pluginName . "/" . $srcDir . "/" . $mainClass . ".php";
            }
            return '';
        }
    
        /**
         * Lädt die Plugin-Klasse basierend auf der `plugin.yml`.
         *
         * @param string $pluginName Der Name des Plugins.
         * @return void
         */
        public function loadPluginClass($pluginName) {
            $classPath = $this->getPluginClassPath($pluginName);
            if (file_exists($classPath)) {
                require_once $classPath;
            } else {
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