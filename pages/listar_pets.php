<?php
require_once "src/db.php";
require_once "src/Gateway/AnimalGateway.php";

$animalGateway = new AnimalGateway();
$pets = $animalGateway->getAvailableForAdoption();
$total_pets = count($pets);
?>

<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Pets Disponíveis</h2>
        <span class="badge bg-light text-dark border p-2"><?= $total_pets ?> pets encontrados</span>
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
            <div class="col-md-4">
                <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden;">
                    <?php $foto = !empty($pet["foto_principal_id"])
                        ? "foto.php?id=" . (int) $pet["foto_principal_id"]
                        : (!empty($pet["foto_principal"])
                            ? $pet["foto_principal"]
                            : "https://images.unsplash.com/photo-1543466835-00a7907e9de1?auto=format&fit=crop&w=500&q=80"); ?>
                    <img src="<?= $foto ?>" class="card-img-top" style="height: 250px; object-fit: cover;" alt="<?= htmlspecialchars(
    $pet["Nome"],
) ?>">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold mb-1"><?= htmlspecialchars(
                            $pet["Nome"],
                        ) ?></h5>
                        <p class="text-muted small mb-3"><?= htmlspecialchars(
                            $pet["Raca"] ?? $pet["Especie"],
                        ) ?> • <?= $pet["Sexo"] === "M"
     ? "Macho"
     : "Fêmea" ?></p>
                        <p class="card-text text-secondary"><?= htmlspecialchars(
                            $pet["Porte"],
                        ) ?> • Status: <?= htmlspecialchars(
     $pet["Status"],
 ) ?></p>
                        <a href="?page=detalhes_pet&id=<?= (int) $pet["Id_animal"] ?>" class="btn btn-dark w-100 py-2" style="border-radius: 12px;">
                            Ver detalhes
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if ($total_pets === 0): ?>
            <div class="col-12 text-center py-5">
                <p class="text-muted">Nenhum pet encontrado no momento.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
