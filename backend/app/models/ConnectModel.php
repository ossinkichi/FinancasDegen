<?php

namespace app\models;

use \PDO;
use \PDOException;
use Dotenv\Dotenv;

class ConnectModel
{

    public function connect()
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

    protected function usersTable()
    {
        try {
            $database = $this->connect();
            $sql = $database->prepare('CREATE TABLE IF NOT EXISTS users(
            id INTEGER PRIMARY KEY AUTOINCREMENT, 
            userhash VARCHAR(64) UNIQUE, 
            type VARCHAR(14) CHECK(type IN(\'administrador\', \'funcionario\')) DEFAULT \'funcionario\',
            name VARCHAR(340) NOT NULL, 
            email VARCHAR(220) UNIQUE NOT NULL, 
            emailverify BOOL DEFAULT false, 
            password VARCHAR(130) NOT NULL, 
            cpf VARCHAR(11) UNIQUE NOT NULL, 
            active BOOL DEFAULT false, 
            createdaccount DATETIME DEFAULT CURRENT_TIMESTAMP, 
            dateofbirth DATE, 
            gender VARCHAR(10), 
            phone INTEGER(20), 
            company INTEGER,
            FOREIGN KEY (company)  REFERENCES companies(id) ON DELETE CASCADE
            );');

            $sql->execute();
        } catch (PDOException $pe) {
            throw new PDOException("Users Error: " . $pe->getMessage());
        }
    }

    protected function companyTable()
    {
        try {
            $database = $this->connect();
            $sql = $database->prepare('CREATE TABLE IF NOT EXISTS companies(
            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 
            companyname VARCHAR(200) NOT NULL, 
            companydescribe TEXT,
            cnpj VARCHAR(14) UNIQUE NOT NULL, 
            plan INTEGER,
            FOREIGN KEY (plan) REFERENCES plans(id)
            );');
            $sql->execute();
        } catch (PDOException $pe) {
            throw new PDOException('CompanyTable error: ' . $pe->getMessage());
        }
    }

    protected function clientsTable()
    {
        try {
            $database = $this->connect();
            $sql = $database->prepare('CREATE TABLE IF NOT EXISTS clients(
            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 
            name VARCHAR(350) NOT NULL, 
            email VARCHAR(220) NOT NULL, 
            phone INTEGER(11) NOT NULL, 
            shippingaddress VARCHAR(220), 
            billingaddress VARCHAR(220), 
            company INTEGER,
            FOREIGN KEY (company) REFERENCES company(id) ON DELETE CASCADE
            );');

            $sql->execute();
        } catch (PDOException $pe) {
            throw new PDOException('ClientsTable error: ' . $pe->getMessage());
        }
    }

    protected function plansTable()
    {
        try {
            $database = $this->connect();
            $sql = $database->prepare('CREATE TABLE IF NOT EXISTS plans(
            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 
            planname VARCHAR(200) NOT NULL, 
            plandescribe TEXT, 
            numberofusers INTEGER DEFAULT 5,
            numberofclients INTEGER DEFAULT 25,
            price VARCHAR(10),
            type VARCHAR(6) CHECK(type IN (\'anual\', \'mensal\')),
            promotionprice VARCHAR(10),
            );');

            $sql->execute();
        } catch (PDOException $pe) {
            throw new PDOException('PlainsTable error ' . $pe->getMessage());
        }
    }

    protected function requestTable()
    {
        try {
            $sql = $this->connect();
            $database = $this->connect();

            $sql = $database->prepare('CREATE TABLE IF NOT EXISTS requests(
            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 
            client INTEGER,
            price VARCHAR(10) NOT NULL,
            numberofinstallments INTEGER DEFAULT 1,
            installmentspaid INTEGER DEFAULT 0,
            status VARCHAR(20) DEFAULT \'pendente\',
            FOREIGN KEY (client) REFERENCES clients(id) ON DELETE CASCADE
            );');
            $sql->execute();
        } catch (PDOException $pe) {
            throw new PDOException('RequestTable error: ' . $pe->getMessage());
        }
    }
}
