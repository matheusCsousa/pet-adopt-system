<?php
require_once "src/db.php";
require_once "src/Gateway/AdotanteGateway.php";
require_once "src/Gateway/FuncionarioGateway.php";

exigirLogin();

if (usuarioAdotante()) {
    $gateway = new AdotanteGateway();
    $usuario = $gateway->getById($_SESSION["usuario_id"]);
} else {
    $gateway = new FuncionarioGateway();
    $usuario = $gateway->getById($_SESSION["usuario_id"]);
}

if (!$usuario) {
    header("Location: index.php?page=logout");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $senha = $_POST["senha"] ?? "";

    try {
        if (usuarioAdotante()) {
            $dados = [
                "nome" => trim($_POST["nome"] ?? ""),
                "cpf" => trim($_POST["cpf"] ?? ""),
                "telefone" => trim($_POST["telefone"] ?? ""),
                "email" => trim($_POST["email"] ?? ""),
                "endereco" => trim($_POST["endereco"] ?? ""),
            ];

            $gateway->update($_SESSION["usuario_id"], $dados);
        } else {
            $dados = [
                "nome" => trim($_POST["nome"] ?? ""),
                "cargo" => trim($_POST["cargo"] ?? ""),
                "email" => trim($_POST["email"] ?? ""),
            ];

            $gateway->update($_SESSION["usuario_id"], $dados);
        }

        if ($senha !== "") {
            $gateway->updateSenha($_SESSION["usuario_id"], $senha);
        }

        $_SESSION["usuario_nome"] = $dados["nome"];
        $sucesso = "Informacoes atualizadas com sucesso.";

        $usuario = $gateway->getById($_SESSION["usuario_id"]);
    } catch (Exception $e) {
        $erro = "Nao foi possivel atualizar. Verifique se o e-mail ou CPF ja esta em uso.";
    }
}
?>

<main class="row justify-content-center">
    <div class="col-md-8 col-lg-7">
        <div class="card shadow-sm border-0 mt-4" style="border-radius: 15px;">
            <div class="card-body p-5">
                <h2 class="fw-bold mb-3">Meu perfil</h2>
                <p class="text-muted mb-4"><?= usuarioAdotante() ? "Dados de adotante" : "Dados de funcionario" ?></p>

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

                <form method="POST">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold small text-uppercase text-secondary">Nome</label>
                            <input type="text" name="nome" class="form-control" required value="<?= htmlspecialchars($usuario["Nome"] ?? "") ?>">
                        </div>

                        <?php if (usuarioAdotante()): ?>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-uppercase text-secondary">CPF</label>
                                <input type="text" name="cpf" class="form-control" required value="<?= htmlspecialchars($usuario["CPF"] ?? "") ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-uppercase text-secondary">Telefone</label>
                                <input type="text" name="telefone" class="form-control" value="<?= htmlspecialchars($usuario["Telefone"] ?? "") ?>">
                            </div>
                        <?php else: ?>
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-bold small text-uppercase text-secondary">Cargo</label>
                                <input type="text" name="cargo" class="form-control" required value="<?= htmlspecialchars($usuario["Cargo"] ?? "") ?>">
                            </div>
                        <?php endif; ?>

                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold small text-uppercase text-secondary">E-mail</label>
                            <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($usuario["Email"] ?? "") ?>">
                        </div>

                        <?php if (usuarioAdotante()): ?>
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-bold small text-uppercase text-secondary">Endereco</label>
                                <input type="text" name="endereco" class="form-control" value="<?= htmlspecialchars($usuario["Endereco"] ?? "") ?>">
                            </div>
                        <?php endif; ?>

                        <div class="col-md-12 mb-4">
                            <label class="form-label fw-bold small text-uppercase text-secondary">Nova senha</label>
                            <input type="password" name="senha" class="form-control" placeholder="Deixe em branco para manter a senha atual">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-dark btn-lg w-100" style="border-radius: 12px;">Salvar alteracoes</button>
                </form>
            </div>
        </div>
    </div>
</main>
