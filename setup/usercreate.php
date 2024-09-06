<?php
    /*
     * Helper for first user creation
     * This script allows 3 input variables:
     * username, desired password and email
     * and returns an SQL statement to be run in the database
     */

if(isset($argv[1], $argv[2], $argv[3])){
    $username = $argv[1];
    $pass = password_hash($argv[2], PASSWORD_DEFAULT);
    $email = $argv[3];

    $statement = "INSERT INTO `users` (`id`, `name`, `username`, `email`, `password`, `email_confirmed`, `isAdmin`, `state`, `easymode`) VALUES (NULL, '{$username}','{$username}', '{$email}', '{$pass}', 1, 1, NULL, 0);";
    echo $statement;
    return 0;
} else {
    return 1;
}

