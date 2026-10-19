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

    public function create() {
        require_once __DIR__ . "/../views/clientes/crear.php";
    }

    public function store() {
        $nombre = $_POST["nombre"];
        $telefono = $_POST["telefono"];
        $email = $_POST["email"];

        $this->cliente->crear($nombre, $telefono, $email);

        header("Location: ?controller=clientes&action=index");
    }

    public function edit() {
        $id = $_GET["id"];
        $cliente = $this->cliente->obtenerPorId($id);
        require_once __DIR__ . "/../views/clientes/editar.php";
    }

    public function update() {
        $id = $_POST["id"];
        $nombre = $_POST["nombre"];
        $telefono = $_POST["telefono"];
        $email = $_POST["email"];

        $this->cliente->actualizar($id, $nombre, $telefono, $email);

        header("Location: ?controller=clientes&action=index");
    }

    public function delete() {
        $id = $_GET["id"];
        $this->cliente->eliminar($id);

        header("Location: ?controller=clientes&action=index");
    }
}
