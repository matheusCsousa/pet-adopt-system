<?php

function usuarioLogado() {
    return isset($_SESSION["usuario_id"], $_SESSION["usuario_tipo"]);
}

function usuarioFuncionario() {
    return usuarioLogado() && $_SESSION["usuario_tipo"] === "funcionario";
}

function usuarioAdotante() {
    return usuarioLogado() && $_SESSION["usuario_tipo"] === "adotante";
}

function exigirLogin() {
    if (!usuarioLogado()) {
        header("Location: index.php?page=login");
        exit();
    }
}

function exigirFuncionario() {
    exigirLogin();

    if (!usuarioFuncionario()) {
        header("Location: index.php?page=listar_pets");
        exit();
    }
}
