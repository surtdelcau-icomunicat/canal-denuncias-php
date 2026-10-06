<?php
// Copy to config.local.php in this directory. Never commit that local file.
return [
    'db_host' => '127.0.0.1',
    'db_port' => 3306,
    'db_name' => 'canal_denuncias',
    'db_user' => 'CHANGE_ME',
    'db_password' => 'CHANGE_ME',
    'timezone' => 'Europe/Madrid',
    // mail() uses the server transport. SMTP is not configured by this app.
    'mail_enabled' => false,
    'mail_from' => 'notificaciones@example.com',
];
