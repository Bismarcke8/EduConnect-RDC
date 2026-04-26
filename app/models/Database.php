<?php

namespace App\Models;

class Database
{
    private $host;
    private $db;
    private $user;
    private $pass;
    private $connection;

    public function __construct()
    {
        $this->host = DB_HOST;
        $this->db = DB_NAME;
        $this->user = DB_USER;
        $this->pass = DB_PASS;
    }

    public function connect()
    {
        // Database connection logic
    }
}
