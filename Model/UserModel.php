<?php

namespace Model;

use PDO;

class RoupaModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Connection::getConnection();
    }

    // Listar todas as roupas
    public function listar(): array
    {
        $sql = "SELECT * FROM roupas ORDER BY id DESC";

        $stmt = $this->db->query($sql);

        return $stmt->fetchAll();
    }

    // Buscar roupa pelo ID
    public function buscarPorId(int $id): ?array
    {
        $sql = "SELECT * FROM roupas WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':id' => $id
        ]);

        $roupa = $stmt->fetch();

        return $roupa ?: null;
    }

    // Cadastrar roupa
    public function cadastrar(array $dados): int
    {
        $sql = "
            INSERT INTO roupas
            (nome, categoria, tamanho, cor, preco, quantidade)
            VALUES
            (:nome, :categoria, :tamanho, :cor, :preco, :quantidade)
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':nome' => $dados['nome'],
            ':categoria' => $dados['categoria'],
            ':tamanho' => $dados['tamanho'],
            ':cor' => $dados['cor'],
            ':preco' => $dados['preco'],
            ':quantidade' => $dados['quantidade']
        ]);

        return (int) $this->db->lastInsertId();
    }

    // Atualizar roupa
    public function atualizar(int $id, array $dados): bool
    {
        $sql = "
            UPDATE roupas SET
                nome = :nome,
                categoria = :categoria,
                tamanho = :tamanho,
                cor = :cor,
                preco = :preco,
                quantidade = :quantidade
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':id' => $id,
            ':nome' => $dados['nome'],
            ':categoria' => $dados['categoria'],
            ':tamanho' => $dados['tamanho'],
            ':cor' => $dados['cor'],
            ':preco' => $dados['preco'],
            ':quantidade' => $dados['quantidade']
        ]);
    }

    // Excluir roupa
    public function excluir(int $id): bool
    {
        $sql = "DELETE FROM roupas WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':id' => $id
        ]);
    }
}
