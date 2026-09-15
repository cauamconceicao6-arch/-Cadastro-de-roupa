<?php

namespace Model;

use Config\Connection;
use PDO;

class RoupaModel 
{
    private PDO $conn;

    public function __construct() 
    {
        $this->conn = Connection::getInstance();
    }

    public function readAllRoupas(): array
    {
        $stmt = $this->conn->prepare("SELECT * FROM roupas");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readById(int $id): array|false
    {
        $stmt = $this->conn->prepare("SELECT * FROM roupas WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create(array $data): bool
    {
        $stmt = $this->conn->prepare("INSERT INTO roupas (nome, categoria, tamanho, cor, preco, quantidade) VALUES (:nome, :categoria, :tamanho, :cor, :preco, :quantidade)");
        $stmt->bindValue(':nome', $data['nome']);
        $stmt->bindValue(':categoria', $data['categoria']);
        $stmt->bindValue(':tamanho', $data['tamanho']);
        $stmt->bindValue(':cor', $data['cor']);
        $stmt->bindValue(':preco', $data['preco']);
        $stmt->bindValue(':quantidade', $data['quantidade']);
        return $stmt->execute();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->conn->prepare("UPDATE roupas SET nome = :nome, categoria = :categoria, tamanho = :tamanho, cor = :cor, preco = :preco, quantidade = :quantidade WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':nome', $data['nome']);
        $stmt->bindValue(':categoria', $data['categoria']);
        $stmt->bindValue(':tamanho', $data['tamanho']);
        $stmt->bindValue(':cor', $data['cor']);
        $stmt->bindValue(':preco', $data['preco']);
        $stmt->bindValue(':quantidade', $data['quantidade']);
        return $stmt->execute();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->conn->prepare("DELETE FROM roupas WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}