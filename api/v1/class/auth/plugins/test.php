<?php
require "../../arbeitszeit.inc.php";
require "./mail_new_entry.auth.arbeit.inc.php";
use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Auth;
use Arbeitszeit\Auth\NewMailEntry;

$ab = new Arbeitszeit;
$auth = new Auth();
$nbe = new NewMailEntry;


$nbe->mail_new_entry("asze", 1, $auth->mail_init("asze", true));
?>