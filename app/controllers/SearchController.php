<?php

namespace App\Controllers;

use Core\Controller;
use Core\Security;
use App\Models\User;

class SearchController extends Controller
{
    /**
     * Show search page
     */
    public function index()
    {
        $this->view('search/index', [
            'csrf_token' => $_SESSION['csrf_token']
        ]);
    }

    /**
     * Handle search
     */
    public function search()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Method not allowed');
        }

        $query = Security::sanitize($_POST['query'] ?? '');
        $field = $_POST['field'] ?? 'name';
        $university = Security::sanitize($_POST['university'] ?? '');

        if (empty($query) && empty($university)) {
            $_SESSION['error'] = 'Please enter a search term';
            $this->redirect('/search');
        }

        $results = [];

        if (!empty($query)) {
            $sql = "SELECT * FROM users WHERE (
                CONCAT(first_name, ' ', last_name) LIKE ? OR
                email LIKE ? OR
                university LIKE ?
            ) AND is_active = 1
            LIMIT 50";

            $searchTerm = '%' . $query . '%';
            $results = $this->db->fetchAll($sql, [$searchTerm, $searchTerm, $searchTerm]);
        }

        if (!empty($university)) {
            $sql = "SELECT * FROM users WHERE university = ? AND is_active = 1 LIMIT 50";
            $results = $this->db->fetchAll($sql, [$university]);
        }

        $this->view('search/results', [
            'results' => $results,
            'query' => $query,
            'count' => count($results)
        ]);
    }
}
