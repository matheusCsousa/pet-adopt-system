<?php
// pages/login.php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario_digitado = $_POST['usuario'] ?? '';
    $senha_digitada = $_POST['senha'] ?? '';

    // Lógica de Validação: Usuário 'admin' e Senha '123'
    if ($usuario_digitado === "admin" && $senha_digitada === "123") {
        $_SESSION['usuario_id'] = 1;
        $_SESSION['usuario_nome'] = "admin"; // Nome que aparecerá no Header
        
        header("Location: index.php?page=home");
        exit();
    } else {
        $erro = "Usuário ou senha incorretos!";
    }
}
?>

<main class="row justify-content-center">
    <div class="col-md-4">
        <div class="card shadow-sm border-0 mt-5">
            <div class="card-body p-5">
                <h2 class="fw-bold mb-3 text-center">Login</h2>
                <p class="text-muted text-center mb-4">Identifique-se para acessar</p>

                <?php if(isset($erro)): ?>
                    <div class="alert alert-danger py-2 text-center" role="alert">
                        <small><?= $erro ?></small>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label text-secondary small fw-bold">USUÁRIO</label>
                        <input type="text" name="usuario" class="form-control form-control-lg" placeholder="Ex: admin" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-secondary small fw-bold">SENHA</label>
                        <input type="password" name="senha" class="form-control form-control-lg" placeholder="Digite 123" required>
                    </div>

                    <button type="submit" class="btn btn-dark btn-lg w-100 shadow-sm mt-3" style="border-radius: 10px;">
                        Entrar no Sistema
                    </button>
                </form>
            </div>
        </div>
    </div>
</main>