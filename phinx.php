<?php
require_once __DIR__ . "/api/v1/inc/arbeit.inc.php";
use Arbeitszeit\Arbeitszeit;

$data = Arbeitszeit::get_app_ini();
return
[
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/migrations/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/migrations/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'production',
        'production' => [
            'adapter' => 'mysql',
            'host' => $data['mysql']['db_host'],
            'name' => $data['mysql']['db'],
            'user' => $data['mysql']['db_user'],
            'pass' => $data['mysql']['db_password'],
            'port' => '3306',
            'charset' => 'utf8',
        ],
        // you can add development and testing environments here
    ],
    'version_order' => 'creation'
];
