<?php
namespace Arbeitszeit {
    use Arbeitszeit\Arbeitszeit;

    class Mailbox extends Arbeitszeit
    {

        public function __construct(){
            header("HTTP/1.0 403 Forbidden");
            header("Location: /errors/403.html");
        }
    }
}


?>