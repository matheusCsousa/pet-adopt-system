<?php
require_once "src/db.php";
require_once "src/Gateway/AnimalGateway.php";
require_once "src/Gateway/AbrigoGateway.php";
require_once "src/Gateway/VacinaGateway.php";

exigirFuncionario();

$animalGateway = new AnimalGateway();
$abrigoGateway = new AbrigoGateway();
$abrigos = $abrigoGateway->getAll();
$vacinaGateway = new VacinaGateway();
$vacinas = $vacinaGateway->getAll();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $acao = $_POST["acao"] ?? "";
    $animalId = (int) ($_POST["animal_id"] ?? 0);

    try {
        if ($acao === "editar") {
            $animalGateway->update($animalId, [
                "Nome" => trim($_POST["nome"] ?? ""),
                "Raca" => trim($_POST["raca"] ?? ""),
                "Especie" => $_POST["especie"] ?? "Outro",
                "Sexo" => $_POST["sexo"] ?? "M",
                "Porte" => $_POST["porte"] ?? "Medio",
                "Status" => $_POST["status"] ?? "Cadastrado",
                "fk_Abrigo_Id_abrigo" => (int) ($_POST["abrigo_id"] ?? 0),
            ]);

            $vacinaGateway->applyManyToAnimal(
                $animalId,
                $_POST["vacina_id"] ?? [],
                $_POST["data_aplicacao"] ?? [],
                $_POST["proxima_dose"] ?? []
            );
            $sucesso = "Pet atualizado com sucesso.";
        } elseif ($acao === "remover_anuncio") {
            $animalGateway->removeAnnouncement($animalId);
            $sucesso = "Anuncio removido. O pet voltou para o cadastro interno.";
        }
    } catch (Exception $e) {
        $erro = "Nao foi possivel executar a acao.";
    }
}

$pets = $animalGateway->getAll();
?>

