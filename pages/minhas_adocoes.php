<?php
require_once "src/db.php";
require_once "src/Gateway/AdocaoGateway.php";

exigirLogin();

if (!usuarioAdotante()) {
    header("Location: index.php?page=home");
    exit();
}

$adocaoGateway = new AdocaoGateway();
$statusFiltro = $_GET["status"] ?? "";
$statusPermitidos = ["", "Pendente", "Aprovada", "Cancelada"];

if (!in_array($statusFiltro, $statusPermitidos)) {
    $statusFiltro = "";
}

$solicitacoes = $adocaoGateway->getByAdotanteWithDetails($_SESSION["usuario_id"], $statusFiltro);
?>

<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Minhas candidaturas</h2>
            <p class="text-muted mb-0">Acompanhe os pets que voce se candidatou para adotar.</p>
        </div>
        <span class="badge bg-light text-dark border p-2"><?= count($solicitacoes) ?> candidaturas</span>
    </div>

    <form method="GET" class="bg-white shadow-sm p-3 mb-4" style="border-radius: 14px;">
        <input type="hidden" name="page" value="minhas_adocoes">
        <div class="row g-3 align-items-end">
            <div class="col-md-8">
                <label class="form-label fw-bold small text-uppercase text-secondary">Filtrar por status</label>
                <select name="status" class="form-select">
                    <option value="" <?= $statusFiltro === "" ? "selected" : "" ?>>Todos</option>
                    <option value="Pendente" <?= $statusFiltro === "Pendente" ? "selected" : "" ?>>Pendente</option>
                    <option value="Aprovada" <?= $statusFiltro === "Aprovada" ? "selected" : "" ?>>Aprovada</option>
                    <option value="Cancelada" <?= $statusFiltro === "Cancelada" ? "selected" : "" ?>>Cancelada</option>
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-dark w-100" style="border-radius: 12px;">Aplicar filtro</button>
            </div>
        </div>
    </form>

    <?php if (count($solicitacoes) === 0): ?>
        <div class="bg-white shadow-sm p-5 text-center" style="border-radius: 18px;">
            <p class="text-muted mb-4">Nenhuma candidatura encontrada para este filtro.</p>
            <a href="?page=listar_pets" class="btn btn-dark" style="border-radius: 12px;">Ver pets</a>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <?php foreach ($solicitacoes as $solicitacao): ?>
            <?php
                $foto = !empty($solicitacao["foto_principal_id"])
                    ? "foto.php?id=" . (int) $solicitacao["foto_principal_id"]
                    : (!empty($solicitacao["foto_principal"])
                        ? $solicitacao["foto_principal"]
                        : "https://images.unsplash.com/photo-1543466835-00a7907e9de1?auto=format&fit=crop&w=500&q=80");
            ?>
            <div class="col-md-6">
                <div class="bg-white shadow-sm h-100" style="border-radius: 18px; overflow: hidden;">
                    <div class="row g-0 h-100">
                        <div class="col-md-5">
                            <img src="<?= htmlspecialchars($foto) ?>" class="w-100 h-100" style="min-height: 220px; object-fit: cover;" alt="<?= htmlspecialchars($solicitacao["animal_nome"]) ?>">
                        </div>
                        <div class="col-md-7">
                            <div class="p-4 h-100 d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                    <div>
                                        <h4 class="fw-bold mb-1"><?= htmlspecialchars($solicitacao["animal_nome"]) ?></h4>
                                        <p class="text-muted mb-0">
                                            <?= htmlspecialchars($solicitacao["Especie"]) ?>
                                            <?= $solicitacao["Raca"] ? " • " . htmlspecialchars($solicitacao["Raca"]) : "" ?>
                                        </p>
                                    </div>
                                    <span class="badge bg-light text-dark border p-2"><?= htmlspecialchars($solicitacao["Status"]) ?></span>
                                </div>

                                <p class="text-secondary small mb-2">
                                    Aberta em <?= htmlspecialchars($solicitacao["Data_abertura"]) ?>
                                    <?php if ($solicitacao["Data_conclusao"]): ?>
                                        • Concluida em <?= htmlspecialchars($solicitacao["Data_conclusao"]) ?>
                                    <?php endif; ?>
                                </p>

                                <div class="border rounded-3 p-3 mb-3">
                                    <span class="text-muted small d-block">Mensagem da ONG</span>
                                    <p class="mb-0"><?= htmlspecialchars($solicitacao["Descricao"] ?: "Ainda sem observacoes do funcionario.") ?></p>
                                </div>

                                <a href="?page=detalhes_pet&id=<?= (int) $solicitacao["fk_Animal_Id_animal"] ?>" class="btn btn-dark mt-auto" style="border-radius: 12px;">
                                    Ver pet
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
