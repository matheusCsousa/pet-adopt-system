<?php
require_once "src/db.php";
require_once "src/Gateway/FuncionarioGateway.php";
require_once "src/Gateway/AdotanteGateway.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario_digitado = trim($_POST["usuario"] ?? "");
    $senha_digitada = $_POST["senha"] ?? "";

    $funcionarioGateway = new FuncionarioGateway();
    $funcionario = $funcionarioGateway->getByEmailAndSenha($usuario_digitado, $senha_digitada);

    if ($funcionario) {
        $_SESSION["usuario_id"] = $funcionario["Id_funcionario"];
        $_SESSION["usuario_nome"] = $funcionario["Nome"];
        $_SESSION["usuario_tipo"] = "funcionario";

        header("Location: index.php?page=home");
        exit();
    }

    $adotanteGateway = new AdotanteGateway();
    $adotante = $adotanteGateway->getByEmailAndSenha($usuario_digitado, $senha_digitada);

    if ($adotante) {
        $_SESSION["usuario_id"] = $adotante["Id_adotante"];
        $_SESSION["usuario_nome"] = $adotante["Nome"];
        $_SESSION["usuario_tipo"] = "adotante";

        header("Location: index.php?page=listar_pets");
        exit();
    }

    $erro = "E-mail ou senha incorretos!";
}
?>

<main class="row justify-content-center">
    <div class="col-md-4">
        <div class="card shadow-sm border-0 mt-5">
            <div class="card-body p-5">
                <h2 class="fw-bold mb-3 text-center">Login</h2>
                <p class="text-muted text-center mb-4">Identifique-se para acessar</p>

                <?php if (isset($erro)): ?>
                    <div class="alert alert-danger py-2 text-center" role="alert">
                        <small><?= $erro ?></small>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label text-secondary small fw-bold">E-MAIL</label>
                        <input type="email" name="usuario" class="form-control form-control-lg" placeholder="Ex: ana@ong.com" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-secondary small fw-bold">SENHA</label>
                        <input type="password" name="senha" class="form-control form-control-lg" placeholder="Digite sua senha" required>
                    </div>

                    <button type="submit" class="btn btn-dark btn-lg w-100 shadow-sm mt-3" style="border-radius: 10px;">
                        Entrar no Sistema
                    </button>
                </form>

                <p class="text-center text-muted mt-4 mb-0">
                    Ainda nao tem conta?
                    <a href="?page=cadastro" class="text-dark fw-bold">Cadastre-se</a>
                </p>
            </div>
        </div>
    </div>
</main>