<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Gerenciar pets</h2>
            <p class="text-muted mb-0">Edite dados dos pets e remova anuncios ativos.</p>
        </div>
        <span class="badge bg-light text-dark border p-2"><?= count($pets) ?> pets</span>
    </div>

    <?php if (isset($sucesso)): ?>
        <div class="alert alert-success border-0 shadow-sm" role="alert">
            <?= htmlspecialchars($sucesso) ?>
        </div>
    <?php endif; ?>

    <?php if (isset($erro)): ?>
        <div class="alert alert-danger border-0 shadow-sm" role="alert">
            <?= htmlspecialchars($erro) ?>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <?php foreach ($pets as $pet): ?>
            <?php
                $foto = !empty($pet["foto_principal_id"])
                    ? "foto.php?id=" . (int) $pet["foto_principal_id"]
                    : (!empty($pet["foto_principal"])
                        ? $pet["foto_principal"]
                        : "https://images.unsplash.com/photo-1543466835-00a7907e9de1?auto=format&fit=crop&w=500&q=80");
                $modalId = "editarPet" . (int) $pet["Id_animal"];
                $removerModalId = "removerAnuncio" . (int) $pet["Id_animal"];
                $vacinasPet = $animalGateway->getVacinasByAnimal($pet["Id_animal"]);
            ?>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 18px; overflow: hidden;">
                    <img src="<?= htmlspecialchars($foto) ?>" class="card-img-top" style="height: 220px; object-fit: cover;" alt="<?= htmlspecialchars($pet["Nome"]) ?>">
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                            <div>
                                <h5 class="fw-bold mb-1"><?= htmlspecialchars($pet["Nome"]) ?></h5>
                                <p class="text-muted small mb-0">
                                    <?= htmlspecialchars($pet["Especie"]) ?>
                                    <?= $pet["Raca"] ? " • " . htmlspecialchars($pet["Raca"]) : "" ?>
                                </p>
                            </div>
                            <span class="badge bg-light text-dark border p-2"><?= htmlspecialchars($pet["Status"]) ?></span>
                        </div>

                        <p class="text-secondary small mb-3">
                            <?= htmlspecialchars($pet["Porte"]) ?> • <?= htmlspecialchars($pet["abrigo_nome"] ?: "Abrigo nao informado") ?>
                        </p>

                        <div class="d-flex gap-2 mt-auto">
                            <button type="button" class="btn btn-dark w-50" style="border-radius: 12px;" data-bs-toggle="modal" data-bs-target="#<?= $modalId ?>">
                                Editar
                            </button>
                            <?php if (in_array($pet["Status"], ["Disponivel", "Em_adocao"])): ?>
                                <button type="button" class="btn btn-outline-danger w-50" style="border-radius: 12px;" data-bs-toggle="modal" data-bs-target="#<?= $removerModalId ?>">
                                    Remover
                                </button>
                            <?php else: ?>
                                <a href="?page=detalhes_pet&id=<?= (int) $pet["Id_animal"] ?>" class="btn btn-light border w-50" style="border-radius: 12px;">Detalhes</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="<?= $modalId ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content border-0" style="border-radius: 16px;">
                        <form method="POST">
                            <div class="modal-header">
                                <h5 class="modal-title fw-bold">Editar <?= htmlspecialchars($pet["Nome"]) ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="acao" value="editar">
                                <input type="hidden" name="animal_id" value="<?= (int) $pet["Id_animal"] ?>">

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold small text-uppercase text-secondary">Nome</label>
                                        <input type="text" name="nome" class="form-control" required value="<?= htmlspecialchars($pet["Nome"]) ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold small text-uppercase text-secondary">Raca</label>
                                        <input type="text" name="raca" class="form-control" value="<?= htmlspecialchars($pet["Raca"] ?? "") ?>">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-bold small text-uppercase text-secondary">Especie</label>
                                        <select name="especie" class="form-select">
                                            <?php foreach (["Cachorro", "Gato", "Outro"] as $especie): ?>
                                                <option value="<?= $especie ?>" <?= $pet["Especie"] === $especie ? "selected" : "" ?>><?= $especie ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-bold small text-uppercase text-secondary">Sexo</label>
                                        <select name="sexo" class="form-select">
                                            <option value="M" <?= $pet["Sexo"] === "M" ? "selected" : "" ?>>Macho</option>
                                            <option value="F" <?= $pet["Sexo"] === "F" ? "selected" : "" ?>>Femea</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-bold small text-uppercase text-secondary">Porte</label>
                                        <select name="porte" class="form-select">
                                            <?php foreach (["Pequeno", "Medio", "Grande"] as $porte): ?>
                                                <option value="<?= $porte ?>" <?= $pet["Porte"] === $porte ? "selected" : "" ?>><?= $porte ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold small text-uppercase text-secondary">Status</label>
                                        <select name="status" class="form-select">
                                            <?php foreach (["Cadastrado", "Disponivel", "Em_adocao", "Adotado", "Em_tratamento"] as $status): ?>
                                                <option value="<?= $status ?>" <?= $pet["Status"] === $status ? "selected" : "" ?>><?= $status ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold small text-uppercase text-secondary">Abrigo</label>
                                        <select name="abrigo_id" class="form-select">
                                            <?php foreach ($abrigos as $abrigo): ?>
                                                <option value="<?= (int) $abrigo["Id_abrigo"] ?>" <?= (int) $pet["fk_Abrigo_Id_abrigo"] === (int) $abrigo["Id_abrigo"] ? "selected" : "" ?>>
                                                    <?= htmlspecialchars($abrigo["Nome"]) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label class="form-label fw-bold small text-uppercase text-secondary">Vacinas registradas</label>
                                        <?php if (count($vacinasPet) > 0): ?>
                                            <div class="table-responsive border rounded-3 mb-3">
                                                <table class="table table-sm mb-0">
                                                    <tbody>
                                                        <?php foreach ($vacinasPet as $vacinaPet): ?>
                                                            <tr>
                                                                <td class="fw-bold"><?= htmlspecialchars($vacinaPet["Nome"]) ?></td>
                                                                <td><?= htmlspecialchars($vacinaPet["Data_aplicacao"]) ?></td>
                                                                <td><?= htmlspecialchars($vacinaPet["Proxima_dose"] ?: "Sem proxima dose") ?></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <?php else: ?>
                                            <p class="text-muted small mb-3">Nenhuma vacina registrada.</p>
                                        <?php endif; ?>

                                        <label class="form-label fw-bold small text-uppercase text-secondary">Adicionar vacinas</label>
                                        <?php for ($i = 0; $i < 2; $i++): ?>
                                            <div class="row g-2 mb-2">
                                                <div class="col-md-5">
                                                    <select name="vacina_id[]" class="form-select">
                                                        <option value="">Selecionar vacina</option>
                                                        <?php foreach ($vacinas as $vacina): ?>
                                                            <option value="<?= (int) $vacina["Id_vacina"] ?>"><?= htmlspecialchars($vacina["Nome"]) ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="date" name="data_aplicacao[]" class="form-control">
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="date" name="proxima_dose[]" class="form-control">
                                                </div>
                                            </div>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-dark">Salvar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <?php if (in_array($pet["Status"], ["Disponivel", "Em_adocao"])): ?>
                <div class="modal fade" id="<?= $removerModalId ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0" style="border-radius: 16px;">
                            <div class="modal-header">
                                <h5 class="modal-title fw-bold">Remover anuncio</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                            </div>
                            <div class="modal-body">
                                Deseja remover <?= htmlspecialchars($pet["Nome"]) ?> da lista de pets disponiveis? Candidaturas pendentes serao canceladas.
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancelar</button>
                                <form method="POST" class="m-0">
                                    <input type="hidden" name="acao" value="remover_anuncio">
                                    <input type="hidden" name="animal_id" value="<?= (int) $pet["Id_animal"] ?>">
                                    <button type="submit" class="btn btn-danger">Remover anuncio</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>
