<?php

namespace App\Application\ORM;

use PDO;

class Database
{
    private static ?self $instance = null;

    private PDO $PDOInstance;

    /**
     * Classe Singleton
     * Constructeur privé Database
     */
    private function __construct()
    {
        $DB_NAME = $_ENV['DB_NAME'] ?? 'commejaime';
        $DB_HOST = $_ENV['DB_HOST'] ?? 'localhost';
        $DB_USER = $_ENV['DB_USER'] ?? 'root';
        $DB_PASS = $_ENV['DB_PASS'] ?? '';

        $this->PDOInstance = new PDO('mysql:dbname=' . $DB_NAME . ';host=' . $DB_HOST . ';charset=utf8', $DB_USER, $DB_PASS);
        $this->PDOInstance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $this->PDOInstance->query('SET NAMES utf8');
        $this->PDOInstance->query('SET CHARACTER SET utf8');
        $this->PDOInstance->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
    }

    /**
     * Récupère une instance de Database (Singleton)
     * @return Database static
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Récupère l'objet PDO de PHP
     * @return PDO
     */
    public function getPDO(): PDO
    {
        return $this->PDOInstance;
    }


}