<?php

// 1. Iniciar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Mostrar errores (Desarrollo)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// === AQUÍ ESTÁ LA SOLUCIÓN ===
// 3. Cargar Helpers (Funciones globales como money())
require_once __DIR__ . "/../app/helpers/functions.php"; 

// 4. Cargar Router
require_once __DIR__ . "/../app/core/Router.php";

// 5. Ejecutar
Router::run();