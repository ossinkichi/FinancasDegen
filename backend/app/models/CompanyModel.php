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

    protected function getCompany(string $cnpj): array
    {
        try {
            $sql = $this->connect()->prepare('SELECT * FROM companies WHERE  cnpj = :cnpj');
            $sql->bindParam(':cnpj', $cnpj);

            if (!$sql->execute()) {
                return ['status' => 403, 'message' => 'Não foi possivel buscar a empresa'];
            }

            $data = $sql->fetch(PDO::FETCH_ASSOC);
            return ['status' => 200, 'message' => $data ? $data : []];
        } catch (PDOException $pe) {
            throw new PDOException('GetCompany error: ' . $pe->getMessage());
        }
    }

    protected function setNewCompany(
        string $companyname,
        string $companydescribe,
        string $cnpj,
        int $plan,
        string $planValue
    ): array {
        try {
            $sql = $this->connect()->prepare('
                INSERT INTO companies (companyname, companydescribe, cnpj, plan, planvalue) 
                VALUES (:companyname, :companydescribe, :cnpj, :plan, :planvalue)
            ');
            $sql->bindValue(':companyname', $companyname);
            $sql->bindValue(':companydescribe', $companydescribe);
            $sql->bindValue(':cnpj', $cnpj);
            $sql->bindValue(':plan', $plan);
            $sql->bindValue(':planvalue', $planValue);

            if (!$sql->execute()) {
                return ['status' => 405, 'message' => 'Não foi possivel cadastrar a empresa'];
            }
            return ['status' => 200, 'message' => 'Empresa cadastrada com sucesso'];
        } catch (PDOException $pe) {
            if ($pe->getCode() == 23000) {
                return ['status' => 403, 'message' => 'Empresa já cadastrado, para ingressar na empresa peça permissão ao administrador da mesma!'];
            }
            throw new PDOException('SetNewCompany error: ' . $pe->getMessage());
        }
    }

    protected function updateTheCompanysPlan(string $cnpj, int $plan, string $planValue): array
    {
        try {
            $sql  = $this->connect()->prepare('UPDATE companies SET plan = :plan, planvalue = :planvalue WHERE cnpj = :cnpj');
            $sql->bindValue(':plan', $plan);
            $sql->bindValue(':planvalue', $planValue);
            $sql->bindValue(':cnpj', $cnpj);

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

    protected function activateAccount(string $cnpj): array
    {
        try {

            $sql = $this->connect()->prepare('UPDATE companies set active = :value WHERE cnpj = :cnpj');
            $sql->bindValue(':value', true);
            $sql->bindValue(':cnpj', $cnpj);

            if (!$sql->execute()) {
                return ['satus' => 400, 'message' => 'Não foi possivel ativar a empresa'];
            }
            return ['status' => 200, 'message' => 'Empresa ativada'];
        } catch (PDOException $pe) {
            throw new PDOException("Erro ao ativa a empresa: " . $pe->getMessage());
        }
    }
}
