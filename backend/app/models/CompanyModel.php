<?php

namespace app\models;

use app\models\ConnectModel;
use \PDO;
use PDOException;

class CompanyModel extends ConnectModel
{

    /**
     * Busca todas as empresas
     * @return array {status: number, message: array|string}
     */
    protected function getAllCompanies(): array
    {
        try {
            $sql = $this->connect()->prepare('SELECT * FROM companies');
            $sql->execute();

            if ($sql->rowCount() == 0) {
                return ['status' => 403, 'message' => 'Nenhuma empresa encontrada', 'error' => $sql->errorInfo()];
            }
            return ['status' => 200, 'message' => $sql->fetchAll(PDO::FETCH_ASSOC) ?? []];
        } catch (PDOException $pe) {
            throw new PDOException('Erro ao buscar as empresas: ' . $pe->getMessage(), $pe->getCode());
        }
    }

    /**
     * Busca uma empresa especifica
     * @return array {status: number, message: array|string}
     */
    protected function getCompany(string $cnpj): array
    {
        try {
            $sql = $this->connect()->prepare('SELECT * FROM companies WHERE  cnpj = :cnpj');
            $sql->bindParam(':cnpj', $cnpj);
            $sql->execute();

            if ($sql->rowCount() == 0) {
                return ['status' => 404, 'message' => 'Não foi possivel buscar a empresa', 'error' => $sql->errorInfo()];
            }
            return ['status' => 200, 'message' => $sql->fetch(PDO::FETCH_ASSOC) ?? []];
        } catch (PDOException $pe) {
            throw new PDOException('Erro ao buscar uma empresa: ' . $pe->getMessage(), $pe->getCode());
        }
    }

    /**
     * Cadastra uma nova empresa
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
                return ['status' => 405, 'message' => 'Não foi possivel cadastrar a empresa', 'error' => $sql->errorInfo()];
            }
            return ['status' => 201, 'message' => ''];
        } catch (PDOException $pe) {
            if ($pe->getCode() == 23000) {
                return ['status' => 403, 'message' => 'Empresa já cadastrado, para ingressar na empresa peça permissão ao administrador da mesma!'];
            }
            throw new PDOException('Erro ao cadastrar a empresa: ' . $pe->getMessage(), $pe->getCode());
        }
    }

    /**
     * Troca o plano da empresa
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
                return ['status' => 403, 'message' => 'Não foi possivel atualizar o plano', 'error' => $sql->errorInfo()];
            }
            return ['status' => 201, 'message' => ''];
        } catch (PDOException $pe) {
            throw new PDOException('Erro ao atualizar plano: ' . $pe->getMessage(), $pe->getCode());
        }
    }

    /**
     * Desativa uma empresa
     * @return array {status: number, message: string|void}
     */
    protected function desactivateCompany(string $cnpj): array
    {
        try {
            $sql = $this->connect()->prepare('UPDATE companies SET status = :status WHERE cnpj = :cnpj');
            $sql->bindValue(':cnpj', $cnpj);
            $sql->bindValue(':status', true);
            $sql->execute();
            if ($sql->rowCount() == 0) {
                return ['status' => 200, 'message' => 'Não foi possivel desativar a empresa', 'error' => $sql->errorInfo()];
            }
            return ['status' => 201, 'message' => ''];
        } catch (PDOException $pe) {
            throw new PDOException('Error ao desativar a empresa: ' . $pe->getMessage(), $pe->getCode());
        }
    }

    /**
     * Ativa uma empresa
     * @return array {status: number, message: string|void}
     */
    protected function activateCompany(string $cnpj): array
    {
        try {
            $sql = $this->connect()->prepare('UPDATE companies set status = :value WHERE cnpj = :cnpj');
            $sql->bindValue(':value', true);
            $sql->bindValue(':cnpj', $cnpj);
            $sql->execute();

            if ($sql->rowCount() == 0) {
                return ['satus' => 404, 'message' => 'Não foi possivel ativar a empresa', 'error' => $sql->errorInfo()];
            }
            return ['status' => 201, 'message' => ''];
        } catch (PDOException $pe) {
            throw new PDOException("Erro ao ativar a empresa: " . $pe->getMessage(), $pe->getCode());
        }
    }

    /**
     * Deleta uma empresa
     * @return array {status: number, message: string|void}
     */
    protected function deleteCompany(string $cnpj): array
    {
        try {
            $sql = $this->connect()->prepare('DELETE FROM companies WHERE cnpj = :cnpj');
            $sql->bindValue(':cnpj', $cnpj);
            $sql->execute();

            if ($sql->rowCount() == 0) {
                return ['status' => 404, 'message' => 'Não foi possivel deletar a empresa', 'error' => $sql->errorInfo()];
            }
            return ['status' => 201, 'message' => ''];
        } catch (PDOException $pe) {
            throw new PDOException('Erro ao deletar a empresa: ' . $pe->getMessage(), $pe->getCode());
        }
    }
}
