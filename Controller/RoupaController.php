<?php

namespace Controller;

use Model\RoupaModel;
use OpenApi\Attributes as OA;

#[OA\Info(title: "API Cadastro de Roupa", version: "1.0.0")]
#[OA\Server(url: "http://localhost:8000")]
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
            case 'PATCH':
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

    #[OA\Get(
        path: "/roupas",
        summary: "Lista todas as roupas",
        responses: [
            new OA\Response(response: 200, description: "Lista retornada com sucesso")
        ]
    )]
    private function getAllRoupas(): void
    {
        $roupas = $this->model->readAllRoupas();
        http_response_code(200);
        echo json_encode($roupas);
    }

    #[OA\Get(
        path: "/roupas/{id}",
        summary: "Busca roupa por ID",
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Roupa encontrada"),
            new OA\Response(response: 404, description: "Roupa não encontrada")
        ]
    )]
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

    #[OA\Post(
        path: "/roupas",
        summary: "Cadastra uma nova roupa",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "nome", type: "string"),
                    new OA\Property(property: "categoria", type: "string"),
                    new OA\Property(property: "tamanho", type: "string"),
                    new OA\Property(property: "cor", type: "string"),
                    new OA\Property(property: "preco", type: "number"),
                    new OA\Property(property: "quantidade", type: "integer")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Roupa cadastrada com sucesso"),
            new OA\Response(response: 400, description: "Campos obrigatórios ausentes")
        ]
    )]
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

    #[OA\Put(
        path: "/roupas/{id}",
        summary: "Atualiza uma roupa existente",
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Roupa atualizada com sucesso"),
            new OA\Response(response: 404, description: "Roupa não encontrada")
        ]
    )]
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

    #[OA\Delete(
        path: "/roupas/{id}",
        summary: "Remove uma roupa",
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Roupa removida com sucesso"),
            new OA\Response(response: 404, description: "Roupa não encontrada")
        ]
    )]
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