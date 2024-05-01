<?php

namespace Arbeitszeit{
    class language extends Arbeitszeit{
        public function __construct(){
            // empty...
        }

        public function loadLanguage($locale, $page){
            $langlist = [
                "de",
                "en"
            ];

            $lang_values = array_values($langlist);
            $locale1 = substr($locale, 0, 2);
            if(in_array($locale1, $lang_values)){
                if(file_get_contents(dirname(__FILE__) . "/assets/{$page}" . "/snippets_" . strtoupper($locale) . ".json")){
                    return json_decode(file_get_contents(dirname(__FILE__) . "/{$page}" . "/snippets_" . strtoupper($locale) . ".json"), true);
                } else {
                    return json_decode(file_get_contents(dirname(__FILE__) . "/assets/{$page}" . "/snippets_EN.json"), true);
                }
            }
        }
    }
}



?>