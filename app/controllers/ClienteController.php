<?php
require_once __DIR__ . "/../models/Cliente.php";

class ClienteController {

    private $cliente;

    public function __construct() {
        $this->cliente = new Cliente();
    }

    public function index() {
        $clientes = $this->cliente->obtenerTodos();
        require_once __DIR__ . "/../views/clientes/index.php";
    }

    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = $this->leerFormulario();
            if ($datos['nombre'] === '') {
                $this->redirigir('error');
            }
            $ok = $this->cliente->crear($datos['documento'], $datos['nombre'], $datos['telefono'], $datos['correo'], $datos['direccion']);
            $this->redirigir($ok ? 'creado' : 'error');
        }
        $this->redirigir();
    }

    public function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = filter_var($_POST['id_cliente'] ?? null, FILTER_VALIDATE_INT);
            $datos = $this->leerFormulario();
            if ($id === false || $datos['nombre'] === '') {
                $this->redirigir('error');
            }
            $ok = $this->cliente->actualizar($id, $datos['documento'], $datos['nombre'], $datos['telefono'], $datos['correo'], $datos['direccion']);
            $this->redirigir($ok ? 'actualizado' : 'error');
        }
        $this->redirigir();
    }

    public function eliminar() {
        $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
        if ($id === false) {
            $this->redirigir('error');
        }
        if ($id === Cliente::ID_PUBLICO_GENERAL) {
            $this->redirigir('protegido');
        }
        $ok = $this->cliente->eliminar($id);
        $this->redirigir($ok ? 'eliminado' : 'error');
    }

    private function leerFormulario() {
        $correo = trim($_POST['correo'] ?? '');
        return [
            'documento' => trim($_POST['documento'] ?? '') ?: null,
            'nombre'    => trim($_POST['nombre'] ?? ''),
            'telefono'  => trim($_POST['telefono'] ?? '') ?: null,
            'correo'    => filter_var($correo, FILTER_VALIDATE_EMAIL) ? $correo : null,
            'direccion' => trim($_POST['direccion'] ?? '') ?: null,
        ];
    }

    private function redirigir($msg = null) {
        $url = "index.php?controller=cliente&action=index";
        if ($msg) {
            $url .= "&msg=" . $msg;
        }
        header("Location: " . $url);
        exit;
    }
}
