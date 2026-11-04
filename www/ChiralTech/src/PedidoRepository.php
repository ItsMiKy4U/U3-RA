<?php
declare(strict_types=1);

final class PedidoRepository
{
    public function __construct(private PDO $pdo) {}

    public function crear(array $datos): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO pedidos
             (nombre_cliente, correo, telefono, fecha, hora, cantidad, mensaje, producto_id)
             VALUES
             (:nombre, :correo, :telefono, :fecha, :hora, :cantidad, :mensaje, :producto_id)'
        );
        $statement->execute([
            'nombre' => trim((string) $datos['nombre']),
            'correo' => strtolower(trim((string) $datos['correo'])),
            'telefono' => trim((string) $datos['telefono']),
            'fecha' => $datos['fecha'],
            'hora' => $datos['hora'],
            'cantidad' => (int) $datos['personas'], // El input del form sigue llamándose 'personas' pero lo guardamos como 'cantidad'
            'mensaje' => trim((string) ($datos['mensaje'] ?? '')),
            'producto_id' => empty($datos['producto_id']) ? null : (int) $datos['producto_id'],
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function listar(): array
    {
        return $this->pdo->query(
            'SELECT p.*, prod.nombre AS producto
             FROM pedidos p LEFT JOIN productos prod ON prod.id = p.producto_id
             ORDER BY p.fecha DESC, p.hora DESC'
        )->fetchAll();
    }
}