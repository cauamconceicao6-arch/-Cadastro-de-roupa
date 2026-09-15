<?php

namespace Controller;

use Model\RoupaModel;

class RoupaController
{
    private RoupaModel $model;

    public function __construct()
    {
        $this->model = new RoupaModel();
    }

    public function processRequest(string $method, ?int $id = null): void
    {
        header("Content-Type: application/json; charset=UTF-8");

        switch ($method) {
            case 'GET':
                if ($id) {
                    $this->getRoupaById($id);
                } else {
                    $this->getAllRoupas();
                }
                break;

            case 'POST':
                $this->createRoupa();
                break;

            case 'PUT':
                if ($id) {
                    $this->updateRoupa($id);
                } else {
                    http_response_code(400);
                    echo json_encode(["error" => "ID é necessário para atualização."]);
                }
                break;

            case 'DELETE':
                if ($id) {
                    $this->deleteRoupa($id);
                } else {
                    http_response_code(400);
                    echo json_encode(["error" => "ID é necessário para remoção."]);
                }
                break;

            default:
                http_response_code(455);
                echo json_encode(["error" => "Método HTTP não permitido."]);
                break;
        }
    }

    private function getAllRoupas(): void
    {
        $roupas = $this->model->readAllRoupas();
        http_response_code(200);
        echo json_encode($roupas);
    }

    private function getRoupaById(int $id): void
    {
        $roupa = $this->model->readById($id);
        if (!$roupa) {
            http_response_code(404);
            echo json_encode(["error" => "Roupa não encontrada."]);
            return;
        }

        http_response_code(200);
        echo json_encode($roupa);
    }

    private function createRoupa(): void
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (empty($data['nome']) || empty($data['tamanho']) || !isset($data['preco'])) {
            http_response_code(400);
            echo json_encode(["error" => "Campos 'nome', 'tamanho' e 'preco' são obrigatórios."]);
            return;
        }

        $success = $this->model->create($data);

        if ($success) {
            http_response_code(201);
            echo json_encode(["message" => "Roupa cadastrada com sucesso."]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Falha ao cadastrar roupa."]);
        }
    }

    private function updateRoupa(int $id): void
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!$this->model->readById($id)) {
            http_response_code(404);
            echo json_encode(["error" => "Roupa não encontrada."]);
            return;
        }

        if (empty($data['nome']) || empty($data['tamanho']) || !isset($data['preco'])) {
            http_response_code(400);
            echo json_encode(["error" => "Campos 'nome', 'tamanho' e 'preco' são obrigatórios."]);
            return;
        }

        $success = $this->model->update($id, $data);

        if ($success) {
            http_response_code(200);
            echo json_encode(["message" => "Roupa atualizada com sucesso."]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Falha ao atualizar roupa."]);
        }
    }

    private function deleteRoupa(int $id): void
    {
        if (!$this->model->readById($id)) {
            http_response_code(404);
            echo json_encode(["error" => "Roupa não encontrada."]);
            return;
        }

        $success = $this->model->delete($id);

        if ($success) {
            http_response_code(200);
            echo json_encode(["message" => "Roupa removida com sucesso."]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Falha ao remover roupa."]);
        }
    }
}