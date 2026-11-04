<?php
declare(strict_types=1);
require __DIR__ . '/../src/app.php';

if (current_user()) redirect('/admin/productos.php');
$setupAvailable = (int) db()->query('SELECT COUNT(*) FROM usuarios')->fetchColumn() === 0;
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $correo = strtolower(trim((string) ($_POST['correo'] ?? '')));
    $password = (string) ($_POST['password'] ?? '');
    $statement = db()->prepare('SELECT id, nombre, correo, password_hash, rol FROM usuarios WHERE correo=:correo LIMIT 1');
    $statement->execute(['correo' => $correo]);
    $user = $statement->fetch();
    if ($user && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);
        unset($user['password_hash']);
        $_SESSION['user'] = $user;
        flash('success', 'Hola, ' . $user['nombre'] . '. Tu sesión está activa.');
        redirect('/admin/productos.php');
    }
    $error = 'El correo o la contraseña no coinciden.';
}
$pageTitle = 'Iniciar sesión | Café Aurora';
require __DIR__ . '/partials/header.php';
?>
<main class="account-page"><section class="account-card"><div class="section-kicker">QUÉ BUENO VERTE</div><h1>Inicia <em>sesión.</em></h1><p>Entra al panel para administrar el menú y revisar solicitudes.</p>
<?php if ($error): ?><div class="alert alert-error"><?= h($error) ?></div><?php endif; ?>
<form method="post" action="/login.php" class="stack-form"><input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
<label>Correo electrónico<input name="correo" type="email" maxlength="190" autocomplete="email" required></label>
<label>Contraseña<input name="password" type="password" autocomplete="current-password" required></label>
<button class="button button-dark button-wide">Entrar al panel</button></form>
<?php if ($setupAvailable): ?><p class="account-switch">Primera visita del docente: <a href="/setup.php">crear la cuenta administradora</a>.</p>
<?php else: ?><p class="account-switch">¿Aún no tienes cuenta? <a href="/registro.php">Crear cuenta de editor</a></p><?php endif; ?>
</section></main>
<?php require __DIR__ . '/partials/footer.php'; ?>
 