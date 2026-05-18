<?php
require_once "src/db.php";
require_once "src/Gateway/AnimalGateway.php";
require_once "src/Gateway/AdocaoGateway.php";
require_once "src/Gateway/FotoGateway.php";

$animalGateway = new AnimalGateway();
$fotoGateway = new FotoGateway();
$animalId = (int) ($_GET["id"] ?? $_POST["animal_id"] ?? 0);

if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST["acao"] ?? "") === "adotar") {
    if (!usuarioAdotante()) {
        header("Location: index.php?page=login");
        exit();
    }

    try {
        $adocaoGateway = new AdocaoGateway();
        $adocaoGateway->createRequest(
            $_SESSION["usuario_id"],
            $animalId,
            "Solicitacao aberta pelo site."
        );
        $sucesso = "Solicitacao de adocao enviada com sucesso.";
    } catch (Exception $e) {
        $erro = $e->getMessage();
    }
}

$pet = $animalGateway->getDetailsById($animalId);

if (!$pet) {
    header("Location: index.php?page=listar_pets");
    exit();
}

$foto = !empty($pet["foto_principal_id"])
    ? "foto.php?id=" . (int) $pet["foto_principal_id"]
    : (!empty($pet["foto_principal"])
        ? $pet["foto_principal"]
        : "https://images.unsplash.com/photo-1543466835-00a7907e9de1?auto=format&fit=crop&w=900&q=80");
$vacinas = $animalGateway->getVacinasByAnimal($animalId);
$fotos = $fotoGateway->getByAnimal($animalId);
$podeSolicitarAdocao = in_array($pet["Status"], ["Disponivel", "Em_adocao"]);
$solicitacaoAtual = null;

if (usuarioAdotante()) {
    $adocaoGateway = new AdocaoGateway();
    $solicitacaoAtual = $adocaoGateway->getByAdotanteAndAnimal($_SESSION["usuario_id"], $animalId);
}
?>

<div class="py-4">
    <a href="?page=listar_pets" class="btn btn-light border mb-4" style="border-radius: 10px;">Voltar para pets</a>

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

    <div class="row g-4 align-items-start">
        <div class="col-lg-6">
            <?php if (count($fotos) > 1): ?>
                <div id="petFotosCarousel" class="carousel slide shadow-sm" data-bs-ride="carousel" style="border-radius: 18px; overflow: hidden;">
                    <div class="carousel-inner">
                        <?php foreach ($fotos as $indice => $fotoItem): ?>
                            <?php
                                $fotoUrl = !empty($fotoItem["Dados"])
                                    ? "foto.php?id=" . (int) $fotoItem["Id_foto"]
                                    : $fotoItem["URL_foto"];
                            ?>
                            <div class="carousel-item <?= $indice === 0 ? "active" : "" ?>">
                                <img src="<?= htmlspecialchars($fotoUrl) ?>" class="d-block w-100" style="height: 520px; object-fit: cover;" alt="<?= htmlspecialchars($pet["Nome"]) ?>">
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#petFotosCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#petFotosCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    </button>
                </div>
            <?php else: ?>
                <img src="<?= htmlspecialchars($foto) ?>" class="img-fluid shadow-sm w-100" style="height: 520px; object-fit: cover; border-radius: 18px;" alt="<?= htmlspecialchars($pet["Nome"]) ?>">
            <?php endif; ?>
        </div>

        <div class="col-lg-6">
            <div class="bg-white shadow-sm p-4" style="border-radius: 18px;">
                <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                    <div>
                        <h2 class="fw-bold mb-1"><?= htmlspecialchars($pet["Nome"]) ?></h2>
                        <p class="text-muted mb-0"><?= htmlspecialchars($pet["Especie"]) ?> • <?= htmlspecialchars($pet["Raca"] ?: "Sem raca informada") ?></p>
                    </div>
                    <span class="badge bg-light text-dark border p-2"><?= htmlspecialchars($pet["Status"]) ?></span>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <div class="border rounded-3 p-3">
                            <span class="text-muted small d-block">Sexo</span>
                            <strong><?= $pet["Sexo"] === "M" ? "Macho" : "Femea" ?></strong>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded-3 p-3">
                            <span class="text-muted small d-block">Porte</span>
                            <strong><?= htmlspecialchars($pet["Porte"]) ?></strong>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded-3 p-3">
                            <span class="text-muted small d-block">Entrada</span>
                            <strong><?= htmlspecialchars($pet["Data_entrada"]) ?></strong>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded-3 p-3">
                            <span class="text-muted small d-block">Abrigo</span>
                            <strong><?= htmlspecialchars($pet["abrigo_nome"] ?: "Nao informado") ?></strong>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <h5 class="fw-bold">Contato do abrigo</h5>
                    <p class="text-secondary mb-1"><?= htmlspecialchars($pet["abrigo_endereco"] ?: "Endereco nao informado") ?></p>
                    <p class="text-secondary mb-0"><?= htmlspecialchars($pet["abrigo_telefone"] ?: "Telefone nao informado") ?></p>
                </div>

                <?php if (usuarioAdotante()): ?>
                    <?php if ($solicitacaoAtual): ?>
                        <button type="button" class="btn btn-secondary btn-lg w-100" style="border-radius: 12px;" disabled>
                            Candidatura enviada
                        </button>
                    <?php elseif ($podeSolicitarAdocao): ?>
                        <button type="button" class="btn btn-dark btn-lg w-100" style="border-radius: 12px;" data-bs-toggle="modal" data-bs-target="#confirmarCandidatura">
                            Quero me candidatar
                        </button>
                    <?php else: ?>
                        <button type="button" class="btn btn-secondary btn-lg w-100" style="border-radius: 12px;" disabled>
                            Este pet nao esta disponivel
                        </button>
                    <?php endif; ?>
                <?php elseif (!usuarioLogado()): ?>
                    <a href="?page=login" class="btn btn-dark btn-lg w-100" style="border-radius: 12px;">Entrar para adotar</a>
                <?php else: ?>
                    <span class="btn btn-light border btn-lg w-100 disabled" style="border-radius: 12px;">Adocao disponivel para adotantes</span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="bg-white shadow-sm p-4 mt-4" style="border-radius: 18px;">
        <h4 class="fw-bold mb-3">Vacinas</h4>

        <?php if (count($vacinas) > 0): ?>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Vacina</th>
                            <th>Aplicacao</th>
                            <th>Proxima dose</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($vacinas as $vacina): ?>
                            <tr>
                                <td class="fw-bold"><?= htmlspecialchars($vacina["Nome"]) ?></td>
                                <td><?= htmlspecialchars($vacina["Data_aplicacao"]) ?></td>
                                <td><?= htmlspecialchars($vacina["Proxima_dose"] ?: "Nao informada") ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted mb-0">Nenhuma vacina registrada para este pet.</p>
        <?php endif; ?>
    </div>
</div>

<?php if (usuarioAdotante() && !$solicitacaoAtual && $podeSolicitarAdocao): ?>
    <div class="modal fade" id="confirmarCandidatura" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius: 16px;">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Confirmar candidatura</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    Deseja se candidatar para adotar <?= htmlspecialchars($pet["Nome"]) ?>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancelar</button>
                    <form method="POST" class="m-0">
                        <input type="hidden" name="acao" value="adotar">
                        <input type="hidden" name="animal_id" value="<?= (int) $pet["Id_animal"] ?>">
                        <button type="submit" class="btn btn-dark">Confirmar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
