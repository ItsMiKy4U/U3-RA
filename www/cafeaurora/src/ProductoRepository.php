<?php
declare(strict_types=1);

final class ProductoRepository
{
    public function __construct(private PDO $pdo) {}

    public function listar(string $buscar = '', bool $soloDisponibles = false): array
    {
        $sql = 'SELECT id, nombre, categoria, descripcion, precio, disponible
                FROM productos WHERE 1=1';
        $params = [];
        if ($soloDisponibles) {
            $sql .= ' AND disponible = 1';
        }
        if ($buscar !== '') {
            $sql .= ' AND (nombre LIKE :por_nombre OR categoria LIKE :por_categoria OR descripcion LIKE :por_descripcion)';
            $term = '%' . $buscar . '%';
            $params = [
                'por_nombre' => $term,
                'por_categoria' => $term,
                'por_descripcion' => $term,
            ];
        }
        $sql .= ' ORDER BY categoria, nombre';
        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);
        return $statement->fetchAll();
    }

    public function buscarPorId(int $id): ?array
    {
        $statement = $this->pdo->prepare('SELECT * FROM productos WHERE id = :id');
        $statement->execute(['id' => $id]);
        $product = $statement->fetch();
        return $product ?: null;
    }

    public function crear(array $datos): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO productos (nombre, categoria, descripcion, precio, disponible)
             VALUES (:nombre, :categoria, :descripcion, :precio, :disponible)'
        );
        $statement->execute($this->normalizar($datos));
        return (int) $this->pdo->lastInsertId();
    }

    public function actualizar(int $id, array $datos): void
    {
        $statement = $this->pdo->prepare(
            'UPDATE productos
             SET nombre=:nombre, categoria=:categoria, descripcion=:descripcion,
                 precio=:precio, disponible=:disponible
             WHERE id=:id'
        );
        $statement->execute($this->normalizar($datos) + ['id' => $id]);
    }

    public function eliminar(int $id): void
    {
        $statement = $this->pdo->prepare('DELETE FROM productos WHERE id = :id');
        $statement->execute(['id' => $id]);
    }

    private function normalizar(array $datos): array
    {
        return [
            'nombre' => trim((string) $datos['nombre']),
            'categoria' => trim((string) $datos['categoria']),
            'descripcion' => trim((string) $datos['descripcion']),
            'precio' => number_format((float) $datos['precio'], 2, '.', ''),
            'disponible' => !empty($datos['disponible']) ? 1 : 0,
        ];
    }
}
 