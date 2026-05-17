<?php

require_once __DIR__ . '/../Database.php';

class AdotanteGateway {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getByEmail($email) {
        $sql = "SELECT * FROM Adotante WHERE Email = ?";
        return $this->db->fetch($sql, [$email]);
    }

    public function getByEmailAndSenha($email, $senha) {
        $sql = "SELECT * FROM Adotante WHERE Email = ? AND Senha = PASSWORD(?)";
        return $this->db->fetch($sql, [$email, $senha]);
    }

    public function getById($id) {
        $sql = "SELECT * FROM Adotante WHERE Id_adotante = ?";
        return $this->db->fetch($sql, [$id]);
    }

    public function create($data) {
        $sql = "INSERT INTO Adotante (Nome, CPF, Telefone, Email, Senha, Endereco) VALUES (?, ?, ?, ?, PASSWORD(?), ?)";
        $this->db->query($sql, [
            $data['nome'],
            $data['cpf'],
            $data['telefone'],
            $data['email'],
            $data['senha'],
            $data['endereco']
        ]);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $sql = "UPDATE Adotante
                SET Nome = ?, CPF = ?, Telefone = ?, Email = ?, Endereco = ?
                WHERE Id_adotante = ?";
        return $this->db->query($sql, [
            $data['nome'],
            $data['cpf'],
            $data['telefone'],
            $data['email'],
            $data['endereco'],
            $id
        ]);
    }

    public function updateSenha($id, $senha) {
        $sql = "UPDATE Adotante SET Senha = PASSWORD(?) WHERE Id_adotante = ?";
        return $this->db->query($sql, [$senha, $id]);
    }
}
