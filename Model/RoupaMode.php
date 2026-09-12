<?php

namespace Model;

use PDO;
use PDOException;
use Exception;

class RoupaModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Connection::getInstance();
    }

    // CADASTRAR ROUPA
    public function createRoupa(
        string $nome,
        string $categoria,
        string $tamanho,
        string $cor,
        float $preco,
        int $quantidade
    ): int {

        try {

            $sql = "
                INSERT INTO roupas
                (nome, categoria, tamanho, cor, preco, quantidade)
                VALUES
                (:nome, :categoria, :tamanho, :cor, :preco, :quantidade)
            ";

            $stmt = $this->db->prepare($sql);

            $stmt->bindValue(":nome", $nome);
            $stmt->bindValue(":categoria", $categoria);
            $stmt->bindValue(":tamanho", $tamanho);
            $stmt->bindValue(":cor", $cor);
            $stmt->bindValue(":preco", $preco);
            $stmt->bindValue(":quantidade", $quantidade, PDO::PARAM_INT);

            $stmt->execute();

            return (int) $this->db->lastInsertId("roupas_id_seq");

        } catch (PDOException $error) {

            error_log($error->getMessage());

            throw new Exception("Erro ao cadastrar roupa.");
        }
    }


    // BUSCAR UMA ROUPA
    public function readRoupa(int $id): ?array
    {
        try {

            $sql = "
                SELECT
                    id,
                    nome,
                    categoria,
                    tamanho,
                    cor,
                    preco,
                    quantidade
                FROM roupas
                WHERE id = :id
            ";

            $stmt = $this->db->prepare($sql);

            $stmt->bindValue(":id", $id, PDO::PARAM_INT);

            $stmt->execute();

            $roupa = $stmt->fetch();

            return $roupa ?: null;

        } catch (PDOException $error) {

            error_log($error->getMessage());

            throw new Exception("Erro ao buscar roupa.");
        }
    }


    // LISTAR TODAS AS ROUPAS
    public function readAllRoupas(): array
    {
        try {

            $sql = "
                SELECT
                    id,
                    nome,
                    categoria,
                    tamanho,
                    cor,
                    preco,
                    quantidade
                FROM roupas
                ORDER BY id
            ";

            $stmt = $this->db->query($sql);

            return $stmt->fetchAll();

        } catch (PDOException $error) {

            error_log($error->getMessage());

            throw new Exception("Erro ao listar roupas.");
        }
    }


    // ATUALIZAR ROUPA
    public function updateRoupa(
        int $id,
        string $nome,
        string $categoria,
        string $tamanho,
        string $cor,
        float $preco,
        int $quantidade
    ): bool {

        try {

            $sql = "
                UPDATE roupas
                SET
                    nome = :nome,
                    categoria = :categoria,
                    tamanho = :tamanho,
                    cor = :cor,
                    preco = :preco,
                    quantidade = :quantidade
                WHERE id = :id
            ";

            $stmt = $this->db->prepare($sql);

            $stmt->bindValue(":id", $id, PDO::PARAM_INT);
            $stmt->bindValue(":nome", $nome);
            $stmt->bindValue(":categoria", $categoria);
            $stmt->bindValue(":tamanho", $tamanho);
            $stmt->bindValue(":cor", $cor);
            $stmt->bindValue(":preco", $preco);
            $stmt->bindValue(":quantidade", $quantidade, PDO::PARAM_INT);

            $stmt->execute();

            return $stmt->rowCount() > 0;

        } catch (PDOException $error) {

            error_log($error->getMessage());

            throw new Exception("Erro ao atualizar roupa.");
        }
    }


    // EXCLUIR ROUPA
    public function deleteRoupa(int $id): bool
    {
        try {

            $sql = "DELETE FROM roupas WHERE id = :id";

            $stmt = $this->db->prepare($sql);

            $stmt->bindValue(":id", $id, PDO::PARAM_INT);

            $stmt->execute();

            return $stmt->rowCount() > 0;

        } catch (PDOException $error) {

            error_log($error->getMessage());

            throw new Exception("Erro ao excluir roupa.");
        }
    }
}