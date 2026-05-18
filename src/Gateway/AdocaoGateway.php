<?php

require_once __DIR__ . '/../Database.php';

class AdocaoGateway {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getByAdotanteAndAnimal($adotanteId, $animalId) {
        $sql = "SELECT * FROM Adocao
                WHERE fk_Adotante_Id_adotante = ?
                  AND fk_Animal_Id_animal = ?
                  AND Status IN ('Pendente', 'Aprovada')
                LIMIT 1";
        return $this->db->fetch($sql, [$adotanteId, $animalId]);
    }

    public function getPendingWithDetails() {
        $sql = "SELECT ad.*, a.Nome as animal_nome, a.Especie, a.Raca, a.Status as animal_status,
                       adot.Nome as adotante_nome, adot.Email as adotante_email,
                       adot.Telefone as adotante_telefone, adot.Endereco as adotante_endereco
                FROM Adocao ad
                INNER JOIN Animal a ON ad.fk_Animal_Id_animal = a.Id_animal
                INNER JOIN Adotante adot ON ad.fk_Adotante_Id_adotante = adot.Id_adotante
                WHERE ad.Status = 'Pendente'
                ORDER BY ad.Data_abertura ASC, ad.Id_adocao ASC";
        return $this->db->fetchAll($sql);
    }

    public function getByAdotanteWithDetails($adotanteId, $status = "") {
        $sql = "SELECT ad.*, a.Nome as animal_nome, a.Especie, a.Raca,
                       a.Status as animal_status, f.Id_foto as foto_principal_id,
                       f.URL_foto as foto_principal
                FROM Adocao ad
                INNER JOIN Animal a ON ad.fk_Animal_Id_animal = a.Id_animal
                LEFT JOIN Foto f ON a.Id_animal = f.fk_Animal_Id_animal AND f.Is_principal = 1
                WHERE ad.fk_Adotante_Id_adotante = ?";
        $params = [$adotanteId];

        if (in_array($status, ["Pendente", "Aprovada", "Cancelada"])) {
            $sql .= " AND ad.Status = ?";
            $params[] = $status;
        }

        $sql .= "
                ORDER BY ad.Data_abertura DESC, ad.Id_adocao DESC";
        return $this->db->fetchAll($sql, $params);
    }

    public function createRequest($adotanteId, $animalId, $descricao = "") {
        $pdo = $this->db->getConnection();

        try {
            $pdo->beginTransaction();

            $animal = $this->db->fetch(
                "SELECT * FROM Animal WHERE Id_animal = ? FOR UPDATE",
                [$animalId]
            );

            if (!$animal) {
                throw new RuntimeException("Pet nao encontrado.");
            }

            if (!in_array($animal["Status"], ["Disponivel", "Em_adocao"])) {
                throw new RuntimeException("Este pet nao esta disponivel para adocao.");
            }

            if ($this->getByAdotanteAndAnimal($adotanteId, $animalId)) {
                throw new RuntimeException("Voce ja solicitou a adocao deste pet.");
            }

            $funcionario = $this->db->fetch(
                "SELECT Id_funcionario FROM Funcionario ORDER BY Id_funcionario LIMIT 1"
            );

            if (!$funcionario) {
                throw new RuntimeException("Nao ha funcionario cadastrado para acompanhar a adocao.");
            }

            $this->db->query(
                "INSERT INTO Adocao (Status, Data_abertura, Descricao, fk_Adotante_Id_adotante, fk_Funcionario_Id_funcionario, fk_Animal_Id_animal)
                 VALUES ('Pendente', CURDATE(), ?, ?, ?, ?)",
                [
                    $descricao,
                    $adotanteId,
                    $funcionario["Id_funcionario"],
                    $animalId,
                ]
            );

            $this->db->query(
                "UPDATE Animal SET Status = 'Em_adocao' WHERE Id_animal = ?",
                [$animalId]
            );

            $pdo->commit();
            return $this->db->lastInsertId();
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            throw $e;
        }
    }

    public function approve($adocaoId, $funcionarioId, $descricao, $mensagemReprovados) {
        $pdo = $this->db->getConnection();

        try {
            $pdo->beginTransaction();

            $adocao = $this->db->fetch(
                "SELECT * FROM Adocao WHERE Id_adocao = ? AND Status = 'Pendente' FOR UPDATE",
                [$adocaoId]
            );

            if (!$adocao) {
                throw new RuntimeException("Solicitacao de adocao nao encontrada.");
            }

            $this->db->query(
                "UPDATE Adocao
                 SET Status = 'Aprovada', Descricao = ?, Data_conclusao = CURDATE(),
                     fk_Funcionario_Id_funcionario = ?
                 WHERE Id_adocao = ?",
                [$descricao, $funcionarioId, $adocaoId]
            );

            $this->db->query(
                "UPDATE Adocao
                 SET Status = 'Cancelada', Descricao = ?, Data_conclusao = CURDATE(),
                     fk_Funcionario_Id_funcionario = ?
                 WHERE fk_Animal_Id_animal = ?
                   AND Id_adocao <> ?
                   AND Status = 'Pendente'",
                [$mensagemReprovados, $funcionarioId, $adocao["fk_Animal_Id_animal"], $adocaoId]
            );

            $this->db->query(
                "UPDATE Animal
                 SET Status = 'Adotado', Data_saida = CURDATE()
                 WHERE Id_animal = ?",
                [$adocao["fk_Animal_Id_animal"]]
            );

            $pdo->commit();
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            throw $e;
        }
    }

    public function cancel($adocaoId, $funcionarioId, $descricao) {
        $pdo = $this->db->getConnection();

        try {
            $pdo->beginTransaction();

            $adocao = $this->db->fetch(
                "SELECT * FROM Adocao WHERE Id_adocao = ? AND Status = 'Pendente' FOR UPDATE",
                [$adocaoId]
            );

            if (!$adocao) {
                throw new RuntimeException("Solicitacao de adocao nao encontrada.");
            }

            $this->db->query(
                "UPDATE Adocao
                 SET Status = 'Cancelada', Descricao = ?, Data_conclusao = CURDATE(),
                     fk_Funcionario_Id_funcionario = ?
                 WHERE Id_adocao = ?",
                [$descricao, $funcionarioId, $adocaoId]
            );

            $ativas = $this->db->fetch(
                "SELECT COUNT(*) as total
                 FROM Adocao
                 WHERE fk_Animal_Id_animal = ?
                   AND Status IN ('Pendente', 'Aprovada')",
                [$adocao["fk_Animal_Id_animal"]]
            );

            if ((int) $ativas["total"] === 0) {
                $this->db->query(
                    "UPDATE Animal SET Status = 'Disponivel', Data_saida = NULL WHERE Id_animal = ?",
                    [$adocao["fk_Animal_Id_animal"]]
                );
            }

            $pdo->commit();
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            throw $e;
        }
    }
}
