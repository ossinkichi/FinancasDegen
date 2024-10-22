<?php

namespace app\models;

use PDO;
use \PDOException;
use app\models\ConnectModel;

class ClientModel extends ConnectModel
{

    private $db;

    public function __construct()
    {
        $this->db = $this->connect();
    }

    protected function getAllClientsOfCompany(int $company): array
    {
        $clients = [];
        try {

            $sql = $this->db->prepare('SELECT * FROM clients WHERE company = :company');
            $sql->bindValue(':company', $company);
            $sql->execute();
            $clients = $sql->fetchAll(PDO::FETCH_ASSOC);

            return $clients;
        } catch (PDOException $pe) {
            throw new PDOException("Erro ao buscar os clientes" . $pe->getMessage());
        }
    }

    protected static function getClientData($client)
    {
        // try{

        // }catch(PDOException $pe){
        //   return throw new PDOException("Erro ao buscar o cliente $pe->getMessage()");
        // }
    }

    protected static function setNewClientOfCompany(array $clientData) {}

    protected static function updateDataClientOfCompany(array $clientData) {}

    protected static function deleteClientOfCompany(int $client) {}
}
