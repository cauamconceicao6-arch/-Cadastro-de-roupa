<?php

require_once "vendor/autoload.php";

use Model\RoupaModel;
use Controller\RoupaController;


$path = parse_url(
    $_SERVER["REQUEST_URI"],
    PHP_URL_PATH
);


$parts = explode("/", trim($path, "/"));


$resource = $parts[1] ?? null;

$id = $parts[2] ?? null;


header(
    "Content-Type: application/json; charset=UTF-8"
);


if ($resource !== "roupas") {

    http_response_code(404);

    echo json_encode([
        "error" => "Rota desconhecida!"
    ]);

    exit;
}


try {

    $roupaModel = new RoupaModel();

    $roupaController =
        new RoupaController($roupaModel);


    $roupaController->ProcessRequest(
        $_SERVER["REQUEST_METHOD"],
        $id
    );

} catch (\Throwable $error) {

    error_log($error->getMessage());

    http_response_code(500);

    echo json_encode([
        "error" => "Erro interno do servidor."
    ]);
}