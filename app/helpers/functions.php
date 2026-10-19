<?php
// app/helpers/functions.php

// Función para formatear dinero
function money($amount) {
    return 'Bs. ' . number_format((float)$amount, 2, '.', ',');
}

// Función para mostrar estado visualmente
function badge($estado) {
    if ($estado == 1) return '<span class="bg-emerald-100 text-emerald-700 px-2 py-1 rounded-full text-xs font-bold">Activo</span>';
    return '<span class="bg-red-100 text-red-700 px-2 py-1 rounded-full text-xs font-bold">Inactivo</span>';
}

// Función para verificar si el usuario es administrador
function isAdmin() {
    // Asegurarse de que la sesión esté iniciada
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Verificamos si existe la variable de sesión 'user_rol' y si es 'admin'
    // Asegúrate de que en tu base de datos el rol sea exactamente 'admin' (en minúsculas)
    return isset($_SESSION['user_rol']) && $_SESSION['user_rol'] === 'admin';
}
?>