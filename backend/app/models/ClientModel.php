<?php

namespace app\models;

use PDO;
use \PDOException;
use app\models\ConnectModel;

class ClientModel extends ConnectModel
{

    protected function getAllClientsOfCompany(string $company): array
    {
        try {

            $sql = $this->connect()->prepare('SELECT * FROM clients WHERE company = :company');
            $sql->bindValue(':company', $company);

            if (!$sql->execute()) {
                return ['status' => 400, 'message' => 'Não foi possivel buscar os clientes'];
            }

            $clients = $sql->fetchAll(PDO::FETCH_ASSOC);
            return ['status' => 200, 'message' => $clients ? $clients : []];
        } catch (PDOException $pe) {
            if ($pe->getCode() == 23000) {
                return ['status' => 400, 'message' => 'Não foi possivel cadastrar o cliente'];
            }
            throw new PDOException("Erro ao buscar os clientes" . $pe->getMessage());
        }
    }

    protected function getClient(array $client): array
    {
        try {
            $sql = $this->connect()->prepare('SELECT * FROM clients WHERE id = :id AND company = :company');
            $sql->bindValue(':id', $client['id']);
            $sql->bindValue(':company', $client['company']);
            if (!$sql->execute()) {
                return ['status' => 403, 'message' => 'Houve um erro ao buscar os clientes'];
            }
            $client = $sql->fetch(PDO::FETCH_ASSOC);
            return ['status' => 200, 'message' => $client];
        } catch (PDOException $pe) {
            if ($pe->getCode() == 23000) {
                return ['status' => 400, 'message' => 'Não foi possivel cadastrar o cliente'];
            }
            return throw new PDOException("Erro ao buscar o cliente" . $pe->getMessage());
        }
    }

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

            if (!$sql->execute()) {
                return ['status' => 400, 'message' => 'Não foi possivel cadastrar o cliente'];
            }
            return ['status' => 200, 'message' => 'Cadastro feito com sucesso'];
        } catch (PDOException $pe) {
            if ($pe->getCode() == 23000) {
                return ['status' => 400, 'message' => 'Não foi possivel cadastrar o cliente'];
            }
            throw new PDOException("Register client error: " . $pe->getMessage());
        }
    }

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

            if (!$sql->execute()) {
                return ['status' => 400, 'message' => 'Não foi possivel atualizar os dados do cliente'];
            }
            return ['status' => 200, 'message' => 'Dados atualizados com sucesso'];
        } catch (PDOException $pe) {
            if ($pe->getCode() == 23000) {
                return ['status' => 400, 'message' => 'Não foi possivel atualizar os dados do cliente'];
            }
            throw new PDOException('Update client error: ' . $pe->getMessage());
        }
    }

    protected function deleteClientOfCompany(int $client): array
    {
        try {
            $sql = $this->connect()->prepare('DELETE FROM clients WHERE id = :id');
            $sql->bindValue(':id', $client);
            if (!$sql->execute()) {
                return ['status' => 403, 'message' => 'Não foi possivel deletar o cliente'];
            }
            return ['status' => 200, 'message' => 'Cliente deletado'];
        } catch (PDOException $pe) {
            if ($pe->getCode() == 23000) {
                return ['status' => 400, 'message' => 'Não foi possivel cadastrar o cliente'];
            }
            throw new PDOException("delete error: " . $pe->getMessage());
        }
    }
}
