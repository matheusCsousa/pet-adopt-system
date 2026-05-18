<?php
session_start();
require_once "src/config.php";
require_once "src/db.php";
require_once "src/auth.php";

$page = $_GET["page"] ?? "home";

$allowed = ["home", "login", "cadastro", "perfil", "minhas_adocoes", "cadastrar_pet", "anunciar_pet", "gerenciar_pets", "logout", "listar_pets", "detalhes_pet", "validar_adocoes"];
if (!in_array($page, $allowed)) {
    $page = "home";
}

ob_start();
include "pages/{$page}.php";
$content = ob_get_clean();

include "templates/header.php";
echo $content;
include "templates/footer.php";
?>
