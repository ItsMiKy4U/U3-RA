<?php
declare(strict_types=1);
require __DIR__ . '/../src/app.php';

$pdo = db();
$lock = (int) $pdo->query("SELECT GET_LOCK('cafe_aurora_u3_setup', 3)")->fetchColumn() === 1;
if (!$lock) {
    http_response_code(503);
    exit('No se pudo abrir la configuración inicial. Espera un momento y recarga.');
}
$count = (int) $pdo->query('SELECT COUNT(*) FROM usuarios')->fetchColumn();
if ($count > 0) {
    $pdo->query("SELECT RELEASE_LOCK('cafe_aurora_u3_setup')");
    http_response_code(404);
    exit('La cuenta administradora inicial ya fue creada. Inicia sesión en /login.php.');
}
$errores = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $nombre = trim((string) ($_POST['nombre'] ?? ''));
    $correo = strtolower(trim((string) ($_POST['correo'] ?? '')));
    $password = (string) ($_POST['password'] ?? '');
    if ($nombre === '' || mb_strlen($nombre) > 100) $errores[] = 'Escribe tu nombre (máximo 100 caracteres).';
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL) || mb_strlen($correo) > 190) $errores[] = 'Escribe un correo válido.';
    if (strlen($password) < 12) $errores[] = 'La contraseña debe tener al menos 12 caracteres.';
    if (!$errores) {
        $statement = $pdo->prepare("INSERT INTO usuarios (nombre, correo, password_hash, rol) VALUES (:nombre, :correo, :hash, 'admin')");
        $statement->execute(['nombre' => $nombre, 'correo' => $correo, 'hash' => password_hash($password, PASSWORD_DEFAULT)]);
        session_regenerate_id(true);
        $_SESSION['user'] = ['id' => (int) $pdo->lastInsertId(), 'nombre' => $nombre, 'correo' => $correo, 'rol' => 'admin'];
        $pdo->query("SELECT RELEASE_LOCK('cafe_aurora_u3_setup')");
        flash('success', 'Cuenta administradora lista. Ya puedes administrar el catálogo.');
        redirect('./login.php');
    }
}
$pdo->query("SELECT RELEASE_LOCK('cafe_aurora_u3_setup')");
$pageTitle = 'Configuración inicial | Café Aurora';
require __DIR__ . '/partials/header.php';
?>
<main class="account-page"><section class="account-card"><div class="section-kicker"></div><h1>Crear cuenta<br><em>administradora.</em></h1><p></p>
<?php foreach ($errores as $error): ?><div class="alert alert-error"><?= h($error) ?></div><?php endforeach; ?>
<form method="post" action="/setup.php" class="stack-form"><input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
<label>Nombre<input name="nombre" maxlength="100" autocomplete="name" required value="<?= h(old('nombre')) ?>"></label>
<label>Correo electrónico<input name="correo" type="email" maxlength="190" autocomplete="email" required value="<?= h(old('correo')) ?>"></label>
<label>Contraseña <small>mínimo 12 caracteres; elige una propia</small><input name="password" type="password" minlength="12" autocomplete="new-password" required></label>
<button class="button button-dark button-wide">Crear administración inicial</button></form>
<p class="account-switch"><a href="/">Volver a la cafetería</a></p></section></main>
<?php require __DIR__ . '/partials/footer.php'; ?>
 