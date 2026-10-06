<?php
// CLI only. Password arrives on stdin, never as a command-line argument.
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}
if ($argc !== 3) {
    fwrite(STDERR, "Usage: php tools/create-admin.php USER EMAIL < password-from-stdin\n");
    exit(1);
}
$user = trim($argv[1]);
$email = trim($argv[2]);
if ($user === '' || strlen($user) > 100 || strlen($email) > 254 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    fwrite(STDERR, "Invalid username or email.\n");
    exit(1);
}
$password = rtrim(stream_get_contents(STDIN), "\r\n");
if (strlen($password) < 12 || strlen($password) > 72) {
    fwrite(STDERR, "Choose a password between 12 and 72 bytes.\n");
    exit(1);
}
require dirname(__DIR__) . '/public/config.php';
try {
    $hash = password_hash($password, PASSWORD_BCRYPT);
    unset($password);
    $stmt = $conn->prepare('INSERT INTO admins (usuario, `contraseña`, email) VALUES (?, ?, ?)');
    $stmt->bind_param('sss', $user, $hash, $email);
    $stmt->execute();
    $stmt->close();
    $conn->close();
    fwrite(STDOUT, "Administrator created. Sign in at admin_login.php.\n");
} catch (Throwable $error) {
    fwrite(STDERR, "Cannot create administrator. Check schema and whether the username already exists.\n");
    exit(1);
}
