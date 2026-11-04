<?php
declare(strict_types=1);

final class ReservaRepository
{
    public function __construct(private PDO $pdo) {}

    public function crear(array $datos): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO reservas
             (nombre_cliente, correo, telefono, fecha, hora, personas, mensaje, producto_id)
             VALUES
             (:nombre, :correo, :telefono, :fecha, :hora, :personas, :mensaje, :producto_id)'
        );
        $statement->execute([
            'nombre' => trim((string) $datos['nombre']),
            'correo' => strtolower(trim((string) $datos['correo'])),
            'telefono' => trim((string) $datos['telefono']),
            'fecha' => $datos['fecha'],
            'hora' => $datos['hora'],
            'personas' => (int) $datos['personas'],
            'mensaje' => trim((string) ($datos['mensaje'] ?? '')),
            'producto_id' => empty($datos['producto_id']) ? null : (int) $datos['producto_id'],
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function listar(): array
    {
        return $this->pdo->query(
            'SELECT r.*, p.nombre AS producto
             FROM reservas r LEFT JOIN productos p ON p.id = r.producto_id
             ORDER BY r.fecha DESC, r.hora DESC'
        )->fetchAll();
    }
}
 