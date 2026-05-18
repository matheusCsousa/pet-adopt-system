<?php

require_once __DIR__ . '/../Database.php';

class VacinaGateway {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll() {
        $sql = "SELECT * FROM Vacina ORDER BY Nome";
        return $this->db->fetchAll($sql);
    }

    public function applyToAnimal($animalId, $vacinaId, $dataAplicacao, $proximaDose = null) {
        $sql = "INSERT IGNORE INTO Aplica (fk_Animal_Id_animal, fk_Vacina_Id_vacina, Data_aplicacao, Proxima_dose)
                VALUES (?, ?, ?, ?)";
        return $this->db->query($sql, [
            $animalId,
            $vacinaId,
            $dataAplicacao,
            $proximaDose ?: null
        ]);
    }

    public function applyManyToAnimal($animalId, $vacinas, $datasAplicacao, $proximasDoses) {
        foreach ($vacinas as $indice => $vacinaId) {
            $vacinaId = (int) $vacinaId;
            $dataAplicacao = trim($datasAplicacao[$indice] ?? "");

            if ($vacinaId <= 0 || $dataAplicacao === "") {
                continue;
            }

            $this->applyToAnimal(
                $animalId,
                $vacinaId,
                $dataAplicacao,
                trim($proximasDoses[$indice] ?? "")
            );
        }
    }
}
