<?php
/**
 * Database connection class
 * @package App\Config
 */
class Database {
    private $host = "localhost";
    private $db_name = "gallon_inventory_db";
    private $username = "root";
    private $password = "";
    public $conn;

    /**
     * Get database connection
     * @return mysqli
     */
    public function getConnection() {
        $this->conn = null;
        
        try {
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->db_name);
            
            if ($this->conn->connect_error) {
                die("Connection failed: " . $this->conn->connect_error);
            }
            
            $this->conn->set_charset("utf8mb4");
        } catch (Exception $e) {
            echo "Connection error: " . $e->getMessage();
        }
        
        return $this->conn;
    }
}
?>
