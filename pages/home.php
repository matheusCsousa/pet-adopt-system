<main class="text-center py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h1 class="display-4 fw-bold">Adote um Pet!</h1>
            <p class="lead text-muted">Encontre seu novo melhor amigo ou gerencie os cadastros da sua ONG de forma simples e rápida.</p>
            <hr class="my-4">
            <?php if (!isset($_SESSION['usuario_id'])): ?>
                <a class="btn btn-dark btn-lg" href="?page=login" role="button">Acessar Sistema</a>
            <?php else: ?>
                <p>Olá, <strong><?= $_SESSION['usuario_nome'] ?></strong>! O que deseja fazer hoje?</p>
                <a class="btn btn-dark w-50 py-3 fw-bold shadow"  href="?page=cadastrar_pet">Cadastrar Novo Pet</a>
            <?php endif; ?>
        </div>
    </div>
</main>
