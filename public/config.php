<?php
if (PHP_SAPI !== 'cli' && realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === __FILE__) {
    http_response_code(404);
    exit;
}

$configFile = dirname(__DIR__) . '/config.local.php';
if (!is_file($configFile)) {
    if (PHP_SAPI === 'cli') {
        fwrite(STDERR, "Configuracion pendiente: crea config.local.php fuera de public/.\n");
        exit(1);
    }
    http_response_code(503);
    exit('Configuracion pendiente: crea config.local.php fuera de public/.');
}

try {
    $settings = require $configFile;
    if (!is_array($settings) || empty($settings['db_name']) || empty($settings['db_user'])
        || $settings['db_user'] === 'CHANGE_ME' || ($settings['db_password'] ?? '') === 'CHANGE_ME') {
        throw new RuntimeException('Invalid configuration');
    }
    date_default_timezone_set($settings['timezone'] ?? 'Europe/Madrid');
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $conn = new mysqli(
        $settings['db_host'] ?? '127.0.0.1',
        $settings['db_user'],
        $settings['db_password'] ?? '',
        $settings['db_name'],
        (int) ($settings['db_port'] ?? 3306)
    );
    $conn->set_charset('utf8mb4');
} catch (Throwable $error) {
    if (PHP_SAPI === 'cli') {
        fwrite(STDERR, "No se pudo conectar. Revisa config.local.php y MySQL/MariaDB.\n");
        exit(1);
    }
    http_response_code(503);
    exit('No se pudo conectar. Revisa la configuracion local y el servicio MySQL/MariaDB.');
}
