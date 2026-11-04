<?php
declare(strict_types=1);
require __DIR__ . '/../../src/app.php';
require __DIR__ . '/../../src/ProductoRepository.php';
require_login();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('/admin/productos.php');
verify_csrf();
$id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
if (!is_int($id) || $id < 1) {
    flash('error', 'Selecciona un producto válido.');
} else {
    (new ProductoRepository(db()))->eliminar($id);
    flash('success', 'Producto eliminado. Las solicitudes existentes se conservan.');
}
redirect('/admin/productos.php');
