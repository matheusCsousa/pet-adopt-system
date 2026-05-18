<?php
require_once "src/db.php";
require_once "src/Gateway/AnimalGateway.php";

exigirFuncionario();

$animalGateway = new AnimalGateway();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $animalId = (int) ($_POST["animal_id"] ?? 0);

    try {
        if (($_POST["acao"] ?? "") === "anunciar") {
            $animalGateway->announce($animalId);
            $sucesso = "Pet publicado para adocao com sucesso.";
        }
    } catch (Exception $e) {
        $erro = "Nao foi possivel anunciar o pet.";
    }
}

$pets = $animalGateway->getNotAnnounced();
?>

<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Anunciar pets</h2>
            <p class="text-muted mb-0">Escolha pets cadastrados para publicar na lista de adocao.</p>
        </div>
        <span class="badge bg-light text-dark border p-2"><?= count($pets) ?> aguardando</span>
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

    <?php if (count($pets) === 0): ?>
        <div class="bg-white shadow-sm p-5 text-center" style="border-radius: 18px;">
            <p class="text-muted mb-4">Nenhum pet aguardando anuncio.</p>
            <a href="?page=cadastrar_pet" class="btn btn-dark" style="border-radius: 12px;">Cadastrar pet</a>
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
            ?>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 18px; overflow: hidden;">
                    <img src="<?= htmlspecialchars($foto) ?>" class="card-img-top" style="height: 230px; object-fit: cover;" alt="<?= htmlspecialchars($pet["Nome"]) ?>">
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
                            <a href="?page=detalhes_pet&id=<?= (int) $pet["Id_animal"] ?>" class="btn btn-light border w-50" style="border-radius: 12px;">Detalhes</a>
                            <button type="button" class="btn btn-dark w-50" style="border-radius: 12px;" data-bs-toggle="modal" data-bs-target="#confirmarAnuncio<?= (int) $pet["Id_animal"] ?>">
                                Anunciar
                            </button>
                            <div class="modal fade" id="confirmarAnuncio<?= (int) $pet["Id_animal"] ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0" style="border-radius: 16px;">
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold">Confirmar anuncio</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                                        </div>
                                        <div class="modal-body">
                                            Deseja publicar <?= htmlspecialchars($pet["Nome"]) ?> para adocao?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancelar</button>
                                            <form method="POST" class="m-0">
                                                <input type="hidden" name="acao" value="anunciar">
                                                <input type="hidden" name="animal_id" value="<?= (int) $pet["Id_animal"] ?>">
                                                <button type="submit" class="btn btn-dark">Confirmar</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
