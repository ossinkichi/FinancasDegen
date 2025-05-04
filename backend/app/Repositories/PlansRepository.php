<?php

namespace App\Repositories;

use \PDO;
use \PDOException;
use App\Concern\InteractsWithDatabase;
use App\DTO\PlansDto;
use App\Entities\PlansEntity;
use App\Exceptions\RepositoryException;

class PlansRepository
{
    use InteractsWithDatabase;

    /**
     * Busca todos os planos existentes
     * @return array {status: number, message: array|string}
     */
    public function getPlans(): array|object
    {
        try {
            $sql = $this->connect()->prepare('SELECT * FROM plans');
            $sql->execute();

            // if ($sql->rowCount() == 0) {
            //     throw RepositoryException::entityNotFound('plans', 'plans');
            // }

            return \array_map(fn($model) => PlansEntity::make($model), $sql->fetchAll(PDO::FETCH_ASSOC));
        } catch (PDOException $pe) {
            throw new PDOException('Erro ao buscar os planos: ' . $pe->getMessage(), $pe->getCode());
        }
    }

    /**
     * Registra um novo plano
     * @return array {status: number, message: string|void}
     */
    public function setNewPlan(PlansDto $plansDto): void
    {
        $sql = $this->connect()->prepare('INSERT INTO plans(name, describe, numberofusers, numberofclients, price, type) VALUES(:planname, :plandescribe, :numberofusers, :numberofclients, :price, :type)');
        $sql->bindValue(':planname', $plansDto->name);
        $sql->bindValue(':plandescribe', $plansDto->describe);
        $sql->bindValue(':numberofusers', $plansDto->numberofusers);
        $sql->bindValue(':numberofclients', $plansDto->numberofclients);
        $sql->bindValue(':price', $plansDto->price);
        $sql->bindValue('type', $plansDto->type);
        $sql->execute();

        if ($sql->rowCount() == 0) {
            throw RepositoryException::entityNotFound('plans', $plansDto->name);
        }
    }

    /**
     * Atualiza os dados de um plano existente
     * @return array {status: number, message: string|void}
     */
    public function updatePlan(PlansDto $plansDto): void
    {
        $sql = $this->connect()->prepare('UPDATE plans SET name = :planname, describe = :plandescribe,  numberofusers = :numberofusers,  numberofclients = :numberofclients,  price = :price, type = :type WHERE id = :id');
        $sql->bindValue(':id', $plansDto->id);
        $sql->bindValue(':planname', $plansDto->name);
        $sql->bindValue(':plandescribe', $plansDto->describe);
        $sql->bindValue(':numberofusers', $plansDto->numberofusers);
        $sql->bindValue(':numberofclients', $plansDto->numberofclients);
        $sql->bindValue(':price', $plansDto->price);
        $sql->bindValue(':type', $plansDto->type);
        $sql->execute();

        if ($sql->rowCount() == 0) {
            throw RepositoryException::entityNotFound('plans', $plansDto->id);
        }
    }

    /**
     * Desativa um plano existente
     * @return array {status: number, message: string|void}
     */
    public function disableThePlan(int $id): void
    {
        $sql = $this->connect()->prepare('UPDATE plans SET status = :status WHERE id = :id');
        $sql->bindValue(':status', 0);
        $sql->bindValue(':id', $id);
        $sql->execute();

        if ($sql->rowCount() === 0) {
            throw RepositoryException::entityNotFound('plans', $id);
        }
    }
    public function enableThePlan(int $id): void
    {

        $sql = $this->connect()->prepare('UPDATE plans SET status = :status WHERE id = :id');
        $sql->bindValue(':status', true);
        $sql->bindValue(':id', $id);
        $sql->execute();

        if ($sql->rowCount() === 0) {
            throw RepositoryException::entityNotFound('plans', $id);
        }
    }
}
