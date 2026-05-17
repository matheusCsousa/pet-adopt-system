<?php
try {
    $pdo = new PDO("mysql:host=localhost", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = file_get_contents(__DIR__ . "/ong_adocao.sql");
    $pdo->exec($sql);

    echo "Banco criado com sucesso!";
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}
