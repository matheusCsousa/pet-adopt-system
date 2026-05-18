<?php

require_once __DIR__ . '/../Database.php';

class AnimalGateway {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll() {
        $sql = "SELECT a.*, f.Id_foto as foto_principal_id, f.URL_foto as foto_principal,
                       ab.Nome as abrigo_nome
                FROM Animal a
                LEFT JOIN Foto f ON a.Id_animal = f.fk_Animal_Id_animal AND f.Is_principal = 1
                LEFT JOIN Abrigo ab ON a.fk_Abrigo_Id_abrigo = ab.Id_abrigo
                ORDER BY a.Data_entrada DESC, a.Id_animal DESC";
        return $this->db->fetchAll($sql);
    }

    public function getAvailableForAdoption() {
        $sql = "SELECT a.*, f.Id_foto as foto_principal_id, f.URL_foto as foto_principal
                FROM Animal a
                LEFT JOIN Foto f ON a.Id_animal = f.fk_Animal_Id_animal AND f.Is_principal = 1
                WHERE a.Status IN ('Disponivel', 'Em_adocao')
                ORDER BY a.Data_entrada DESC, a.Id_animal DESC";
        return $this->db->fetchAll($sql);
    }

    public function getNotAnnounced() {
        $sql = "SELECT a.*, f.Id_foto as foto_principal_id, f.URL_foto as foto_principal,
                       ab.Nome as abrigo_nome
                FROM Animal a
                LEFT JOIN Foto f ON a.Id_animal = f.fk_Animal_Id_animal AND f.Is_principal = 1
                LEFT JOIN Abrigo ab ON a.fk_Abrigo_Id_abrigo = ab.Id_abrigo
                WHERE a.Status IN ('Cadastrado', 'Em_tratamento')
                ORDER BY a.Data_entrada DESC, a.Id_animal DESC";
        return $this->db->fetchAll($sql);
    }

    public function getById($id) {
        $sql = "SELECT * FROM Animal WHERE Id_animal = ?";
        return $this->db->fetch($sql, [$id]);
    }

    public function getDetailsById($id) {
        $sql = "SELECT a.*, f.Id_foto as foto_principal_id, f.URL_foto as foto_principal, ab.Nome as abrigo_nome,
                       ab.Endereco as abrigo_endereco, ab.Telefone as abrigo_telefone
                FROM Animal a
                LEFT JOIN Foto f ON a.Id_animal = f.fk_Animal_Id_animal AND f.Is_principal = 1
                LEFT JOIN Abrigo ab ON a.fk_Abrigo_Id_abrigo = ab.Id_abrigo
                WHERE a.Id_animal = ?";
        return $this->db->fetch($sql, [$id]);
    }

    public function getVacinasByAnimal($id) {
        $sql = "SELECT v.Nome, ap.Data_aplicacao, ap.Proxima_dose
                FROM Aplica ap
                INNER JOIN Vacina v ON ap.fk_Vacina_Id_vacina = v.Id_vacina
                WHERE ap.fk_Animal_Id_animal = ?
                ORDER BY ap.Data_aplicacao DESC, v.Nome ASC";
        return $this->db->fetchAll($sql, [$id]);
    }

    public function create($data) {
        $sql = "INSERT INTO Animal (Nome, Raca, Especie, Sexo, Porte, Status, Data_entrada, fk_Abrigo_Id_abrigo)
                VALUES (:nome, :raca, :especie, :sexo, :porte, :status, :data_entrada, :fk_abrigo)";

        $this->db->query($sql, [
            'nome' => $data['nome'],
            'raca' => $data['raca'] ?? null,
            'especie' => $data['especie'],
            'sexo' => $data['sexo'],
            'porte' => $data['porte'],
            'status' => $data['status'] ?? 'Cadastrado',
            'data_entrada' => $data['data_entrada'] ?? date('Y-m-d'),
            'fk_abrigo' => $data['fk_abrigo']
        ]);

        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $fields = [];
        $params = ['id' => $id];

        foreach ($data as $key => $value) {
            $fields[] = "$key = :$key";
            $params[$key] = $value;
        }

        $sql = "UPDATE Animal SET " . implode(', ', $fields) . " WHERE Id_animal = :id";
        return $this->db->query($sql, $params);
    }

    public function announce($id) {
        $sql = "UPDATE Animal
                SET Status = 'Disponivel'
                WHERE Id_animal = ?
                  AND Status IN ('Cadastrado', 'Em_tratamento')";
        return $this->db->query($sql, [$id]);
    }

    public function removeAnnouncement($id) {
        $this->db->query(
            "UPDATE Adocao
             SET Status = 'Cancelada',
                 Descricao = 'O anuncio deste pet foi removido pela ONG.',
                 Data_conclusao = CURDATE()
             WHERE fk_Animal_Id_animal = ?
               AND Status = 'Pendente'",
            [$id]
        );

        $sql = "UPDATE Animal
                SET Status = 'Cadastrado'
                WHERE Id_animal = ?
                  AND Status IN ('Disponivel', 'Em_adocao')";
        return $this->db->query($sql, [$id]);
    }

    public function delete($id) {
        $sql = "DELETE FROM Animal WHERE Id_animal = ?";
        return $this->db->query($sql, [$id]);
    }
}
