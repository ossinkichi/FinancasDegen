<?php

namespace app\models;

use PDO;
use \PDOException;
use app\models\ConnectModel;

class ClientModel extends ConnectModel
{

    /**
     * @return array {status: number, message: array|string}
     */
    protected function getAllClientsOfCompany(string $company): array
    {
        try {
            $sql = $this->connect()->prepare('SELECT * FROM clients WHERE company = :company');
            $sql->bindValue(':company', $company);
            $sql->execute();

            if ($sql->rowCount() == 0) {
                return ['status' => 400, 'message' => 'Não foi possivel buscar os clientes', 'error' => $sql->errorInfo()];
            }
            return ['status' => 200, 'message' => $sql->fetchAll(PDO::FETCH_ASSOC) ?? []];
        } catch (PDOException $pe) {
            if ($pe->getCode() == 23000) {
                return ['status' => 400, 'message' => 'Não foi possivel cadastrar o cliente'];
            }
            throw new PDOException("Erro ao buscar os clientes" . $pe->getMessage());
        }
    }

    /**
     * @return array {status: number, message: array|string}
     */
    protected function getClient(array $client): array
    {
        try {
            $sql = $this->connect()->prepare('SELECT * FROM clients WHERE id = :id AND company = :company');
            $sql->bindValue(':id', $client['id']);
            $sql->bindValue(':company', $client['company']);
            $sql->execute();
            if ($sql->rowCount() == 0) {
                return ['status' => 403, 'message' => 'Houve um erro ao buscar os clientes', 'error' => $sql->errorInfo()];
            }

            return ['status' => 200, 'message' => $sql->fetch(PDO::FETCH_ASSOC) ?? []];
        } catch (PDOException $pe) {
            if ($pe->getCode() == 23000) {
                return ['status' => 400, 'message' => 'Não foi possivel cadastrar o cliente'];
            }
            return throw new PDOException("Erro ao buscar o cliente" . $pe->getMessage());
        }
    }

    /**
     * @return array {status: number, message: array|string}
     */
    protected function setNewClientOfCompany(
        string $company,
        string $name,
        string $email,
        string $phone,
        string $gender,
        string $shippingaddress,
        string $billingaddress
    ): array {
        try {
            $sql = $this->connect()->prepare('INSERT INTO
            clients(name, email, phone, gender,shippingaddress, billingaddress, company)
            VALUES(:name, :email, :phone, :gender,:shippingaddress, :billingaddress, :company)');
            $sql->bindValue(':name', $name);
            $sql->bindValue(':email', $email);
            $sql->bindValue(':phone', $phone);
            $sql->bindValue(':gender', $gender);
            $sql->bindValue(':shippingaddress', $shippingaddress);
            $sql->bindValue(':billingaddress', $billingaddress);
            $sql->bindValue(':company', $company);
            $sql->execute();

            if ($sql->rowCount() == 0) {
                return ['status' => 400, 'message' => 'Não foi possivel cadastrar o cliente', 'error' => $sql->errorInfo()];
            }
            return ['status' => 201, 'message' => ''];
        } catch (PDOException $pe) {
            if ($pe->getCode() == 23000) {
                return ['status' => 400, 'message' => 'Não foi possivel cadastrar o cliente'];
            }
            throw new PDOException("Register client error: " . $pe->getMessage());
        }
    }

    /**
     * @return array {status: number, message: string|void}
     */
    protected function updateDataClientOfCompany(
        int $id,
        string $name,
        string $email,
        string $phone,
        string $gender,
        string $shippingaddress,
        string $billingaddress
    ): array {
        try {
            $sql = $this->connect()->prepare('UPDATE clients SET name = :name, email = :email, phone = :phone, gender = :gender, shippingaddress = :shippingaddress, billingaddress = :billingaddress WHERE id = :id');
            $sql->bindValue(':id', $id);
            $sql->bindValue(':name', $name);
            $sql->bindValue(':email', $email);
            $sql->bindValue(':phone', $phone);
            $sql->bindValue(':gender', $gender);
            $sql->bindValue(':shippingaddress', $shippingaddress);
            $sql->bindValue(':billingaddress', $billingaddress);
            $sql->execute();

            if ($sql->rowCount() == 0) {
                return ['status' => 400, 'message' => 'Não foi possivel atualizar os dados do cliente', 'error' => $sql->errorInfo()];
            }
            return ['status' => 201, 'message' => ''];
        } catch (PDOException $pe) {
            if ($pe->getCode() == 23000) {
                return ['status' => 400, 'message' => 'Não foi possivel atualizar os dados do cliente'];
            }
            throw new PDOException('Update client error: ' . $pe->getMessage());
        }
    }

    /**
     * @return array {status: number, message: string|void}
     */
    protected function deleteClientOfCompany(int $client): array
    {
        try {
            $sql = $this->connect()->prepare('DELETE FROM clients WHERE id = :id');
            $sql->bindValue(':id', $client);
            $sql->execute();

            if ($sql->rowCount() == 0) {
                return ['status' => 403, 'message' => 'Não foi possivel deletar o cliente', 'error' => $sql->errorInfo()];
            }
            return ['status' => 201, 'message' => ''];
        } catch (PDOException $pe) {
            if ($pe->getCode() == 23000) {
                return ['status' => 400, 'message' => 'Não foi possivel deletar o cliente'];
            }
            throw new PDOException("Erro ao deletar: " . $pe->getMessage(), $pe->getCode());
        }
    }
}
