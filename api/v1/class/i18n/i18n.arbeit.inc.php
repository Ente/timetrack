<?php


/**
 * This function loads the specified language by their locale identifier
 * 
 * @param string $locale The locale identifier
 * @param string $page Page to load data for
 * @param string $area The area the user is trying to access ("admin", "employee")
 * @return array Returns array with data
 */

 // Example Code snippet on how to use it:
 /*
$locale = locale_accept_from_http($_SERVER["HTTP_ACCEPT_LANGUAGE"]); // Locale retrieved from Header

$language = loadLanguage($locale, "panel"); // Loads the language for the "panel" Page

 */
function loadLanguage($locale, $page, $area){
    $langlist = [
        "de",
        "en"
    ];
    $lang_values = array_values($langlist);
    $locale = substr($locale, 0,2);
    if(in_array($locale, $lang_values)){
        if($area == "admin"){
            if(file_get_contents(dirname(__FILE__) . "/admin/{$page}" . "/snippets_" . strtoupper($locale) . ".json")){
                return json_decode(file_get_contents(dirname(__FILE__) . "/admin/{$page}" . "/snippets_" . strtoupper($locale) . ".json"), true);
            } else {
                return json_decode(file_get_contents(dirname(__FILE__) . "/admin/{$page}" . "/snippets_EN.json"), true);
            }
        } elseif($area == "employee"){
            if($e = file_get_contents(dirname(__FILE__) . "/suite/{$page}" . "/snippets_" . strtoupper($locale) . ".json")){
                return json_decode($e, true);
            } else {
                return json_decode(file_get_contents(dirname(__FILE__ . "/suite/{$page}" . "/snippets_EN.json"), true));
            }
        }
    }
}

?>