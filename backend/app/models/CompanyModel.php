<?php

namespace app\models;

use app\models\ConnectModel;
use \PDO;
use PDOException;

class CompanyModel extends ConnectModel
{

    private object $db;

    public function __construct()
    {
        $this->db = $this->connect();
    }

    protected function getCompany(int $id): array
    {
        $data = [];
        try {
            $sql = $this->db->prepare('SELECT * FROM companies WHERE  id = :id');
            $sql->bindParam(':id', $id);
            $sql->execute();
            $data = $sql->fetch(PDO::FETCH_ASSOC);


            return $data;
        } catch (PDOException $pe) {
            throw new PDOException('GetCompany error: ' . $pe->getMessage());
        }
    }

    protected function setNewCompany(array $companyData)
    {
        try {
            $sql = $this->db->prepare('
                INSERT INTO companies (companyname, companydescribe, cnpj, plan) 
                VALUES (:companyname, :companydescribe, :cnpj, :plan)
            ');

            foreach ($companyData as $key => $value) {
                $sql->bindValue(':' . $key, $value);
            }

            $sql->execute();
        } catch (PDOException $pe) {
            throw new PDOException('SetNewCompany error: ' . $pe->getMessage());
        }
    }

    protected function updateTheCompanysPlan(array $companyData)
    {
        try {
            $sql  = $this->db->prepare('UPDATE companies SET plan = :plan WHERE cnpj = :cnpj');

            foreach ($companyData as $key => $value) {
                $sql->bindValue(':' . $key, $value);
            }

            $sql->execute();
        } catch (PDOException $pe) {
            throw new PDOException('UpdateTheCompanysPlan error: ' . $pe->getMessage());
        }
    }

    protected function deleteCompany(string $cnpj)
    {
        try {
            $sql = $this->db->prepare('DELETE FROM companies WHERE cnpj = :cnpj');
            $sql->bindValue(':cnpj', $cnpj);
            $sql->execute();
        } catch (PDOException $pe) {
            throw new PDOException('DeleteCompany error: ' . $pe->getMessage());
        }
    }
}
