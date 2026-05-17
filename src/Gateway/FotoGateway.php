<?php

require_once __DIR__ . '/../Database.php';

class FotoGateway {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function create($data) {
        $sql = "INSERT INTO Foto (URL_foto, Is_principal, fk_Animal_Id_animal) VALUES (?, ?, ?)";
        $this->db->query($sql, [
            $data['url_foto'],
            $data['is_principal'] ? 1 : 0,
            $data['fk_animal']
        ]);
        return $this->db->lastInsertId();
    }

    public function getByAnimal($animalId) {
        $sql = "SELECT * FROM Foto WHERE fk_Animal_Id_animal = ?";
        return $this->db->fetchAll($sql, [$animalId]);
    }
}
