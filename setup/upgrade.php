<?php
/**
 * This utility allows to manually upgrading the database scheme to the desired version needed.
 */

require_once dirname(__DIR__) . "/api/v1/inc/arbeit.inc.php";
use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Exceptions;
use Arbeitszeit\Updates;

$updates = new Updates;

$missingUpdate = 2; # if your database is at scheme 1 and the latest is 5 you should upgrade from 1 -> 2, 2 -> 3, ...
$o = $updates->perform_migration($missingUpdate);

?>