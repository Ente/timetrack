#!/bin/bash
set -e

php <<'PHP'
<?php
$required = ['TIMETRACK_BASE_URL', 'MYSQL_PASSWORD'];
foreach ($required as $key) {
    if (getenv($key) === false || getenv($key) === '') {
        fwrite(STDERR, "Missing required environment variable: {$key}\n");
        exit(1);
    }
}

$env = static function (string $key, string $default = ''): string {
    $value = getenv($key);
    return $value === false ? $default : $value;
};

$bool = static function (string $key, bool $default = false) use ($env): bool {
    return filter_var($env($key, $default ? 'true' : 'false'), FILTER_VALIDATE_BOOLEAN);
};

$config = [
    'general' => [
        'app_name' => $env('APP_NAME', 'TimeTrack'),
        'base_url' => $env('TIMETRACK_BASE_URL'),
        'support_email' => $env('SUPPORT_EMAIL', 'support@example.com'),
        'debug' => 'false',
        'auto_update' => 'false',
        'timezone' => $env('TIMEZONE', 'Europe/Berlin'),
        'theme_file' => '/assets/css/v8.css',
        'force_theme' => false,
        'demo' => false,
        'telemetry' => 'disabled',
        'telemetry_server_url' => 'https://telemetry.openducks.org/timetrack/submit',
        'telemetryServer' => false,
    ],
    'mysql' => [
        'db_host' => 'db',
        'db_user' => $env('MYSQL_USER', 'timetool'),
        'db_password' => $env('MYSQL_PASSWORD'),
        'db' => $env('MYSQL_DATABASE', 'ab'),
    ],
    'smtp' => [
        'smtp' => $bool('SMTP_ENABLED'),
        'host' => $env('SMTP_HOST', 'smtp.example.com'),
        'username' => $env('SMTP_USERNAME'),
        'password' => $env('SMTP_PASSWORD'),
        'port' => $env('SMTP_PORT', '587'),
        'usessl' => $env('SMTP_USE_SSL', 'false'),
    ],
    'plugins' => [
        'plugins' => 'true',
        'path' => '/api/v1/class/plugins/plugins',
        'data' => 'data',
        'testing' => 'false',
    ],
    'ldap' => [
        'ldap' => 'false',
        'ldap_user' => '',
        'ldap_password' => '',
        'ldap_host' => '',
        'ldap_ip' => '',
        'ldap_domain' => '',
        'ldap_basedn' => '',
        'ldap_group' => '',
        'saf' => 'true',
        'saf_user' => '',
        'saf_password' => '',
        'saf_host' => '',
        'saf_ip' => '',
        'saf_basedn' => '',
        'saf_group' => '',
        'saf_domain' => '',
        'create_user' => 'false',
    ],
    'config' => [
        'worktime_types' => '/api/v1/inc/config/worktime_types.json',
        'vacation_types' => '/api/v1/inc/config/vacation_types.json',
        'sickness_types' => '/api/v1/inc/config/sickness_types.json',
        'default_worktime_type' => 1,
    ],
    'mobile' => [
        'allow_app_use' => false,
        'allow_api_token_generation_w_settings' => true,
        'enable_rate_limit_client_requests' => true,
        'rate_limit_client_requests_p_minute' => 80,
        'enable_qr_code_pairing' => false,
    ],
];

$path = '/var/www/html/api/v1/inc/app.json';
file_put_contents($path, json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
PHP

chown www-data:www-data /var/www/html/api/v1/inc/app.json

exec /entrypoint.sh
