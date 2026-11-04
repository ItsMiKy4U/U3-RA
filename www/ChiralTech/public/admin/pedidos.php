<?php
declare(strict_types=1);
require __DIR__ . '/../../src/app.php';
require __DIR__ . '/../../src/ReservaRepository.php';
$user = require_login();
$reservas = (new ReservaRepository(db()))->listar();
$pageTitle = 'Reservaciones | Café Aurora';
require __DIR__ . '/../partials/header.php';
?>
<main class="admin-page"><div class="admin-heading"><div><div class="section-kicker">PANEL · SESIÓN DE <?= h(mb_strtoupper($user['nombre'])) ?></div><h1>Solicitudes <em>recibidas.</em></h1><p>Consulta los formularios POST que quedaron guardados en MySQL.</p></div><a class="button button-dark" href="/">Ver landing ↗</a></div>
<nav class="admin-tabs"><a href="/admin/productos.php">Productos · CRUD</a><a class="active" href="/admin/reservas.php">Reservaciones</a></nav>
<div class="table-wrap"><table><thead><tr><th>Cliente</th><th>Visita</th><th>Personas</th><th>Preferencia</th><th>Estado</th></tr></thead><tbody>
<?php foreach ($reservas as $reserva): ?><tr><td><strong><?= h($reserva['nombre_cliente']) ?></strong><small><?= h($reserva['correo']) ?> · <?= h($reserva['telefono']) ?></small><?php if ($reserva['mensaje']): ?><small><?= h($reserva['mensaje']) ?></small><?php endif; ?></td><td><?= h($reserva['fecha']) ?><small><?= h(substr($reserva['hora'], 0, 5)) ?></small></td><td><?= (int) $reserva['personas'] ?></td><td><?= h($reserva['producto'] ?? 'Sin preferencia') ?></td><td><span class="status status-pending"><?= h(ucfirst($reserva['estado'])) ?></span></td></tr><?php endforeach; ?>
<?php if (!$reservas): ?><tr><td colspan="5">Todavía no hay solicitudes. Envía una reservación desde la página principal para ver el flujo POST.</td></tr><?php endif; ?>
</tbody></table></div>
<p class="teaching-note"><span>ACTIVIDAD</span> Envía una reservación desde la landing y observa el registro nuevo en esta tabla. La lista la genera ReservaRepository.</p></main>
<?php require __DIR__ . '/../partials/footer.php'; ?>
