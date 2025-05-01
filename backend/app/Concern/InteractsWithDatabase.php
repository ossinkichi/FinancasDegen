<?php

namespace App\Concern;

use PDO;
use PDOException;
use Dotenv\Dotenv;

trait InteractsWithDatabase
{

    protected function connect(): PDO
    {
        try {

            $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
            $dotenv->load();

            if (!defined('DATABASE')) define('DATABASE', __DIR__ . '/' . $_ENV['DATABASE']);

            $db = new PDO('sqlite:' . DATABASE);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $db->exec('PRAGMA foreign_keys = ON;');
            return $db;
        } catch (PDOException $pe) {
            throw new PDOException('Error ao conectar: ' . $pe->getMessage());
        }
    }
}
