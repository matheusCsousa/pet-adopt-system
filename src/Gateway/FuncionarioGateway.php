<?php

require_once __DIR__ . '/../Database.php';

class FuncionarioGateway {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getByEmail($email) {
        $sql = "SELECT * FROM Funcionario WHERE Email = ?";
        return $this->db->fetch($sql, [$email]);
    }

    public function getByEmailAndSenha($email, $senha) {
        $sql = "SELECT * FROM Funcionario WHERE Email = ? AND Senha = PASSWORD(?)";
        return $this->db->fetch($sql, [$email, $senha]);
    }

    public function getById($id) {
        $sql = "SELECT * FROM Funcionario WHERE Id_funcionario = ?";
        return $this->db->fetch($sql, [$id]);
    }

    public function create($data) {
        $sql = "INSERT INTO Funcionario (Nome, Cargo, Email, Senha) VALUES (?, ?, ?, PASSWORD(?))";
        $this->db->query($sql, [
            $data['nome'],
            $data['cargo'],
            $data['email'],
            $data['senha']
        ]);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $sql = "UPDATE Funcionario
                SET Nome = ?, Cargo = ?, Email = ?
                WHERE Id_funcionario = ?";
        return $this->db->query($sql, [
            $data['nome'],
            $data['cargo'],
            $data['email'],
            $id
        ]);
    }

    public function updateSenha($id, $senha) {
        $sql = "UPDATE Funcionario SET Senha = PASSWORD(?) WHERE Id_funcionario = ?";
        return $this->db->query($sql, [$senha, $id]);
    }
}
