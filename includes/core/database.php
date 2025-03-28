<?php
// includes/core/database.php

class Database {
    private $host = 'localhost';
    private $dbname = 'leotool';
    private $username = 'markleo';
    private $password = '123456';
    private $conn;

    public function connect() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log(
                date('Y-m-d H:i:s') . " - Database Connection Error: " . $e->getMessage() . "\n",
                3,
                __DIR__ . '/../../storage/logs/error.log'
            );
            die("Kết nối thất bại: " . $e->getMessage());
        }
        return $this->conn;
    }
}