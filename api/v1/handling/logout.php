<?php

session_start();
setcookie("erinnern", "ne", 0 - 3600, "/");
session_unset();
session_destroy();
header("Location: ../../../login.php?info=logout");


?>