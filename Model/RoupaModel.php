<?php
namespace Model;

use Config\Connection;
use PDO;

class RoupaModel 
{
    private $conn;

    public function __construct() 
    {
        $this->conn = Connection::getInstance();
    }

    public function getAll() 
    {
        $stmt = $this->conn->prepare("SELECT * FROM roupas");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) 
    {
        $stmt = $this->conn->prepare("INSERT INTO roupas (nome, tamanho, preco) VALUES (:nome, :tamanho, :preco)");
        $stmt->bindValue(':nome', $data['nome']);
        $stmt->bindValue(':tamanho', $data['tamanho']);
        $stmt->bindValue(':preco', $data['preco']);
        return $stmt->execute();
    }
}