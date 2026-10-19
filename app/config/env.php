<?php
function loadEnv($path) {
    if (!is_readable($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#' || strpos($line, '=') === false) {
            continue;
        }

        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);

        if (strlen($value) >= 2 && ($value[0] === '"' || $value[0] === "'") && substr($value, -1) === $value[0]) {
            $value = substr($value, 1, -1);
        }

        // Las variables definidas en el servidor tienen prioridad sobre el archivo .env
        if (getenv($key) === false && !isset($_ENV[$key])) {
            $_ENV[$key] = $value;
        }
    }
}

function env($key, $default = null) {
    if (isset($_ENV[$key])) {
        return $_ENV[$key];
    }
    $value = getenv($key);
    return $value !== false ? $value : $default;
}
