<?php
session_start();
require_once "src/config.php";
require_once "src/db.php";

$page = $_GET["page"] ?? "home";

$allowed = ["home", "login", "cadastrar_pet", "logout"];
if (!in_array($page, $allowed)) {
    $page = "404";
}

include "templates/header.php";
include "pages/{$page}.php";
include "templates/footer.php";
?>
