<?php
require_once "src/db.php";
require_once "src/Gateway/AnimalGateway.php";
require_once "src/Gateway/FotoGateway.php";
require_once "src/Gateway/AbrigoGateway.php";

exigirFuncionario();

$abrigoGateway = new AbrigoGateway();
$abrigos = $abrigoGateway->getAll();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST["nome"] ?? "";
    $especie = $_POST["especie"] ?? "";
    $raca = $_POST["raca"] ?? "";
    $sexo = $_POST["sexo"] ?? "M";
    $porte = $_POST["porte"] ?? "Medio";
    $abrigo_id = $_POST["abrigo_id"] ?? "";

    if (isset($_FILES["foto"]) && $_FILES["foto"]["error"] === 0) {
        $extensao = pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION);
        $novoNome = uniqid() . "." . $extensao;
        $destino = "uploads/" . $novoNome;

        if (move_uploaded_file($_FILES["foto"]["tmp_name"], $destino)) {
            try {
                $animalGateway = new AnimalGateway();
                $id_animal = $animalGateway->create([
                    "nome" => $nome,
                    "especie" => $especie,
                    "raca" => $raca,
                    "sexo" => $sexo,
                    "porte" => $porte,
                    "fk_abrigo" => $abrigo_id,
                ]);

                $fotoGateway = new FotoGateway();
                $fotoGateway->create([
                    "url_foto" => "uploads/" . $novoNome,
                    "is_principal" => true,
                    "fk_animal" => $id_animal,
                ]);

                $sucesso = "O pet <strong>$nome</strong> foi registrado com sucesso!";
            } catch (Exception $e) {
                $erro = "Erro ao salvar no banco de dados: " . $e->getMessage();
            }
        } else {
            $erro = "Ops! Não conseguimos salvar a foto.";
        }
    } else {
        $erro = "Por favor, selecione uma foto do pet.";
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow-sm border-0" style="border-radius: 15px;">
            <div class="card-body p-5">

                <div class="text-center mb-5">
                    <span style="font-size: 3rem;">🐾</span>
                    <h2 class="fw-bold mt-2">Novo Cadastro</h2>
                    <p class="text-muted">Preencha as informações para anunciar o pet</p>
                </div>

                <?php if (isset($sucesso)): ?>
                    <div class="alert alert-success border-0 shadow-sm mb-4" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i> <?= $sucesso ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($erro)): ?>
                    <div class="alert alert-danger border-0 shadow-sm mb-4" role="alert">
                        <?= $erro ?>
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">

                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <label class="form-label fw-bold small text-uppercase text-secondary">Nome do Pet</label>
                            <input type="text" name="nome" class="form-control form-control-lg border-light-subtle"
                                   placeholder="Ex: Luke, Mel, Pipoca..." required style="background-color: #fcfcfc;">
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold small text-uppercase text-secondary">Espécie</label>
                            <select name="especie" class="form-select border-light-subtle" style="background-color: #fcfcfc;">
                                <option value="Cachorro">Cachorro</option>
                                <option value="Gato">Gato</option>
                                <option value="Outro">Outro</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold small text-uppercase text-secondary">Raça</label>
                            <input type="text" name="raca" class="form-control border-light-subtle"
                                   placeholder="Ex: Poodle, Persa..." style="background-color: #fcfcfc;">
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold small text-uppercase text-secondary">Sexo</label>
                            <select name="sexo" class="form-select border-light-subtle" style="background-color: #fcfcfc;">
                                <option value="M">Macho</option>
                                <option value="F">Fêmea</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold small text-uppercase text-secondary">Porte</label>
                            <select name="porte" class="form-select border-light-subtle" style="background-color: #fcfcfc;">
                                <option value="Pequeno">Pequeno</option>
                                <option value="Medio">Médio</option>
                                <option value="Grande">Grande</option>
                            </select>
                        </div>

                        <div class="col-md-12 mb-4">
                            <label class="form-label fw-bold small text-uppercase text-secondary">Abrigo</label>
                            <select name="abrigo_id" class="form-select border-light-subtle" required style="background-color: #fcfcfc;">
                                <?php foreach ($abrigos as $abrigo): ?>
                                    <option value="<?= $abrigo[
                                        "Id_abrigo"
                                    ] ?>"><?= htmlspecialchars(
    $abrigo["Nome"],
) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-12 mb-5">
                            <label class="form-label fw-bold small text-uppercase text-secondary">Foto de Perfil</label>
                            <div class="input-group">
                                <input type="file" name="foto" class="form-control border-light-subtle"
                                       id="inputGroupFile02" accept="image/*" required>
                            </div>
                            <div class="form-text mt-2">Escolha uma foto bem bonita para ajudar na adoção!</div>
                        </div>
                    </div>

                    <div class="d-flex gap-3">
                        <a href="?page=home" class="btn btn-light w-50 py-3 fw-bold text-secondary" style="border-radius: 10px;">
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-dark w-50 py-3 fw-bold shadow" style="border-radius: 10px;">
                            Salvar Cadastro
                        </button>
                    </div>

                </form>
            </div>
        </div>

        <p class="text-center mt-4 text-muted small">
            Dica: Certifique-se de que a foto está nítida antes de salvar.
        </p>
    </div>
</div>
