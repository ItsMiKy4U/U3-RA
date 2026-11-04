<?php
declare(strict_types=1);
require __DIR__ . '/../src/app.php';
require __DIR__ . '/../src/ReservaRepository.php';
require __DIR__ . '/../src/ProductoRepository.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/');
}
verify_csrf();
$datos = [
    'nombre' => trim((string) ($_POST['nombre'] ?? '')),
    'correo' => trim((string) ($_POST['correo'] ?? '')),
    'telefono' => trim((string) ($_POST['telefono'] ?? '')),
    'fecha' => trim((string) ($_POST['fecha'] ?? '')),
    'hora' => trim((string) ($_POST['hora'] ?? '')),
    'personas' => filter_var($_POST['personas'] ?? null, FILTER_VALIDATE_INT),
    'producto_id' => filter_var($_POST['producto_id'] ?? '', FILTER_VALIDATE_INT),
    'mensaje' => trim((string) ($_POST['mensaje'] ?? '')),
];
$errores = [];
if ($datos['nombre'] === '' || mb_strlen($datos['nombre']) > 100) $errores[] = 'Escribe un nombre de hasta 100 caracteres.';
if (!filter_var($datos['correo'], FILTER_VALIDATE_EMAIL) || mb_strlen($datos['correo']) > 190) $errores[] = 'Escribe un correo electrónico válido.';
if ($datos['telefono'] === '' || mb_strlen($datos['telefono']) > 30) $errores[] = 'Escribe un teléfono de hasta 30 caracteres.';
$date = DateTime::createFromFormat('!Y-m-d', $datos['fecha']);
if (!$date || $date->format('Y-m-d') !== $datos['fecha'] || $datos['fecha'] < date('Y-m-d')) $errores[] = 'Selecciona una fecha válida a partir de hoy.';
if (!preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $datos['hora'])) $errores[] = 'Selecciona una hora válida.';
if (!is_int($datos['personas']) || $datos['personas'] < 1 || $datos['personas'] > 8) $errores[] = 'Selecciona de 1 a 8 personas.';
if (mb_strlen($datos['mensaje']) > 500) $errores[] = 'La nota permite hasta 500 caracteres.';
if ($datos['producto_id'] === false) $datos['producto_id'] = null;
if (is_int($datos['producto_id']) && !(new ProductoRepository(db()))->buscarPorId($datos['producto_id'])) $errores[] = 'El producto seleccionado no existe.';

if ($errores) {
    foreach ($errores as $error) flash('error', $error);
    redirect('/#reservar');
}
try {
    (new ReservaRepository(db()))->crear($datos);
    flash('success', '¡Gracias, ' . $datos['nombre'] . '! Guardamos tu solicitud de práctica. El equipo confirmará el horario.');
} catch (PDOException $exception) {
    flash('error', 'No pudimos guardar la solicitud. Verifica la conexión a la base de datos e inténtalo otra vez.');
}
redirect('/#reservar');
 