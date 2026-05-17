<?php
require_once "src/db.php";
require_once "src/Gateway/AdotanteGateway.php";

if (usuarioLogado()) {
    header("Location: index.php?page=perfil");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $dados = [
        "nome" => trim($_POST["nome"] ?? ""),
        "cpf" => trim($_POST["cpf"] ?? ""),
        "telefone" => trim($_POST["telefone"] ?? ""),
        "email" => trim($_POST["email"] ?? ""),
        "senha" => $_POST["senha"] ?? "",
        "endereco" => trim($_POST["endereco"] ?? ""),
    ];

    if ($dados["nome"] === "" || $dados["cpf"] === "" || $dados["email"] === "" || $dados["senha"] === "") {
        $erro = "Preencha nome, CPF, e-mail e senha.";
    } else {
        try {
            $adotanteGateway = new AdotanteGateway();
            $id = $adotanteGateway->create($dados);

            $_SESSION["usuario_id"] = $id;
            $_SESSION["usuario_nome"] = $dados["nome"];
            $_SESSION["usuario_tipo"] = "adotante";

            header("Location: index.php?page=listar_pets");
            exit();
        } catch (Exception $e) {
            $erro = "Nao foi possivel criar o cadastro. Verifique se CPF ou e-mail ja estao em uso.";
        }
    }
}
?>

<main class="row justify-content-center">
    <div class="col-md-7 col-lg-6">
        <div class="card shadow-sm border-0 mt-4" style="border-radius: 15px;">
            <div class="card-body p-5">
                <h2 class="fw-bold mb-3 text-center">Criar conta</h2>
                <p class="text-muted text-center mb-4">Cadastre-se como adotante</p>

                <?php if (isset($erro)): ?>
                    <div class="alert alert-danger border-0 shadow-sm" role="alert">
                        <?= htmlspecialchars($erro) ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold small text-uppercase text-secondary">Nome</label>
                            <input type="text" name="nome" class="form-control" required value="<?= htmlspecialchars($_POST["nome"] ?? "") ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-uppercase text-secondary">CPF</label>
                            <input type="text" name="cpf" class="form-control" required value="<?= htmlspecialchars($_POST["cpf"] ?? "") ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-uppercase text-secondary">Telefone</label>
                            <input type="text" name="telefone" class="form-control" value="<?= htmlspecialchars($_POST["telefone"] ?? "") ?>">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold small text-uppercase text-secondary">E-mail</label>
                            <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($_POST["email"] ?? "") ?>">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold small text-uppercase text-secondary">Senha</label>
                            <input type="password" name="senha" class="form-control" required>
                        </div>
                        <div class="col-md-12 mb-4">
                            <label class="form-label fw-bold small text-uppercase text-secondary">Endereco</label>
                            <input type="text" name="endereco" class="form-control" value="<?= htmlspecialchars($_POST["endereco"] ?? "") ?>">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-dark btn-lg w-100" style="border-radius: 12px;">Criar conta</button>
                </form>
            </div>
        </div>
    </div>
</main>
