<?php

namespace app\models;

use \PDO;
use \PDOException;
use app\models\ConnectModel;

class PlansModel extends ConnectModel
{
    /**
     * Busca todos os planos existentes
     * @return array {status: number, message: array|string}
     */
    protected function getPlans(): array
    {
        try {
            $sql = $this->connect()->prepare('SELECT * FROM plans');
            $sql->execute();

            if ($sql->rowCount() == 0) {
                return ['status' => 400, 'message' => 'Não foi possivel puxar os planos'];
            }
            return ['status' => 200, 'message' => $sql->fetchAll(PDO::FETCH_ASSOC) ?? []];
        } catch (PDOException $pe) {
            if ($pe->getCode() == 23000) {
                return ['status' => 400, 'message' => 'Não foi possivel buscar os planos'];
            }
            throw new PDOException('Erro ao buscar os planos: ' . $pe->getMessage(), $pe->getCode());
        }
    }

    /**
     * Registra um novo plano
     * @return array {status: number, message: string|void}
     */
    protected function setNewPlan(string $planname, string $plandescribe, int $numberofusers, int $numberofclients, string $price, string $type): array
    {
        try {
            $sql = $this->connect()->prepare('INSERT INTO plans(planname, plandescribe, numberofusers, numberofclients, price, type) VALUES(:planname, :plandescribe, :numberofusers, :numberofclients, :price, :type)');
            $sql->bindValue(':planname', $planname);
            $sql->bindValue(':plandescribe', $plandescribe);
            $sql->bindValue(':numberofusers', $numberofusers);
            $sql->bindValue(':numberofclients', $numberofclients);
            $sql->bindValue(':price', $price);
            $sql->bindValue('type', $type);
            $sql->execute();

            if ($sql->rowCount() == 0) {
                return ['status' => 403, 'message' => 'Não foi possivel registrar um novo plano plano'];
            }
            return ['status' => 201, 'message' => ''];
        } catch (PDOException $pe) {
            throw new PDOException("Erro ao registrar um plano: " . $pe->getMessage(), $pe->getCode());
            if ($pe->getCode() == 23000) {
                return ['status' => 400, 'message' => 'Não foi possivel registrar um novo plano'];
            }
        }
    }

    /**
     * Atualiza os dados de um plano existente
     * @return array {status: number, message: string|void}
     */
    protected function updatePlan(int $id, string $planname, string $plandescribe, int $numberofusers, int $numberofclients, string $price, string $type): array
    {
        try {
            $sql = $this->connect()->prepare('UPDATE plans SET planname = :planname, plandescribe = :plandescribe,  numberofusers = :numberofusers,  numberofclients = :numberofclients,  price = :price, type = :type WHERE id = :id');
            $sql->bindValue(':id', $id);
            $sql->bindValue(':planname', $planname);
            $sql->bindValue(':plandescribe', $plandescribe);
            $sql->bindValue(':numberofusers', $numberofusers);
            $sql->bindValue(':numberofclients', $numberofclients);
            $sql->bindValue(':price', $price);
            $sql->bindValue(':type', $type);
            $sql->execute();
            if ($sql->rowCount() == 0) {
                return ['status' => 400, 'message' => 'Não foi possivel atualizar os dados do plano'];
            }

            return ['status' => 201, 'message' => ''];
        } catch (PDOException $pe) {
            throw new PDOException("Erro ao atualizar o plano: " . $pe->getMessage(), $pe->getCode());
            if ($pe->getCode() == 23000) {
                return ['status' => 400, 'message' => 'Não foi possivel atualizar o plano o plano'];
            }
        }
    }

    /**
     * Desativa um plano existente
     * @return array {status: number, message: string|void}
     */
    protected function desactivatePlan(int $id): array
    {
        try {
            $sql = $this->connect()->prepare('UPDATE plans SET status = :status WHERE id = :id');
            $sql->bindValue(':status', false);
            $sql->bindValue(':id', $id);
            $sql->execute();

            if ($sql->rowCount() == 0) {
                return ['status' => 403, 'message' => 'Não foi possivel desativar os dados do plano'];
            }
            return ['status' => 201, 'message' => ''];
        } catch (PDOException $pe) {
            if ($pe->getCode() == 23000) {
                return ['status' => 400, 'message' => 'Não foi possivel desativar o plano'];
            }
            throw new PDOException("Erro ao desativar o plano: " . $pe->getMessage(), $pe->getCode());
        }
    }
}
