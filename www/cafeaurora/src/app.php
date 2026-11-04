<?php
declare(strict_types=1);

$configFile = dirname(__DIR__) . '/config/config.php';
if (!is_file($configFile)){
    http_response_code(500);
    exit('Falta config/config.php. Revise la conexión e intente de nuevo.');
}

$config = require $configFile;
date_default_timezone_set($config['app']['timezone'] ?? 'America/Mexico_city');

if (session_status() !== PHP_SESSION_ACTIVE) {
	$isSecure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
	session_name('cafe_aurora_session');
	session_set_cookie_params([
		'lifetime' => 0,
		'path' => '/',
		'secure' => $isSecure,
		'httponly' => true,
		'samesite' => 'Lax',
	]);
    ini_set('session.use_strict_mode', '1');
    session_start();
}

function db(): PDO
{
    static $pdo = null;
    global $config;
    if ($pdo instanceof PDO) {
        return $pdo;
    }
    $dbconfig = $config['db'];
    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=%s',
        $dbconfig['host'],
        $dbconfig['port'],
        $dbconfig['database'],
        $dbconfig['charset'] ?? 'utf8mb4'
    );
    
    $pdo = new PDO($dsn, $dbconfig['username'], $dbconfig['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    return $pdo;      
}

function h(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function redirect(string $location): never
{
    header('Location: ' . $location);
    exit;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf(): void
{
    $sent = $_POST['csrf_token'] ?? '';
    if (!is_string($sent) || !hash_equals(csrf_token(), $sent)) {
        http_response_code(419);
        exit('El formulario expiró. Regresa y vuelve a intentarlo.');
    }
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function take_flashes(): array
{
    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $messages;
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function require_login(): array
{
    $user = current_user();
    if (!$user) {
        flash('error', 'Inicia sesión para entrar al panel.');
        redirect('/login.php');
    }
    return $user;
}

function require_admin(): array
{
    $user = require_login();
    if (($user['rol'] ?? '') !== 'admin') {
        http_response_code(403);
        exit('Esta sección requiere una cuenta administradora.');
    }
    return $user;
}

function old(string $key, string $default = ''): string
{
    $value = $_POST[$key] ?? $default;
    return is_string($value) ? $value : $default;
}