<?php
declare(strict_types=1);

final class ProductoRepository
{
    public function __construct(private PDO $pdo) {}

    public function listar(string $buscar = '', bool $soloDisponibles = false): array
    {
        // Se hace un JOIN con la tabla de categorias para traer el nombre real de la categoría
        $sql = 'SELECT p.*, c.nombre as categoria_nombre 
                FROM productos p 
                LEFT JOIN categorias c ON p.categoria_id = c.id 
                WHERE 1=1';
        $params = [];
        
        if ($soloDisponibles) {
            // En lugar de "disponible = 1", validamos que haya stock
            $sql .= ' AND p.stock > 0';
        }
        
        if ($buscar !== '') {
            // Agregamos especificaciones a la búsqueda para que encuentren sockets, frecuencias, etc.
            $sql .= ' AND (p.nombre LIKE :por_nombre OR c.nombre LIKE :por_categoria OR p.descripcion LIKE :por_descripcion OR p.especificaciones LIKE :por_especificaciones)';
            $term = '%' . $buscar . '%';
            $params = [
                'por_nombre' => $term,
                'por_categoria' => $term,
                'por_descripcion' => $term,
                'por_especificaciones' => $term,
            ];
        }
        
        $sql .= ' ORDER BY c.nombre, p.nombre';
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
            'INSERT INTO productos (nombre, descripcion, especificaciones, precio, stock, categoria_id, imagen_url)
             VALUES (:nombre, :descripcion, :especificaciones, :precio, :stock, :categoria_id, :imagen_url)'
        );
        $statement->execute($this->normalizar($datos));
        return (int) $this->pdo->lastInsertId();
    }

    public function actualizar(int $id, array $datos): void
    {
        $statement = $this->pdo->prepare(
            'UPDATE productos
             SET nombre=:nombre, descripcion=:descripcion, especificaciones=:especificaciones,
                 precio=:precio, stock=:stock, categoria_id=:categoria_id, imagen_url=:imagen_url
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
        // Formateamos y casteamos los tipos de datos correctos para el nuevo esquema SQL
        return [
            'nombre' => trim((string) ($datos['nombre'] ?? '')),
            'descripcion' => trim((string) ($datos['descripcion'] ?? '')),
            'especificaciones' => trim((string) ($datos['especificaciones'] ?? '')),
            'precio' => number_format((float) ($datos['precio'] ?? 0), 2, '.', ''),
            'stock' => (int) ($datos['stock'] ?? 0),
            'categoria_id' => (int) ($datos['categoria_id'] ?? 0),
            'imagen_url' => trim((string) ($datos['imagen_url'] ?? '')),
        ];
    }
}