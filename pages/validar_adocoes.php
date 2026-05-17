<?php
require_once "src/db.php";
require_once "src/Gateway/AdocaoGateway.php";

exigirFuncionario();

$adocaoGateway = new AdocaoGateway();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $adocaoId = (int) ($_POST["adocao_id"] ?? 0);
    $acao = $_POST["acao"] ?? "";
    $descricao = trim($_POST["descricao"] ?? "");
    $mensagemReprovados = trim($_POST["mensagem_reprovados"] ?? "");

    if ($descricao === "") {
        $descricao = "Sua candidatura foi aprovada. A ONG entrara em contato para os proximos passos da adocao.";
    }

    if ($mensagemReprovados === "") {
        $mensagemReprovados = "Outro candidato foi selecionado para adotar este pet. Agradecemos seu interesse e carinho.";
    }

    try {
        if ($acao === "aprovar") {
            $adocaoGateway->approve($adocaoId, $_SESSION["usuario_id"], $descricao, $mensagemReprovados);
            $sucesso = "Adocao aprovada. Os outros candidatos receberam a mensagem de nao aprovacao.";
        } elseif ($acao === "cancelar") {
            $adocaoGateway->cancel($adocaoId, $_SESSION["usuario_id"], $descricao);
            $sucesso = "Solicitacao cancelada.";
        }
    } catch (Exception $e) {
        $erro = $e->getMessage();
    }
}

$solicitacoes = $adocaoGateway->getPendingWithDetails();
?>

<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Validar adocoes</h2>
            <p class="text-muted mb-0">Analise os candidatos e aprove apenas um adotante por pet.</p>
        </div>
        <span class="badge bg-light text-dark border p-2"><?= count($solicitacoes) ?> pendentes</span>
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

    <?php if (count($solicitacoes) === 0): ?>
        <div class="bg-white shadow-sm p-5 text-center" style="border-radius: 18px;">
            <p class="text-muted mb-0">Nenhuma solicitacao pendente no momento.</p>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <?php foreach ($solicitacoes as $solicitacao): ?>
            <div class="col-lg-6">
                <div class="bg-white shadow-sm p-4 h-100" style="border-radius: 18px;">
                    <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                        <div>
                            <h4 class="fw-bold mb-1"><?= htmlspecialchars($solicitacao["animal_nome"]) ?></h4>
                            <p class="text-muted mb-0">
                                <?= htmlspecialchars($solicitacao["Especie"]) ?>
                                <?= $solicitacao["Raca"] ? " • " . htmlspecialchars($solicitacao["Raca"]) : "" ?>
                            </p>
                        </div>
                        <span class="badge bg-light text-dark border p-2"><?= htmlspecialchars($solicitacao["animal_status"]) ?></span>
                    </div>

                    <div class="border rounded-3 p-3 mb-3">
                        <h5 class="fw-bold mb-2"><?= htmlspecialchars($solicitacao["adotante_nome"]) ?></h5>
                        <p class="text-secondary mb-1"><?= htmlspecialchars($solicitacao["adotante_email"]) ?></p>
                        <p class="text-secondary mb-1"><?= htmlspecialchars($solicitacao["adotante_telefone"] ?: "Telefone nao informado") ?></p>
                        <p class="text-secondary mb-0"><?= htmlspecialchars($solicitacao["adotante_endereco"] ?: "Endereco nao informado") ?></p>
                    </div>

                    <form method="POST">
                        <input type="hidden" name="adocao_id" value="<?= (int) $solicitacao["Id_adocao"] ?>">

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-secondary">Mensagem para o candidato aprovado</label>
                            <textarea name="descricao" class="form-control" rows="3" placeholder="Registre a avaliacao, orientacoes ou proximos passos.">Sua candidatura foi aprovada. A ONG entrara em contato para os proximos passos da adocao.</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-secondary">Mensagem para os outros candidatos deste pet</label>
                            <textarea name="mensagem_reprovados" class="form-control" rows="3" placeholder="Explique que outro candidato foi selecionado.">Outro candidato foi selecionado para adotar este pet. Agradecemos seu interesse e carinho.</textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" name="acao" value="aprovar" class="btn btn-dark w-50" style="border-radius: 12px;">
                                Aprovar
                            </button>
                            <button type="submit" name="acao" value="cancelar" class="btn btn-outline-danger w-50" style="border-radius: 12px;">
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
