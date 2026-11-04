<?php
declare(strict_types=1);
$pageTitle = $pageTitle ?? 'Café Aurora';
$loggedInUser = current_user();
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#f7f3ec">
  <title><?= h($pageTitle) ?></title><link rel="stylesheet" href="/assets/styles.css">
</head>
<body>
<header class="site-header">
  <a class="brand" href="/" aria-label="Café Aurora, inicio"><span class="brand-mark">✳</span><span>café<br><strong>aurora</strong></span></a>
  <nav class="main-nav" aria-label="Navegación principal">
    <a href="/#nosotros">Nosotros</a><a href="/#menu">Menú</a><a href="/#reservar">Reservar</a>
  </nav>
  <div class="header-actions">
    <?php if ($loggedInUser): ?><a class="admin-link" href="/admin/productos.php">Panel</a>
      <form action="/salir.php" method="post" class="inline-form"><input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>"><button class="nav-button" type="submit">Salir</button></form>
    <?php else: ?><a class="admin-link" href="/login.php">Equipo Aurora ↗</a><?php endif; ?>
  </div>
</header>
<?php foreach (take_flashes() as $message): ?><div class="alert alert-<?= h($message['type']) ?> flash-message" role="status"><?= h($message['message']) ?></div><?php endforeach; ?>
 