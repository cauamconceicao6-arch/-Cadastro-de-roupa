<?php

namespace Model;

require_once __DIR__ . "/../Config/Configuration.php";

use PDO;
use PDOException;

class Connection
{
    private static ?PDO $connection = null;

    public static function getInstance(): PDO
    {
        if (self::$connection === null) {

            try {

                self::$connection = new PDO(
                    "pgsql:host=" . DB_HOST .
                    ";port=" . DB_PORT .
                    ";dbname=" . DB_NAME .
                    ";sslmode=require",

                    DB_USER,
                    DB_PASSWORD
                );

                self::$connection->setAttribute(
                    PDO::ATTR_ERRMODE,
                    PDO::ERRMODE_EXCEPTION
                );

                self::$connection->setAttribute(
                    PDO::ATTR_DEFAULT_FETCH_MODE,
                    PDO::FETCH_ASSOC
                );

            } catch (PDOException $error) {

                http_response_code(500);

                echo json_encode([
                    "error" => "Erro ao conectar com o banco de dados."
                ]);

                exit;
            }
        }

        return self::$connection;
    }
}