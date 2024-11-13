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
            return throw new PDOException("Erro ao buscar o cliente" . $pe->getMessage());
        }
    }

    protected function setNewClientOfCompany(array $clientData)
    {
        try {
            $sql = $this->connect()->prepare('INSERT INTO 
            clients(name, email, phone, shippingaddress, billingaddress, company) 
            VALUES(:name, :email, :phone, :shippingaddress, :billingaddress, :company)');

            foreach ($clientData as $key => $value) {
                $sql->bindValue(':' . $key, $value);
            }
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

    protected function updateDataClientOfCompany(array $clientData) {}

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
            throw new PDOException("delete error: " . $pe->getMessage());
        }
    }
}
