<?php
declare(strict_types=1);
require __DIR__ . '/../../src/app.php';
require __DIR__ . '/../../src/ProductoRepository.php';
$user = require_login();
$productos = (new ProductoRepository(db()))->listar();
$pageTitle = 'Administrar menú | Café Aurora';
require __DIR__ . '/../partials/header.php';
?>
<main class="admin-page"><div class="admin-heading"><div><div class="section-kicker">PANEL · SESIÓN DE <?= h(mb_strtoupper($user['nombre'])) ?></div><h1>El menú <em>Aurora.</em></h1><p>Consulta los registros y crea, actualiza o elimina productos de la base de datos.</p></div><a class="button button-dark" href="/admin/producto.php">+ Nuevo producto</a></div>
<nav class="admin-tabs"><a class="active" href="/admin/productos.php">Productos · CRUD</a><a href="/admin/reservas.php">Reservaciones</a></nav>
<div class="table-wrap"><table><thead><tr><th>Producto</th><th>Categoría</th><th>Precio</th><th>Disponibilidad</th><th>Acciones</th></tr></thead><tbody>
<?php foreach ($productos as $producto): ?><tr><td><strong><?= h($producto['nombre']) ?></strong><small><?= h($producto['descripcion']) ?></small></td><td><?= h($producto['categoria']) ?></td><td>$<?= number_format((float) $producto['precio'], 2) ?></td><td><span class="status <?= $producto['disponible'] ? 'status-active' : 'status-muted' ?>"><?= $producto['disponible'] ? 'Disponible' : 'Agotado' ?></span></td><td class="table-actions"><a href="/admin/producto.php?id=<?= (int) $producto['id'] ?>">Editar</a><form action="/admin/eliminar-producto.php" method="post" onsubmit="return confirm('¿Eliminar este producto del menú?')"><input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>"><input type="hidden" name="id" value="<?= (int) $producto['id'] ?>"><button class="danger-link" type="submit">Eliminar</button></form></td></tr><?php endforeach; ?>
<?php if (!$productos): ?><tr><td colspan="5">Aún no hay productos. Crea el primero con el botón superior.</td></tr><?php endif; ?>
</tbody></table></div>
<p class="teaching-note"><span>PISTA DE CÓDIGO</span> La clase ProductoRepository prepara sentencias INSERT, SELECT, UPDATE y DELETE para este panel.</p></main>
<?php require __DIR__ . '/../partials/footer.php'; ?>
