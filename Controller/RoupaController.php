<?php

namespace Controller;

use Model\RoupaModel;
use Exception;

class RoupaController
{
    private RoupaModel $roupaModel;

    public function __construct(RoupaModel $roupaModel)
    {
        $this->roupaModel = $roupaModel;
    }


    public function ProcessRequest(
        string $method,
        ?string $id
    ): void {

        header("Content-Type: application/json; charset=UTF-8");


        if ($id === null) {

            switch ($method) {

                case "GET":
                    $this->index();
                    break;

                case "POST":
                    $this->create();
                    break;

                default:
                    $this->methodNotAllowed(["GET", "POST"]);
            }

            return;
        }


        switch ($method) {

            case "GET":
                $this->show((int) $id);
                break;

            case "PATCH":
                $this->update((int) $id);
                break;

            case "DELETE":
                $this->delete((int) $id);
                break;

            default:
                $this->methodNotAllowed([
                    "GET",
                    "PATCH",
                    "DELETE"
                ]);
        }
    }


    // LISTAR
    private function index(): void
    {
        try {

            $roupas = $this->roupaModel->readAllRoupas();

            http_response_code(200);

            echo json_encode($roupas);

        } catch (Exception $error) {

            http_response_code(500);

            echo json_encode([
                "error" => $error->getMessage()
            ]);
        }
    }


    // CADASTRAR
    private function create(): void
    {
        $data = $this->readInput();

        $errors = $this->validate($data);

        if (!empty($errors)) {

            http_response_code(422);

            echo json_encode([
                "errors" => $errors
            ]);

            return;
        }


        try {

            $id = $this->roupaModel->createRoupa(
                $data["nome"],
                $data["categoria"],
                $data["tamanho"],
                $data["cor"],
                (float) $data["preco"],
                (int) $data["quantidade"]
            );


            $roupa = $this->roupaModel->readRoupa($id);

            http_response_code(201);

            echo json_encode($roupa);

        } catch (Exception $error) {

            http_response_code(500);

            echo json_encode([
                "error" => $error->getMessage()
            ]);
        }
    }


    // BUSCAR POR ID
    private function show(int $id): void
    {
        try {

            $roupa = $this->roupaModel->readRoupa($id);

            if ($roupa === null) {

                http_response_code(404);

                echo json_encode([
                    "error" => "Roupa não encontrada!"
                ]);

                return;
            }

            http_response_code(200);

            echo json_encode($roupa);

        } catch (Exception $error) {

            http_response_code(500);

            echo json_encode([
                "error" => $error->getMessage()
            ]);
        }
    }


    // ATUALIZAR
    private function update(int $id): void
    {
        try {

            $roupa = $this->roupaModel->readRoupa($id);

            if ($roupa === null) {

                http_response_code(404);

                echo json_encode([
                    "error" => "Roupa não encontrada!"
                ]);

                return;
            }


            $data = $this->readInput();


            $nome = $data["nome"] ?? $roupa["nome"];

            $categoria =
                $data["categoria"] ??
                $roupa["categoria"];

            $tamanho =
                $data["tamanho"] ??
                $roupa["tamanho"];

            $cor =
                $data["cor"] ??
                $roupa["cor"];

            $preco =
                $data["preco"] ??
                $roupa["preco"];

            $quantidade =
                $data["quantidade"] ??
                $roupa["quantidade"];


            $errors = $this->validate([
                "nome" => $nome,
                "categoria" => $categoria,
                "tamanho" => $tamanho,
                "cor" => $cor,
                "preco" => $preco,
                "quantidade" => $quantidade
            ]);


            if (!empty($errors)) {

                http_response_code(422);

                echo json_encode([
                    "errors" => $errors
                ]);

                return;
            }


            $this->roupaModel->updateRoupa(
                $id,
                $nome,
                $categoria,
                $tamanho,
                $cor,
                (float) $preco,
                (int) $quantidade
            );


            $updated =
                $this->roupaModel->readRoupa($id);


            http_response_code(200);

            echo json_encode($updated);

        } catch (Exception $error) {

            http_response_code(500);

            echo json_encode([
                "error" => $error->getMessage()
            ]);
        }
    }


    // EXCLUIR
    private function delete(int $id): void
    {
        try {

            $roupa = $this->roupaModel->readRoupa($id);

            if ($roupa === null) {

                http_response_code(404);

                echo json_encode([
                    "error" => "Roupa não encontrada!"
                ]);

                return;
            }


            $this->roupaModel->deleteRoupa($id);

            http_response_code(204);

        } catch (Exception $error) {

            http_response_code(500);

            echo json_encode([
                "error" => $error->getMessage()
            ]);
        }
    }


    // LER JSON
    private function readInput(): array
    {
        $body = file_get_contents("php://input");

        $data = json_decode($body, true);

        return is_array($data) ? $data : [];
    }


    // VALIDAÇÃO
    private function validate(array $data): array
    {
        $errors = [];


        if (empty($data["nome"])) {

            $errors[] =
                "O campo 'nome' é obrigatório.";
        }


        if (empty($data["categoria"])) {

            $errors[] =
                "O campo 'categoria' é obrigatório.";
        }


        if (empty($data["tamanho"])) {

            $errors[] =
                "O campo 'tamanho' é obrigatório.";
        }


        if (empty($data["cor"])) {

            $errors[] =
                "O campo 'cor' é obrigatório.";
        }


        if (
            !isset($data["preco"]) ||
            !is_numeric($data["preco"])
        ) {

            $errors[] =
                "O campo 'preco' deve ser numérico.";
        }


        if (
            !isset($data["quantidade"]) ||
            !is_numeric($data["quantidade"]) ||
            (int) $data["quantidade"] < 0
        ) {

            $errors[] =
                "O campo 'quantidade' deve ser um número maior ou igual a zero.";
        }


        return $errors;
    }


    private function methodNotAllowed(
        array $allowed
    ): void {

        header(
            "Allow: " .
            implode(", ", $allowed)
        );

        http_response_code(405);

        echo json_encode([
            "error" => "Método não permitido."
        ]);
    }
}