<?php

namespace app\models;

use app\models\ConnectModel;
use \PDO;
use PDOException;

class CompanyModel extends ConnectModel
{

    /**
     * @return array {status: number, message: array|string}
     */
    protected function getAllCompanies(): array
    {
        try {
            $sql = $this->connect()->prepare('SELECT * FROM companies');
            $sql->execute();

            if ($sql->rowCount() == 0) {
                return ['status' => 404, 'message' => 'Nenhuma empresa encontrada'];
            }
            return ['status' => 200, 'message' => $sql->fetchAll(PDO::FETCH_ASSOC) ?? []];
        } catch (PDOException $pe) {
            throw new PDOException('getAllCompanies error: ' . $pe->getMessage(), $pe->getCode());
        }
    }

    /**
     * @return array {status: number, message: array|string}
     */
    protected function getCompany(string $cnpj): array
    {
        try {
            $sql = $this->connect()->prepare('SELECT * FROM companies WHERE  cnpj = :cnpj');
            $sql->bindParam(':cnpj', $cnpj);
            $sql->execute();

            if ($sql->rowCount() == 0) {
                return ['status' => 404, 'message' => 'Não foi possivel buscar a empresa'];
            }
            return ['status' => 200, 'message' => $sql->fetch(PDO::FETCH_ASSOC) ?? []];
        } catch (PDOException $pe) {
            throw new PDOException('GetCompany error: ' . $pe->getMessage(), $pe->getCode());
        }
    }

    /**
     * @return array {status: number, message: string|void}
     */
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
            $sql->execute();

            if ($sql->rowCount() == 0) {
                return ['status' => 405, 'message' => 'Não foi possivel cadastrar a empresa'];
            }
            return ['status' => 201, 'message' => ''];
        } catch (PDOException $pe) {
            if ($pe->getCode() == 23000) {
                return ['status' => 403, 'message' => 'Empresa já cadastrado, para ingressar na empresa peça permissão ao administrador da mesma!'];
            }
            throw new PDOException('SetNewCompany error: ' . $pe->getMessage(), $pe->getCode());
        }
    }

    /**
     * @return array {status: number, message: string|void}
     */
    protected function updateTheCompanysPlan(string $cnpj, int $plan, string $planValue): array
    {
        try {
            $sql  = $this->connect()->prepare('UPDATE companies SET plan = :plan, planvalue = :planvalue WHERE cnpj = :cnpj');
            $sql->bindValue(':plan', $plan);
            $sql->bindValue(':planvalue', $planValue);
            $sql->bindValue(':cnpj', $cnpj);
            $sql->execute();
            if ($sql->rowCount() == 0) {
                return ['status' => 403, 'message' => 'Não foi possivel atualizar o plano'];
            }
            return ['status' => 201, 'message' => ''];
        } catch (PDOException $pe) {
            throw new PDOException('UpdateTheCompanysPlan error: ' . $pe->getMessage(), $pe->getCode());
        }
    }

    protected function deleteCompany(string $cnpj): array
    {
        try {
            $sql = $this->connect()->prepare('DELETE FROM companies WHERE cnpj = :cnpj');
            $sql->bindValue(':cnpj', $cnpj);
            $sql->execute();
            if ($sql->rowCount() == 0) {
                return ['status' => 200, 'message' => 'Empresa deletada com sucesso'];
            }
            return ['status' => 201, 'message' => ''];
        } catch (PDOException $pe) {
            throw new PDOException('DeleteCompany error: ' . $pe->getMessage(), $pe->getCode());
        }
    }

    /**
     * @return array {status: number, message: string|void}
     */
    protected function activateCompany(string $cnpj): array
    {
        try {

            $sql = $this->connect()->prepare('UPDATE companies set active = :value WHERE cnpj = :cnpj');
            $sql->bindValue(':value', true);
            $sql->bindValue(':cnpj', $cnpj);
            $sql->execute();

            if ($sql->rowCount() == 0) {
                return ['satus' => 404, 'message' => 'Não foi possivel ativar a empresa'];
            }
            return ['status' => 201, 'message' => ''];
        } catch (PDOException $pe) {
            throw new PDOException("Erro ao ativa a empresa: " . $pe->getMessage(), $pe->getCode());
        }
    }
}
