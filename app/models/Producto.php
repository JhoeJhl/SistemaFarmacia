<?php
require_once __DIR__ . '/../config/Database.php';

class Producto {
    private $conn;
    private $table = 'productos';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->conectar();
    }

    // 1. OBTENER TODOS (Corrección aquí: id_proveedor)
    public function obtenerTodos() {
        $query = "SELECT p.*, c.nombre as categoria, pr.nombre as proveedor 
                  FROM " . $this->table . " p
                  LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
                  LEFT JOIN proveedores pr ON p.id_proveedor = pr.id_proveedor
                  WHERE p.estado = 1
                  ORDER BY p.id_producto DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 2. OBTENER POR TIPO (Corrección aquí también)
    public function obtenerPorTipo($tipo) {
        $query = "SELECT p.*, c.nombre as categoria 
                  FROM " . $this->table . " p
                  LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
                  WHERE p.tipo = :tipo AND p.estado = 1
                  ORDER BY p.id_producto DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 3. OBTENER POR ID (Para editar)
    public function obtenerPorId($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id_producto = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // 4. BÚSQUEDA (Para el POS)
    public function buscarPorNombre($termino) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE (nombre LIKE :termino OR codigo_barras LIKE :termino) 
                  AND estado = 1 AND stock > 0 LIMIT 10";
        $stmt = $this->conn->prepare($query);
        $termino = "%$termino%";
        $stmt->bindParam(':termino', $termino);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 5. CREAR PRODUCTO
    public function crear($codigo, $nombre, $desc, $cat, $stock, $p_compra, $p_venta, $vence, $prov, $tipo, $imagen) {
        // Asegúrate de usar id_proveedor aquí también
        $query = "INSERT INTO " . $this->table . " 
                  (codigo_barras, nombre, descripcion, id_categoria, stock, precio_compra, precio_venta, fecha_vencimiento, id_proveedor, tipo, imagen)
                  VALUES (:codigo, :nombre, :desc, :cat, :stock, :p_compra, :p_venta, :vence, :prov, :tipo, :imagen)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':codigo', $codigo);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':desc', $desc);
        $stmt->bindParam(':cat', $cat);
        $stmt->bindParam(':stock', $stock);
        $stmt->bindParam(':p_compra', $p_compra);
        $stmt->bindParam(':p_venta', $p_venta);
        $stmt->bindParam(':vence', $vence);
        $stmt->bindParam(':prov', $prov);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->bindParam(':imagen', $imagen);

        return $stmt->execute();
    }

    // 6. ACTUALIZAR PRODUCTO
    public function actualizar($id, $codigo, $nombre, $desc, $cat, $stock, $p_compra, $p_venta, $vence, $prov, $tipo, $imagen) {
        $query = "UPDATE " . $this->table . " 
                  SET codigo_barras = :codigo, 
                      nombre = :nombre, 
                      descripcion = :desc, 
                      id_categoria = :cat, 
                      stock = :stock, 
                      precio_compra = :p_compra, 
                      precio_venta = :p_venta, 
                      fecha_vencimiento = :vence, 
                      id_proveedor = :prov, 
                      tipo = :tipo,
                      imagen = :imagen
                  WHERE id_producto = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':codigo', $codigo);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':desc', $desc);
        $stmt->bindParam(':cat', $cat);
        $stmt->bindParam(':stock', $stock);
        $stmt->bindParam(':p_compra', $p_compra);
        $stmt->bindParam(':p_venta', $p_venta);
        $stmt->bindParam(':vence', $vence);
        $stmt->bindParam(':prov', $prov);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->bindParam(':imagen', $imagen);

        return $stmt->execute();
    }

    // 7. ELIMINAR (Soft Delete)
    public function eliminar($id) {
        $query = "UPDATE " . $this->table . " SET estado = 0 WHERE id_producto = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>