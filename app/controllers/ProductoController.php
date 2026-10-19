<?php
require_once __DIR__ . "/../models/Producto.php";
require_once __DIR__ . "/../models/Categoria.php";
require_once __DIR__ . "/../models/Proveedor.php";

class ProductoController
{

    /* ======================================
       LISTAR (Funciona para TODO o solo MEDICAMENTOS)
    ======================================= */
    public function index()
    {
        $productoModel = new Producto();

        // Verificamos si queremos ver solo Medicamentos o todo
        $tipo = $_GET['tipo'] ?? null;

        if ($tipo) {
            // Si hay tipo (Ej: MEDICAMENTO), filtramos
            $productos = $productoModel->obtenerPorTipo($tipo);
            $titulo = "Gestión de " . ucfirst(strtolower($tipo)) . "s";
        } else {
            // Si no, traemos todo
            $productos = $productoModel->obtenerTodos();
            $titulo = "Inventario Global";
        }

        require_once __DIR__ . "/../views/productos/index.php";
    }

    /* ======================================
       FORMULARIO CREAR
    ======================================= */
    public function crear()
    {
        $categoriaModel = new Categoria();
        $proveedorModel = new Proveedor();

        $categorias = $categoriaModel->obtenerTodos();
        $proveedores = $proveedorModel->obtenerTodos();

        // Para pre-seleccionar el tipo en el formulario si venimos de la sección medicamentos
        $tipoPreseleccionado = $_GET['tipo'] ?? 'GENERAL';

        require_once __DIR__ . "/../views/productos/crear.php";
    }

    /* ======================================
       GUARDAR (Con Validaciones de Seguridad)
    ======================================= */
    public function guardar()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $codigo = trim($_POST["codigo"]);
            $nombre = trim($_POST["nombre"]);
            $descripcion = trim($_POST["descripcion"]);
            $errores = [];
            if (empty($nombre)) {
                $errores[] = "El nombre es obligatorio.";
            } elseif (is_numeric($nombre)) {
                $errores[] = "El nombre no puede ser solo números.";
            } elseif (strlen($nombre) < 3) {
                $errores[] = "El nombre es muy corto (mínimo 3 letras).";
            } elseif (!preg_match('/[a-zA-ZñÑáéíóúÁÉÍÓÚ]/', $nombre)) {
                $errores[] = "El nombre debe contener letras.";
            }
            $pCompra = (float) $_POST["precio_compra"];
            $pVenta = (float) $_POST["precio_venta"];
            if ($pCompra < 0 || $pVenta < 0) {
                $errores[] = "Los precios no pueden ser negativos.";
            }
            if ($pVenta < $pCompra) {
                $errores[] = "El precio de venta no puede ser menor al costo.";
            }

            // C) Validar Fechas (Solo si es medicamento)
            if ($_POST["tipo"] == 'MEDICAMENTO') {
                if (empty($_POST["fecha_vencimiento"])) {
                    $errores[] = "La fecha de vencimiento es obligatoria para medicamentos.";
                } else {
                    $fechaVence = new DateTime($_POST["fecha_vencimiento"]);
                    $hoy = new DateTime();
                    if ($fechaVence < $hoy) {
                        // Opcional: Permitir guardar vencidos pero con advertencia, o bloquearlo:
                        // $errores[] = "El producto ya está vencido."; 
                    }
                }
            }

            // --- 3. SI HAY ERRORES, DETENER TODO ---
            if (!empty($errores)) {
                // Convertimos el array de errores a un string para enviarlo por URL
                $mensajeError = implode(". ", $errores);
                // Redirigimos atrás con el mensaje de error
                header("Location: index.php?controller=producto&action=crear&status=error_validacion&msg=" . urlencode($mensajeError));
                exit;
            }

            // --- 4. SI PASA LAS VALIDACIONES, PROCESAMOS LA IMAGEN ---
            $nombreImagen = 'default_product.png';

            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
                $nombreImagen = uniqid() . '.' . $ext;
                $rutaDestino = __DIR__ . '/../../public/img/uploads/productos/' . $nombreImagen;
                move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino);
            }

            // --- 5. GUARDAR EN BD ---
            $producto = new Producto();
            $producto->crear(
                $codigo,
                $nombre, // Usamos la variable limpia
                $descripcion,
                $_POST["categoria_id"],
                $_POST["stock"],
                $pCompra,
                $pVenta,
                $_POST["fecha_vencimiento"] ?? null,
                $_POST["proveedor_id"],
                $_POST["tipo"],
                $nombreImagen
            );
        }

        // Redirigir éxito
        $redirectTipo = ($_POST["tipo"] == 'MEDICAMENTO') ? '&tipo=MEDICAMENTO' : '';
        header("Location: index.php?controller=producto&action=index" . $redirectTipo . "&status=success");
        exit;
    }

    /* ======================================
       FORMULARIO EDITAR
    ======================================= */
    public function editar()
    {
        if (!isset($_GET["id"])) {
            header("Location: index.php?controller=producto&action=index");
            exit;
        }

        $id = $_GET["id"];
        $productoModel = new Producto();
        $categoriaModel = new Categoria();
        $proveedorModel = new Proveedor();

        $producto = $productoModel->obtenerPorId($id);
        $categorias = $categoriaModel->obtenerTodos();
        $proveedores = $proveedorModel->obtenerTodos();

        require_once __DIR__ . "/../views/productos/editar.php";
    }

    /* ======================================
       ACTUALIZAR
    ======================================= */
    public function actualizar()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $productoModel = new Producto();

            // Recuperamos la imagen actual por si no suben una nueva
            $productoActual = $productoModel->obtenerPorId($_POST["id_producto"]);
            $nombreImagen = $productoActual['imagen'];

            // Si subieron nueva imagen, la reemplazamos
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
                $nombreImagen = uniqid() . '.' . $ext;
                $rutaDestino = __DIR__ . '/../../public/img/uploads/productos/' . $nombreImagen;
                move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino);
            }

            $productoModel->actualizar(
                $_POST["id_producto"],
                $_POST["codigo"],
                $_POST["nombre"],
                $_POST["descripcion"],
                $_POST["categoria_id"],
                $_POST["stock"],
                $_POST["precio_compra"],
                $_POST["precio_venta"],
                $_POST["fecha_vencimiento"] ?? null,
                $_POST["proveedor_id"],
                $_POST["tipo"],
                $nombreImagen
            );
        }

        header("Location: index.php?controller=producto&action=index");
        exit;
    }

    /* ======================================
       ELIMINAR
    ======================================= */
    public function eliminar()
    {

        // 1. Verificar Sesión
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (isset($_GET["id"])) {
            $id = $_GET["id"];

            $productoModel = new Producto();

            // Llamamos al modelo
            $resultado = $productoModel->eliminar($id);

            if ($resultado) {
                // Éxito
                header("Location: index.php?controller=producto&action=index&msg=eliminado");
            } else {
                // Fallo SQL
                header("Location: index.php?controller=producto&action=index&msg=error_sql");
            }
        } else {
            // No llegó el ID
            header("Location: index.php?controller=producto&action=index&msg=falta_id");
        }
        if ($productoModel->eliminar($id)) {

            require_once __DIR__ . '/../models/Bitacora.php';
            Bitacora::registrar(
                $_SESSION['user_id'],
                'ELIMINAR',
                'productos',
                "Se eliminó (soft-delete) el producto ID: $id"
            );
            // ====================

            header("Location: index.php?controller=producto&action=index");
        }
        exit;
    }
}
