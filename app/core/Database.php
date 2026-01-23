<?php

namespace App\Core;

use PDO;
use PDOException;

class Database {
    private static $instance = null;
    private $connection;

    private function connect() {
        $config = require __DIR__ . '/../config/database.php';

        $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset=utf8mb4";

        try {
            $this->connection = new PDO($dsn, $config['user'], $config['pass'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            ]);
        } catch (PDOException $e) {
            throw new \Exception("Fallo de conexión: " . $e->getMessage());
        }
    }

    private function isGoneAway(PDOException $e) {
        $message = strtolower($e->getMessage());
        return strpos($message, '2006') !== false
            || strpos($message, '2013') !== false
            || strpos($message, 'server has gone away') !== false
            || strpos($message, 'lost connection') !== false;
    }

    private function __construct() {
        $this->connect();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        try {
            if ($this->connection === null) {
                $this->connect();
                return $this->connection;
            }

            $this->connection->query('SELECT 1');
        } catch (PDOException $e) {
            if ($this->isGoneAway($e)) {
                $this->connect();
            } else {
                throw $e;
            }
        }

        return $this->connection;
    }
}