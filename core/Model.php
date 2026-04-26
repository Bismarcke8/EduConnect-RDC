<?php

namespace Core;

use Core\Database;

class Model
{
    protected $table;
    protected $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Get all records
     */
    public function all()
    {
        $sql = "SELECT * FROM " . $this->table;
        return $this->db->fetchAll($sql);
    }

    /**
     * Find record by ID
     */
    public function find($id)
    {
        $sql = "SELECT * FROM " . $this->table . " WHERE id = ?";
        return $this->db->fetch($sql, [$id]);
    }

    /**
     * Find by condition
     */
    public function findWhere($conditions)
    {
        $whereClause = implode(' AND ', array_map(function($key) {
            return "$key = ?";
        }, array_keys($conditions)));

        $sql = "SELECT * FROM " . $this->table . " WHERE " . $whereClause;
        return $this->db->fetch($sql, array_values($conditions));
    }

    /**
     * Find all by condition
     */
    public function whereAll($conditions, $limit = null, $offset = null)
    {
        $whereClause = implode(' AND ', array_map(function($key) {
            return "$key = ?";
        }, array_keys($conditions)));

        $sql = "SELECT * FROM " . $this->table . " WHERE " . $whereClause;
        
        if ($limit) {
            $sql .= " LIMIT " . intval($limit);
            if ($offset) {
                $sql .= " OFFSET " . intval($offset);
            }
        }

        return $this->db->fetchAll($sql, array_values($conditions));
    }

    /**
     * Create new record
     */
    public function create($data)
    {
        return $this->db->insert($this->table, $data);
    }

    /**
     * Update record
     */
    public function update($id, $data)
    {
        return $this->db->update($this->table, $data, ['id' => $id]);
    }

    /**
     * Delete record
     */
    public function delete($id)
    {
        return $this->db->delete($this->table, ['id' => $id]);
    }

    /**
     * Count records
     */
    public function count($where = [])
    {
        return $this->db->count($this->table, $where);
    }

    /**
     * Raw query
     */
    public function query($sql, $params = [])
    {
        return $this->db->query($sql, $params);
    }
}
