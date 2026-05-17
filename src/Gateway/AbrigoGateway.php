<?php

require_once __DIR__ . '/../Database.php';

class AbrigoGateway {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll() {
        $sql = "SELECT * FROM Abrigo";
        return $this->db->fetchAll($sql);
    }

    public function getById($id) {
        $sql = "SELECT * FROM Abrigo WHERE Id_abrigo = ?";
        return $this->db->fetch($sql, [$id]);
    }
}
