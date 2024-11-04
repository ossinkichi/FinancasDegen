<?php

namespace app\models;

use app\models\ConnectModel;
use \PDO;
use PDOException;

class CompanyModel extends ConnectModel
{

    protected function getAllCompanies(): array
    {
        try {
            $sql = $this->connect()->prepare('SELECT * FROM companies');
            $sql->execute();

            $companies = $sql->fetchAll(PDO::FETCH_ASSOC);

            return $companies ?? [];
        } catch (PDOException $pe) {
            throw new PDOException('getAllCompanies error: ' . $pe->getMessage());
        }
    }

    protected function getCompany(int $id): array
    {
        $data = [];
        try {
            $sql = $this->connect()->prepare('SELECT * FROM companies WHERE  id = :id');
            $sql->bindParam(':id', $id);
            $sql->execute();
            $data = $sql->fetch(PDO::FETCH_ASSOC);


            return $data;
        } catch (PDOException $pe) {
            throw new PDOException('GetCompany error: ' . $pe->getMessage());
        }
    }

    protected function setNewCompany(array $companyData): array
    {
        try {
            $sql = $this->connect()->prepare('
                INSERT INTO companies (companyname, companydescribe, cnpj, plan) 
                VALUES (:companyname, :companydescribe, :cnpj, :plan)
            ');

            foreach ($companyData as $key => $value) {
                $sql->bindValue(':' . $key, $value);
            }
            if (!$sql->execute()) {
                return ['status' => 405, 'message' => 'Não foi possivel cadastrar a empresa'];
            }
            return ['status' => 200, 'message' => 'Empresa cadastrada com sucesso'];
        } catch (PDOException $pe) {
            throw new PDOException('SetNewCompany error: ' . $pe->getMessage());
        }
    }

    protected function updateTheCompanysPlan(array $companyData): array
    {
        try {
            $sql  = $this->connect()->prepare('UPDATE companies SET plan = :plan WHERE cnpj = :cnpj');

            foreach ($companyData as $key => $value) {
                $sql->bindValue(':' . $key, $value);
            }
            if ($sql->execute()) {
                return ['status' => 403, 'message' => 'Não foi possivel atualizar o plano'];
            }
            return ['status' => 200, 'message' => 'Plano atualizado'];
        } catch (PDOException $pe) {
            throw new PDOException('UpdateTheCompanysPlan error: ' . $pe->getMessage());
        }
    }

    protected function deleteCompany(string $cnpj): array
    {
        try {
            $sql = $this->connect()->prepare('DELETE FROM companies WHERE cnpj = :cnpj');
            $sql->bindValue(':cnpj', $cnpj);

            if ($sql->execute()) {
                return ['status' => 200, 'message' => 'Empresa deletada com sucesso'];
            }
            return ['status' => 403, 'message' => 'Empresa não pode ser deletada'];
        } catch (PDOException $pe) {
            throw new PDOException('DeleteCompany error: ' . $pe->getMessage());
        }
    }
}
