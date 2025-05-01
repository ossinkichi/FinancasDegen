<?php

namespace App\Repositories;

use \PDO;
use \PDOException;
use \App\Concern\InteractsWithDatabase;

class PlansRepository
{
    use InteractsWithDatabase;

    private $plansDatabase;
    public function __construct() {}

    /**
     * Busca todos os planos existentes
     * @return array {status: number, message: array|string}
     */
    public function getPlans(): array
    {
        try {
            $sql = $this->connect()->prepare('SELECT * FROM plans');
            $sql->execute();

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
    public function setNewPlan(string $planname, string $plandescribe, int $numberofusers, int $numberofclients, string $price, string $type): array
    {
        try {
            $sql = $this->connect()->prepare('INSERT INTO plans(name, describe, numberofusers, numberofclients, price, type) VALUES(:planname, :plandescribe, :numberofusers, :numberofclients, :price, :type)');
            $sql->bindValue(':planname', $planname);
            $sql->bindValue(':plandescribe', $plandescribe);
            $sql->bindValue(':numberofusers', $numberofusers);
            $sql->bindValue(':numberofclients', $numberofclients);
            $sql->bindValue(':price', $price);
            $sql->bindValue('type', $type);
            $sql->execute();

            if ($sql->rowCount() === 0) {
                return ['status' => 403, 'message' => 'Não foi possivel registrar um novo plano plano', 'error' => $sql->errorInfo()];
            }
            return ['status' => 201, 'message' => ''];
        } catch (PDOException $pe) {
            if ($pe->getCode() == 23000) {
                return ['status' => 400, 'message' => 'Não foi possivel registrar um novo plano'];
            }
            throw new PDOException("Erro ao registrar um plano: " . $pe->getMessage(), (int) $pe->getCode());
        }
    }

    /**
     * Atualiza os dados de um plano existente
     * @return array {status: number, message: string|void}
     */
    public function updatePlan(int $id, string $planname, string $plandescribe, int $numberofusers, int $numberofclients, string $price, string $type): array
    {
        try {
            $sql = $this->connect()->prepare('UPDATE plans SET name = :planname, describe = :plandescribe,  numberofusers = :numberofusers,  numberofclients = :numberofclients,  price = :price, type = :type WHERE id = :id');
            $sql->bindValue(':id', $id);
            $sql->bindValue(':planname', $planname);
            $sql->bindValue(':plandescribe', $plandescribe);
            $sql->bindValue(':numberofusers', $numberofusers);
            $sql->bindValue(':numberofclients', $numberofclients);
            $sql->bindValue(':price', $price);
            $sql->bindValue(':type', $type);
            $sql->execute();
            if ($sql->rowCount() === 0) {
                return ['status' => 400, 'message' => 'Não foi possivel atualizar os dados do plano'];
            }

            return ['status' => 201, 'message' => ''];
        } catch (PDOException $pe) {
            throw new PDOException("Erro ao atualizar o plano: " . $pe->getMessage(), (int) $pe->getCode());
            if ($pe->getCode() == 23000) {
                return ['status' => 400, 'message' => 'Não foi possivel atualizar o plano o plano'];
            }
        }
    }

    /**
     * Desativa um plano existente
     * @return array {status: number, message: string|void}
     */
    public function disableThePlan(int $id): array
    {
        try {
            $sql = $this->connect()->prepare('UPDATE plans SET status = :status WHERE id = :id');
            $sql->bindValue(':status', 0);
            $sql->bindValue(':id', $id);
            $sql->execute();

            if ($sql->rowCount() === 0) {
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
    public function enableThePlan(int $id): array
    {
        try {
            $sql = $this->connect()->prepare('UPDATE plans SET status = :status WHERE id = :id');
            $sql->bindValue(':status', true);
            $sql->bindValue(':id', $id);
            $sql->execute();

            if ($sql->rowCount() === 0) {
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
