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
            $langlist = [
                "de",
                "en",
                "nl"
            ];
            $lang_values = array_values($langlist);
            $locale = substr($locale, 0, 2);
            if (in_array($locale, $lang_values)) {
                if ($area == "admin") {
                    if (file_get_contents(dirname(__FILE__) . "/admin/{$page}" . "/snippets_" . strtoupper($locale) . ".json")) {
                        Exceptions::error_rep("Suitable language files for page '" . $page . "' and area '" . $area ."' and locale " . $locale . " found", 1, "N/A");
                        return json_decode(file_get_contents(dirname(__FILE__) . "/admin/{$page}" . "/snippets_" . strtoupper($locale) . ".json"), true);
                    } else {
                        Exceptions::error_rep("Could not retrieve language files for page '" . $page . "' and area '" . $area ."' and locale " . $locale . " | Using fallback language 'EN'", 1, "N/A");
                        return json_decode(file_get_contents(dirname(__FILE__) . "/admin/{$page}" . "/snippets_EN.json"), true);
                    }
                } elseif ($area == "suite") {
                    if ($e = file_get_contents(dirname(__FILE__) . "/suite/{$page}" . "/snippets_" . strtoupper($locale) . ".json")) {
                        Exceptions::error_rep("Suitable language files for page '" . $page . "' and area '" . $area ."' and locale " . $locale . " found", 1, "N/A");
                        return json_decode($e, true);
                    } else {
                        Exceptions::error_rep("Could not retrieve language files for page '" . $page . "' and area '" . $area ."' and locale " . $locale . " | Using fallback language 'EN'", 1, "N/A");
                        return json_decode(file_get_contents(dirname(__FILE__ . "/suite/{$page}" . "/snippets_EN.json"), true));
                    }
                }
            }

            Exceptions::failure(code: 1, error: "Could not retrieve language files for page '" . $page . "' and area '" . $area ."' and locale " . $locale, stack: "N/A");
        }
    }
}