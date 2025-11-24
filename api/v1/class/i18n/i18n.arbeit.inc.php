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
            if ($locale == null) {
            $locale = @basename(locale_accept_from_http($_SERVER["HTTP_ACCEPT_LANGUAGE"]));
            if ($locale == null) {
                $locale = "en_EN";
            }
            }

            $lang = substr($locale, 0, 2);
            $lang_upper = strtoupper($lang);

            $default_path = dirname(__FILE__) . "/$area/{$page}/snippets_{$lang_upper}.json";
            if (file_exists($default_path)) {
            $json_data = file_get_contents($default_path);
            $decoded_data = json_decode($json_data, true);

            if (!is_array($decoded_data)) {
                Exceptions::error_rep("Invalid JSON format in '$default_path'", 1, "N/A");
                return [];
            }

            Exceptions::error_rep("Default language file for '$page' and '$area' with locale '$lang' loaded successfully", 1, "N/A");
            return $this->sanitizeOutput($decoded_data);
            }

            $docRoot = rtrim(isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : '', '/\\');
            $custom_path = ($docRoot !== '' ? $docRoot : '') . "/data/i18n/custom/{$page}/snippets_{$lang_upper}.json";

            if (file_exists($custom_path)) {
            $json_data = file_get_contents($custom_path);
            $decoded_data = json_decode($json_data, true);

            if (!is_array($decoded_data)) {
                Exceptions::error_rep("Invalid JSON format in custom file '$custom_path'", 1, "N/A");
                return [];
            }

            Exceptions::error_rep("Custom language file for '$page' with locale '$lang' loaded successfully", 1, "N/A");
            return $this->sanitizeOutput($decoded_data);
            }

            $fallback_path = dirname(__FILE__) . "/$area/{$page}/snippets_EN.json";
            if (file_exists($fallback_path)) {
            $json_data = file_get_contents($fallback_path);
            $decoded_data = json_decode($json_data, true);

            if (!is_array($decoded_data)) {
                Exceptions::error_rep("Invalid JSON format in fallback file '$fallback_path'", 1, "N/A");
                return [];
            }

            Exceptions::error_rep("Fallback English language file for '$page' and '$area' loaded", 1, "N/A");
            return $this->sanitizeOutput($decoded_data);
            }

            Exceptions::failure(1, "Could not retrieve language files for '$page' and '$area' and locale '$locale'", "N/A");
            return [];
        }

        public function loadCustomLanguageFile($file_path = "") {
            if (empty($file_path) || !file_exists($file_path)) {
                Exceptions::failure(1, "Invalid or non-existent file path provided for custom language file", "N/A");
                return [];
            }

            $json_data = file_get_contents($file_path);
            $decoded_data = json_decode($json_data, true);

            if (!is_array($decoded_data)) {
                Exceptions::error_rep("Invalid JSON format in '$file_path'", 1, "N/A");
                return [];
            }

            Exceptions::error_rep("Custom language file '$file_path' loaded successfully", 1, "N/A");
            return $this->sanitizeOutput($decoded_data);
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