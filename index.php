<?php

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

require_once __DIR__ . '/Config/Configuration.php';

use Controller\RoupaController;

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$path = str_replace('/index.php', '', $path);
$parts = explode('/', trim($path, '/'));

if (empty($parts[0]) || $parts[0] !== 'roupas') {
    header("Content-Type: application/json; charset=UTF-8");
    http_response_code(404);
    echo json_encode(["error" => "Rota desconhecida!"]);
    exit;
}

$id = isset($parts[1]) && is_numeric($parts[1]) ? (int) $parts[1] : null;

$controller = new RoupaController();
$controller->processRequest($method, $id);