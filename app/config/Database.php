<?php
require_once __DIR__ . '/env.php';
loadEnv(__DIR__ . '/../../.env');

class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    public $conn;

    public function __construct() {
        $this->host = env('DB_HOST', '127.0.0.1');
        $this->db_name = env('DB_NAME', 'FarmaSistema');
        $this->username = env('DB_USER', '');
        $this->password = env('DB_PASS', '');
    }

    public function conectar() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );

            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (PDOException $exception) {
            echo "Error de conexión: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
