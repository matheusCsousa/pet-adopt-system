<?php
require_once "src/db.php";
require_once "src/Gateway/AnimalGateway.php";
require_once "src/Gateway/FotoGateway.php";
require_once "src/Gateway/AbrigoGateway.php";
require_once "src/Gateway/VacinaGateway.php";

exigirFuncionario();

$abrigoGateway = new AbrigoGateway();
$abrigos = $abrigoGateway->getAll();
$vacinaGateway = new VacinaGateway();
$vacinas = $vacinaGateway->getAll();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST["nome"] ?? "";
    $especie = $_POST["especie"] ?? "";
    $raca = $_POST["raca"] ?? "";
    $sexo = $_POST["sexo"] ?? "M";
    $porte = $_POST["porte"] ?? "Medio";
    $abrigo_id = $_POST["abrigo_id"] ?? "";

    if (isset($_FILES["fotos"]) && is_array($_FILES["fotos"]["error"])) {
        $fotosValidas = [];

        foreach ($_FILES["fotos"]["error"] as $indice => $erroUpload) {
            if ($erroUpload !== UPLOAD_ERR_OK) {
                continue;
            }

            $arquivoTemporario = $_FILES["fotos"]["tmp_name"][$indice];
            $tipoMime = function_exists("mime_content_type")
                ? mime_content_type($arquivoTemporario)
                : ($_FILES["fotos"]["type"][$indice] ?? "");
            $dadosFoto = file_get_contents($arquivoTemporario);

            if ($dadosFoto !== false && is_string($tipoMime) && strpos($tipoMime, "image/") === 0) {
                $fotosValidas[] = [
                    "nome_arquivo" => $_FILES["fotos"]["name"][$indice],
                    "tipo_mime" => $tipoMime,
                    "dados" => $dadosFoto,
                ];
            }
        }

        if (count($fotosValidas) > 0) {
            try {
                $animalGateway = new AnimalGateway();
                $id_animal = $animalGateway->create([
                    "nome" => $nome,
                    "especie" => $especie,
                    "raca" => $raca,
                    "sexo" => $sexo,
                    "porte" => $porte,
                    "status" => "Cadastrado",
                    "fk_abrigo" => $abrigo_id,
                ]);

                $fotoGateway = new FotoGateway();

                foreach ($fotosValidas as $indice => $foto) {
                    $fotoGateway->create([
                        "nome_arquivo" => $foto["nome_arquivo"],
                        "tipo_mime" => $foto["tipo_mime"],
                        "dados" => $foto["dados"],
                        "is_principal" => $indice === 0,
                        "fk_animal" => $id_animal,
                    ]);
                }

                $vacinaGateway->applyManyToAnimal(
                    $id_animal,
                    $_POST["vacina_id"] ?? [],
                    $_POST["data_aplicacao"] ?? [],
                    $_POST["proxima_dose"] ?? []
                );

                $sucesso = "O pet <strong>$nome</strong> foi registrado no banco de dados. Para aparecer aos adotantes, publique em Anunciar.";
            } catch (Exception $e) {
                $erro = "Erro ao salvar no banco de dados: " . $e->getMessage();
            }
        } else {
            $erro = "Por favor, selecione pelo menos uma imagem valida.";
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
                    <h2 class="fw-bold mt-2">Cadastrar Pet</h2>
                    <p class="text-muted">Registre o pet no banco de dados interno</p>
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
                            <label class="form-label fw-bold small text-uppercase text-secondary">Fotos do Pet</label>
                            <div class="input-group">
                                <input type="file" name="fotos[]" class="form-control border-light-subtle"
                                       id="inputGroupFile02" accept="image/*" multiple required>
                            </div>
                            <div class="form-text mt-2">Escolha uma ou mais fotos. A primeira será a foto principal.</div>
                        </div>

                        <div class="col-md-12 mb-5">
                            <label class="form-label fw-bold small text-uppercase text-secondary">Vacinas</label>
                            <?php for ($i = 0; $i < 3; $i++): ?>
                                <div class="row g-2 mb-2">
                                    <div class="col-md-5">
                                        <select name="vacina_id[]" class="form-select border-light-subtle">
                                            <option value="">Selecionar vacina</option>
                                            <?php foreach ($vacinas as $vacina): ?>
                                                <option value="<?= (int) $vacina["Id_vacina"] ?>"><?= htmlspecialchars($vacina["Nome"]) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="date" name="data_aplicacao[]" class="form-control border-light-subtle">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="date" name="proxima_dose[]" class="form-control border-light-subtle">
                                    </div>
                                </div>
                            <?php endfor; ?>
                            <div class="form-text mt-2">Preencha apenas as vacinas já aplicadas. A última data é a próxima dose.</div>
                        </div>
                    </div>

                    <div class="d-flex gap-3">
                        <a href="?page=home" class="btn btn-light w-50 py-3 fw-bold text-secondary" style="border-radius: 10px;">
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-dark w-50 py-3 fw-bold shadow" style="border-radius: 10px;">
                            Salvar Pet
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
