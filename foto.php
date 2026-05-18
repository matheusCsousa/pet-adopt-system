<?php
require_once __DIR__ . "/src/db.php";
require_once __DIR__ . "/src/Gateway/FotoGateway.php";

$fotoId = (int) ($_GET["id"] ?? 0);
$fotoGateway = new FotoGateway();
$foto = $fotoGateway->getById($fotoId);

if (!$foto) {
    http_response_code(404);
    exit();
}

if (!empty($foto["Dados"])) {
    header("Content-Type: " . ($foto["Tipo_mime"] ?: "image/jpeg"));
    header("Content-Length: " . strlen($foto["Dados"]));
    header("Cache-Control: public, max-age=86400");
    echo $foto["Dados"];
    exit();
}

if (!empty($foto["URL_foto"])) {
    header("Location: " . $foto["URL_foto"]);
    exit();
}

http_response_code(404);
