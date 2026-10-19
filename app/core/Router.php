<?php

class Router {

    public static function run() {

        // --- LÓGICA DE CONTROLADOR POR DEFECTO ---
        if (isset($_GET["controller"])) {
            // 1. Si el usuario pide algo específico, se lo damos
            $controller = $_GET["controller"];
        } else {
            // 2. Si entra a la raíz (/), mostramos la LANDING PAGE (Home)
            // Ya NO verificamos sesión aquí, porque el Home es público.
            $controller = "home"; 
        }

        $action = $_GET["action"] ?? "index";

        $controllerName = ucfirst($controller) . "Controller";
        $controllerFile = __DIR__ . "/../controllers/" . $controllerName . ".php";

        if (!file_exists($controllerFile)) {
            die("Error 404: El controlador '$controllerName' no existe.");
        }

        require_once $controllerFile;

        if (!class_exists($controllerName)) {
            die("Error Interno: La clase '$controllerName' no está definida.");
        }

        $obj = new $controllerName();

        if (!method_exists($obj, $action)) {
            die("Error 404: La acción '$action' no existe.");
        }

        // --- PROTECCIÓN DE RUTAS PRIVADAS (Middleware simple) ---
        // Si intentan entrar a dashboard, producto, venta, etc., y NO hay sesión -> Al Login
        $public_controllers = ['home', 'auth', 'catalogo']; // Lista blanca de controladores públicos
        
        if (!in_array($controller, $public_controllers)) {
            if (session_status() === PHP_SESSION_NONE) session_start();
            
            if (!isset($_SESSION['user_id'])) {
                // Si intenta entrar a algo privado sin login, lo mandamos al auth
                header("Location: index.php?controller=auth&action=index");
                exit();
            }
        }

        $obj->$action();
    }
}