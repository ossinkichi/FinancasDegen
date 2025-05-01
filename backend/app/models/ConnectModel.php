<?php

namespace App\models;

use Exception;
use \PDOException;
use App\Concern\InteractsWithDatabase;

class ConnectModel
{
    use InteractsWithDatabase;

    private function plansTable(): void
    {
        try {
            $database = $this->connect();
            $sql = $database->prepare('CREATE TABLE IF NOT EXISTS plans(
            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
            name VARCHAR(200) NOT NULL,
            describe TEXT,
            numberofusers INTEGER DEFAULT 5,
            numberofclients INTEGER DEFAULT 25,
            price DECIMAL(10,2) NOT NULL,
            status BOOLEAN DEFAULT true,
            type VARCHAR(6) CHECK(type IN (\'anual\', \'mensal\'))
            );');
            $sql->execute();
        } catch (PDOException $pe) {
            throw new PDOException('Erro ao criar a tabela de planos: ' . $pe->getMessage(), $pe->getCode());
        }
    }

    private function promotionPlansTable(): void
    {
        try {
            $sql = $this->connect()->prepare('CREATE TABLE IF NOT EXISTS promotionplans(
            id INTEGER PRIMARY KEY AUTOINCREMENT ,
            plan INTEGER,
            price DECIMAL(10,2) NOT NULL,
            dateofexpired DATE NOT NULL,
            status BOOLEAN DEFAULT true,
            FOREIGN KEY (plan) REFERENCES plans(id) ON DELETE CASCADE)');
        } catch (PDOException $pe) {
            throw new PDOException('Error ao criar a tabela de planos promocionais: ' . $pe->getMessage(), $pe->getCode());
        }
    }

    private function usersTable(): void
    {
        try {
            $database = $this->connect();
            $sql = $database->prepare('CREATE TABLE IF NOT EXISTS users(
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            userhash VARCHAR(64) UNIQUE,
            position VARCHAR(14) CHECK(position IN(\'administrador\', \'funcionario\')) DEFAULT \'funcionario\',
            name VARCHAR(340) NOT NULL,
            email VARCHAR(220) UNIQUE NOT NULL,
            emailverify BOOL DEFAULT false,
            password VARCHAR(130) NOT NULL,
            cpf VARCHAR(11) UNIQUE NOT NULL,
            createdaccount DATETIME DEFAULT CURRENT_TIMESTAMP,
            dateofbirth DATE,
            gender VARCHAR(10),
            phone VARCHAR(20),
            company VARCHAR(14),
            FOREIGN KEY (company)  REFERENCES companies(cnpj) ON DELETE CASCADE
            );');
            $sql->execute();
        } catch (PDOException $pe) {
            throw new PDOException("Erro ao criar a tabela de usuário: " . $pe->getMessage(), $pe->getCode());
        }
    }

    private function companyTable(): void
    {
        try {
            $database = $this->connect();
            $sql = $database->prepare('CREATE TABLE IF NOT EXISTS companies(
            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
            name VARCHAR(200) NOT NULL,
            describe TEXT,
            cnpj VARCHAR(14) UNIQUE NOT NULL,
            plan INTEGER,
            status BOOLEAN DEFAULT false,
            FOREIGN KEY (plan) REFERENCES plans(id)
            );');
            $sql->execute();
        } catch (PDOException $pe) {
            throw new PDOException('Erro ao criar a tabela das empresas: ' . $pe->getMessage());
        }
    }

    private function clientsTable(): void
    {
        try {
            $database = $this->connect();
            $sql = $database->prepare('CREATE TABLE IF NOT EXISTS clients(
            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
            name VARCHAR(350) NOT NULL,
            email VARCHAR(220) NOT NULL,
            phone VARCHAR(20) NOT NULL,
            shippingaddress VARCHAR(220),
            billingaddress VARCHAR(220),
            gender TEXT,
            company VARCHAR(14),
            deleted BOOLEAN DEFAULT false,
            FOREIGN KEY (company) REFERENCES companies(cnpj) ON DELETE CASCADE
            );');

            $sql->execute();
        } catch (PDOException $pe) {
            throw new PDOException('Error ao criar a tabela de clientes: ' . $pe->getMessage(), $pe->getCode());
        }
    }

    private function requestTable(): void
    {
        try {
            $sql = $this->connect()->prepare('CREATE TABLE IF NOT EXISTS requests(
            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
            client INTEGER,
            title VARCHAR(200) NOT NULL,
            describe TEXT,
            price DECIMAL(10,2) NOT NULL,
            numberofinstallments INTEGER DEFAULT 1,
            installmentspaid INTEGER DEFAULT 0,
            status VARCHAR(20) DEFAULT \'pendente\',
            fees VARCHAR(10),
            dateofrequest DATE default CURRENT_TIMESTAMP,
            dateofpayment DATE,
            deleted BOOLEAN DEFAULT false,
            FOREIGN KEY (client) REFERENCES clients(id) ON DELETE CASCADE
            );');
            $sql->execute();
        } catch (PDOException $pe) {
            throw new PDOException('Erro ao criar a tabela de contas: ' . $pe->getMessage(), $pe->getCode());
        }
    }

    private function ticketTable(): void
    {
        try {
            $sql = $this->connect()->prepare('CREATE TABLE IF NOT EXISTS ticket(
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            request INTEGER,
            price DECIMAL(10,2) NOT NULL,
            numberofinstallment INTEGER,
            dateofpayment DATE,
            paid BOOLEAN,
            fees DECIMAL(10,2),
            FOREIGN KEY (request) REFERENCES requests(id) ON DELETE CASCADE
            )');
            $sql->execute();
        } catch (PDOException $pe) {
            throw new PDOException('Erro ao criar a tabela de boletos: ' . $pe->getMessage(), $pe->getCode());
        }
    }

    private function accordsTable(): void
    {
        try {
            $sql = $this->connect()->prepare('CREATE TABLE IF NOT EXISTS accords(
            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
            client INTEGER,
            price DECIMAL(10,2) NOT NULL,
            numberofinstallments INTEGER DEFAULT 1,
            installmentspaid INTEGER DEFAULT 0,
            status VARCHAR(20) DEFAULT \'pendente\',
            fees VARCHAR(10),
            requests TEXT NOT NULL,
            tickets TEXT NOT NULL,
            deleted BOOLEAN DEFAULT false,
            FOREIGN KEY (client) REFERENCES clients(id) ON DELETE CASCADE
            );');

            $sql->execute();
        } catch (PDOException $pe) {
            throw new PDOException('RequestTable error: ' . $pe->getMessage());
        }
    }

    private function expensesTable(): void
    {
        try {
            $sql = $this->connect()->prepare('CREATE TABLE IF NOT EXISTS expenses(
            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
            name VARCHAR(200) NOT NULL,
            describe TEXT,
            price DECIMAL(10,2) NOT NULL,
            dateofexpense DATE NOT NULL,
            status BOOLEAN DEFAULT false,
            company VARCHAR(14),
            FOREIGN KEY (company) REFERENCES companies(cnpj) ON DELETE CASCADE
            );');
            $sql->execute();
        } catch (PDOException $pe) {
            throw new PDOException('Erro ao criar a tabela de despesas: ' . $pe->getMessage(), $pe->getCode());
        }
    }

    protected function createTables(): void
    {
        try {
            $this->usersTable();
            $this->companyTable();
            $this->clientsTable();
            $this->plansTable();
            $this->requestTable();
            $this->ticketTable();
            $this->promotionPlansTable();
            $this->accordsTable();
            $this->expensesTable();
        } catch (Exception $e) {
            throw new Exception('Error ao criar as tabelas: ' . $e->getMessage(), $e->getCode());
        }
    }
}
