<?php

namespace Core;

use PDO;
use PDOException;

class Database
{
    private static $instance = null;
    private $pdo;

    private function __construct()
    {
        try {
            $this->pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $e) {
            $message = APP_DEBUG ? ("Database connection failed: " . $e->getMessage()) : "Database connection failed.";
            die($message);
        }
    }

    /**
     * Get singleton instance
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get PDO instance
     */
    public function getConnection()
    {
        return $this->pdo;
    }

    /**
     * Get database server version
     */
    public function getServerVersion()
    {
        return (string) $this->pdo->getAttribute(PDO::ATTR_SERVER_VERSION);
    }

    /**
     * Execute a query
     */
    public function query($sql, $params = [])
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            $message = APP_DEBUG ? ("Query error: " . $e->getMessage()) : "A database error occurred.";
            die($message);
        }
    }

    /**
     * Fetch all results
     */
    public function fetchAll($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }

    /**
     * Fetch single result
     */
    public function fetch($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }

    /**
     * Insert data and return last insert ID
     */
    public function insert($table, $data)
    {
        $columns = implode(',', array_keys($data));
        $values = implode(',', array_fill(0, count($data), '?'));
        
        $sql = "INSERT INTO $table ($columns) VALUES ($values)";
        
        $this->query($sql, array_values($data));
        return $this->pdo->lastInsertId();
    }

    /**
     * Update data
     */
    public function update($table, $data, $where)
    {
        $set = implode(' = ?, ', array_keys($data)) . ' = ?';
        $sql = "UPDATE $table SET $set WHERE ";
        
        $params = array_values($data);
        
        if (is_array($where)) {
            $whereClause = implode(' AND ', array_map(function($key) {
                return "$key = ?";
            }, array_keys($where)));
            $params = array_merge($params, array_values($where));
        } else {
            $whereClause = $where;
        }
        
        $sql .= $whereClause;
        
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }

    /**
     * Delete data
     */
    public function delete($table, $where)
    {
        $sql = "DELETE FROM $table WHERE ";
        
        if (is_array($where)) {
            $whereClause = implode(' AND ', array_map(function($key) {
                return "$key = ?";
            }, array_keys($where)));
            $params = array_values($where);
        } else {
            $whereClause = $where;
            $params = [];
        }
        
        $sql .= $whereClause;
        
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }

    /**
     * Count records
     */
    public function count($table, $where = [])
    {
        $sql = "SELECT COUNT(*) as count FROM $table";
        
        if (!empty($where)) {
            $whereClause = implode(' AND ', array_map(function($key) {
                return "$key = ?";
            }, array_keys($where)));
            $sql .= " WHERE " . $whereClause;
            $result = $this->fetch($sql, array_values($where));
        } else {
            $result = $this->fetch($sql);
        }
        
        return $result['count'] ?? 0;
    }

    /**
     * Start transaction
     */
    public function beginTransaction()
    {
        return $this->pdo->beginTransaction();
    }

    /**
     * Commit transaction
     */
    public function commit()
    {
        return $this->pdo->commit();
    }

    /**
     * Rollback transaction
     */
    public function rollback()
    {
        return $this->pdo->rollBack();
    }
}
