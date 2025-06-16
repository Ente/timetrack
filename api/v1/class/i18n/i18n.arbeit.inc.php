<?php
namespace Arbeitszeit {
    class i18n 
    {

        /**
         * This function loads the specified language by their locale identifier
         * 
         * @param string $locale The locale identifier
         * @param string $page Page to load data for
         * @param string $area The area the user is trying to access ("admin", "employee")
         * @return array Returns array with data
         * 
         * English is always the fallback, if the requested language is not available
         * 
         * @author Torneo Project 2020
         */

        // Example Code snippet on how to use it:
        /*
       $locale = locale_accept_from_http($_SERVER["HTTP_ACCEPT_LANGUAGE"]); // Locale retrieved from Header

       $language = loadLanguage($locale, "panel"); // Loads the language for the "panel" Page

        */
        public function loadLanguage($locale = null, $page = "index", $area = "suite"){
            if($locale == null){
                $locale = @basename(locale_accept_from_http($_SERVER["HTTP_ACCEPT_LANGUAGE"]));
                if($locale == null){
                    $locale = "en_EN";
                }
            }

            $langlist = ["de", "en", "nl"];
            $locale = substr($locale, 0, 2);

            if (in_array($locale, $langlist)) {
                $file_path = dirname(__FILE__) . "/$area/{$page}/snippets_" . strtoupper($locale) . ".json";
                if (file_exists($file_path)) {
                    $json_data = file_get_contents($file_path);
                    $decoded_data = json_decode($json_data, true);
                    
                    if (!is_array($decoded_data)) {
                        Exceptions::error_rep("Invalid JSON format in '$file_path'", 1, "N/A");
                        return [];
                    }

                    Exceptions::error_rep("Language files for '$page' and '$area' with locale '$locale' loaded successfully", 1, "N/A");
                    return $this->sanitizeOutput($decoded_data);
                } else {
                    Exceptions::error_rep("Could not retrieve language files for '$page' and '$area' and locale '$locale' | Using fallback language 'EN'", 1, "N/A");
                    $fallback_path = dirname(__FILE__) . "/$area/{$page}/snippets_EN.json";
                    
                    if (file_exists($fallback_path)) {
                        return $this->sanitizeOutput(json_decode(file_get_contents($fallback_path), true));
                    } else {
                        return [];
                    }
                }
            }

            Exceptions::failure(1, "Could not retrieve language files for '$page' and '$area' and locale '$locale'", "N/A");
            return [];
        }

        public function sanitizeOutput($data) {
            if (is_array($data)) {
                return array_map([$this, 'sanitizeOutput'], $data);
            }
            // ruleset of allowed HTML tags
            $replace = [
                '[RED]' => '<span class="text-red" style="color: red;">',
                '[/RED]' => '</span>',
                '[GREEN]' => '<span class="text-green" style="color: green;">',
                '[/GREEN]' => '</span>',
                '[YELLOW]' => '<span class="text-yellow" style="color: yellow;">',
                '[/YELLOW]' => '</span>',
                '[BLUE]' => '<span class="text-blue" style="color: blue;">',
                '[/BLUE]' => '</span>',
                '[BR]' => '<br>',
                '[SPAN]' => '<span>',
                '[/SPAN]' => '</span>'
            ];
        
            $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        
            return str_replace(array_keys($replace), array_values($replace), $data);
        }
        
    }
}