<?php

namespace Model;

use Config\Configuration;
use PDO;
use PDOException;

class Connection
{
    private static ?PDO $connection = null;

    public static function getConnection(): PDO
    {
        if (self::$connection === null) {

            $config = Configuration::getDatabaseConfig();

            $dsn = "mysql:host={$config['host']};"
                . "port={$config['port']};"
                . "dbname={$config['database']};"
                . "charset=utf8mb4";

            try {
                self::$connection = new PDO(
                    $dsn,
                    $config['username'],
                    $config['password']
                );

                self::$connection->setAttribute(
                    PDO::ATTR_ERRMODE,
                    PDO::ERRMODE_EXCEPTION
                );

                self::$connection->setAttribute(
                    PDO::ATTR_DEFAULT_FETCH_MODE,
                    PDO::FETCH_ASSOC
                );

            } catch (PDOException $e) {

                http_response_code(500);

                die(json_encode([
                    'erro' => 'Erro ao conectar com o banco de dados'
                ]));
            }
        }

        return self::$connection;
    }
}
