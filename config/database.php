<?php
class Database {
    private $host = "localhost";
    private $db_name = "gestion_employes";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, 
                                  $this->username, 
                                  $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}

// Start session and include auth
require_once 'auth.php';

// Create database connection
$database = new Database();
$db = $database->getConnection();

// Create auth object
$auth = new Auth($db);
?>