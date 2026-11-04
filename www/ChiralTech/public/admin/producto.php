<?php
declare(strict_types=1);
require __DIR__ . '/../../src/app.php';
require __DIR__ . '/../../src/ProductoRepository.php';
$user = require_login();
$repository = new ProductoRepository(db());
$id = filter_var($_GET['id'] ?? '', FILTER_VALIDATE_INT);
$product = is_int($id) && $id > 0 ? $repository->buscarPorId($id) : null;
$isEdit = $product !== null;
if (isset($_GET['id']) && !$isEdit) { http_response_code(404); exit('Componente no encontrado.'); }
$errores = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $postedId = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
    $id = is_int($postedId) ? $postedId : null;
    $isEdit = $id !== null && $id > 0;
    $product = [
        'nombre' => trim((string) ($_POST['nombre'] ?? '')),
        'categoria_id' => trim((string) ($_POST['categoria_id'] ?? '')),
        'descripcion' => trim((string) ($_POST['descripcion'] ?? '')),
        'especificaciones' => trim((string) ($_POST['especificaciones'] ?? '')),
        'precio' => trim((string) ($_POST['precio'] ?? '')),
        'stock' => trim((string) ($_POST['stock'] ?? '')),
        'imagen_url' => trim((string) ($_POST['imagen_url'] ?? '')),
    ];
    if ($product['nombre'] === '' || mb_strlen($product['nombre']) > 150) $errores[] = 'El nombre es obligatorio (máximo 150 caracteres).';
    if (!is_numeric($product['categoria_id']) || (int) $product['categoria_id'] <= 0) $errores[] = 'Ingresa un ID de categoría válido.';
    if ($product['descripcion'] === '' || mb_strlen($product['descripcion']) > 500) $errores[] = 'Escribe una descripción válida.';
    if (!is_numeric($product['precio']) || (float) $product['precio'] < 0 || (float) $product['precio'] > 999999.99) $errores[] = 'Escribe un precio válido entre 0 y 999999.99.';
    if (!is_numeric($product['stock']) || (int) $product['stock'] < 0) $errores[] = 'El stock debe ser un número entero mayor o igual a 0.';
    if (!$errores) {
        if ($isEdit) {
            if (!$repository->buscarPorId($id)) { http_response_code(404); exit('Componente no encontrado.'); }
            $repository->actualizar($id, $product);
            flash('success', 'Componente actualizado.');
        } else {
            $repository->crear($product);
            flash('success', 'Componente registrado.');
        }
        redirect('/admin/productos.php');
    }
}
$pageTitle = ($isEdit ? 'Editar componente' : 'Nuevo componente') . ' | Chiral Tech';
require __DIR__ . '/../partials/header.php';
?>
<main class="admin-page form-admin-page"><a class="back-link" href="/admin/productos.php">← Volver al inventario</a><div class="section-kicker">CRUD · <?= $isEdit ? 'UPDATE' : 'CREATE' ?></div><h1><?= $isEdit ? 'Editar componente.' : 'Nuevo componente.' ?></h1><p>Los datos se validan en PHP y se guardan mediante ProductoRepository.</p>
<?php foreach ($errores as $error): ?><div class="alert alert-error"><?= h($error) ?></div><?php endforeach; ?>
<form method="post" class="admin-form"><input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>"><?php if ($isEdit): ?><input type="hidden" name="id" value="<?= (int) $id ?>"><?php endif; ?>
<label>Nombre del hardware<input name="nombre" maxlength="150" required value="<?= h((string) ($product['nombre'] ?? '')) ?>"></label>
<label>ID de Categoría (Ej. 1: CPU, 2: GPU)<input name="categoria_id" type="number" required value="<?= h((string) ($product['categoria_id'] ?? '')) ?>"></label>
<label>Especificaciones (Ej. AM4, 16GB, GDDR6)<input name="especificaciones" type="text" value="<?= h((string) ($product['especificaciones'] ?? '')) ?>"></label>
<label>Descripción<textarea name="descripcion" maxlength="500" rows="3" required><?= h((string) ($product['descripcion'] ?? '')) ?></textarea></label>
<label>Precio en MXN<input name="precio" type="number" min="0" max="999999.99" step="0.01" required value="<?= h((string) ($product['precio'] ?? '')) ?>"></label>
<label>Stock disponible<input name="stock" type="number" min="0" required value="<?= h((string) ($product['stock'] ?? '0')) ?>"></label>
<label>URL de la imagen (Opcional)<input name="imagen_url" type="text" value="<?= h((string) ($product['imagen_url'] ?? '')) ?>"></label>
<div class="form-actions"><button class="button button-dark"><?= $isEdit ? 'Guardar cambios' : 'Registrar pieza' ?></button><a class="text-link" href="/admin/productos.php">Cancelar</a></div></form></main>
<?php require __DIR__ . '/../partials/footer.php'; ?>