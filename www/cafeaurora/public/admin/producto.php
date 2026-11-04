<?php
declare(strict_types=1);
require __DIR__ . '/../../src/app.php';
require __DIR__ . '/../../src/ProductoRepository.php';
$user = require_login();
$repository = new ProductoRepository(db());
$id = filter_var($_GET['id'] ?? '', FILTER_VALIDATE_INT);
$product = is_int($id) && $id > 0 ? $repository->buscarPorId($id) : null;
$isEdit = $product !== null;
if (isset($_GET['id']) && !$isEdit) { http_response_code(404); exit('Producto no encontrado.'); }
$errores = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $postedId = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
    $id = is_int($postedId) ? $postedId : null;
    $isEdit = $id !== null && $id > 0;
    $product = [
        'nombre' => trim((string) ($_POST['nombre'] ?? '')),
        'categoria' => trim((string) ($_POST['categoria'] ?? '')),
        'descripcion' => trim((string) ($_POST['descripcion'] ?? '')),
        'precio' => trim((string) ($_POST['precio'] ?? '')),
        'disponible' => isset($_POST['disponible']) ? 1 : 0,
    ];
    if ($product['nombre'] === '' || mb_strlen($product['nombre']) > 100) $errores[] = 'El nombre es obligatorio (máximo 100 caracteres).';
    if ($product['categoria'] === '' || mb_strlen($product['categoria']) > 60) $errores[] = 'La categoría es obligatoria (máximo 60 caracteres).';
    if ($product['descripcion'] === '' || mb_strlen($product['descripcion']) > 255) $errores[] = 'Escribe una descripción de hasta 255 caracteres.';
    if (!is_numeric($product['precio']) || (float) $product['precio'] < 0 || (float) $product['precio'] > 999999.99) $errores[] = 'Escribe un precio válido entre 0 y 999999.99.';
    if (!$errores) {
        if ($isEdit) {
            if (!$repository->buscarPorId($id)) { http_response_code(404); exit('Producto no encontrado.'); }
            $repository->actualizar($id, $product);
            flash('success', 'Producto actualizado.');
        } else {
            $repository->crear($product);
            flash('success', 'Producto creado.');
        }
        redirect('/admin/productos.php');
    }
}
$pageTitle = ($isEdit ? 'Editar producto' : 'Nuevo producto') . ' | Café Aurora';
require __DIR__ . '/../partials/header.php';
?>
<main class="admin-page form-admin-page"><a class="back-link" href="/admin/productos.php">← Volver al menú</a><div class="section-kicker">CRUD · <?= $isEdit ? 'UPDATE' : 'CREATE' ?></div><h1><?= $isEdit ? 'Editar producto.' : 'Nuevo producto.' ?></h1><p>Los datos se validan en PHP y se guardan mediante ProductoRepository.</p>
<?php foreach ($errores as $error): ?><div class="alert alert-error"><?= h($error) ?></div><?php endforeach; ?>
<form method="post" class="admin-form"><input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>"><?php if ($isEdit): ?><input type="hidden" name="id" value="<?= (int) $id ?>"><?php endif; ?>
<label>Nombre<input name="nombre" maxlength="100" required value="<?= h((string) ($product['nombre'] ?? '')) ?>"></label>
<label>Categoría<input name="categoria" maxlength="60" required value="<?= h((string) ($product['categoria'] ?? '')) ?>"></label>
<label>Descripción<textarea name="descripcion" maxlength="255" rows="3" required><?= h((string) ($product['descripcion'] ?? '')) ?></textarea></label>
<label>Precio en pesos<input name="precio" type="number" min="0" max="999999.99" step="0.01" required value="<?= h((string) ($product['precio'] ?? '')) ?>"></label>
<label class="check-label"><input type="checkbox" name="disponible" value="1" <?= !isset($product['disponible']) || $product['disponible'] ? 'checked' : '' ?>> Disponible en el menú público</label>
<div class="form-actions"><button class="button button-dark"><?= $isEdit ? 'Guardar cambios' : 'Crear producto' ?></button><a class="text-link" href="/admin/productos.php">Cancelar</a></div></form></main>
<?php require __DIR__ . '/../partials/footer.php'; ?>
