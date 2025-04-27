<?php

namespace App\Models;

use AccordDTO;
use AccordEntity;
use App\Concerns\InteractsWithDatabase;
use Exception;
use PDO;
use RepositoryException;

class AccordRepository
{
    use InteractsWithDatabase;

    /**
     * Busca os acordo de um cliente
     *
     * @return AccordEntity[]
     */
    public function getAccordsOfClient(int $client): array
    {

        $sql = $this->connect()->prepare('SELECT * FROM accords WHERE client = :client');
        $sql->bindValue(':client', $client);
        $sql->execute();

        if ($sql->rowCount() === 0) {
            throw RepositoryException::entityNotFound('accord', $client);
        }

        return array_map(fn ($model) => AccordEntity::make($model), $sql->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Cria um novo acordo
     */
    public function setNewAccord(
        AccordDTO $accordDTO
    ): void {

        $sql = $this->connect()->prepare('INSERT INTO accords(client, price, numberofinstallments, installmentspaid, fees, requests, tickets) VALUES(:client, :price, :numberofinstallments, :installmentspaid, :fees, :requests, :tickets)');
        $sql->bindValue(':client', $accordDTO->client);
        $sql->bindValue(':price', $accordDTO->price);
        $sql->bindValue(':numberofinstallments', $accordDTO->numberofinstallments);
        $sql->bindValue(':installmentspaid', $accordDTO->installmentspaid);
        $sql->bindValue(':fees', $accordDTO->fees);
        $sql->bindValue(':requests', $accordDTO->requests);
        $sql->bindValue(':tickets', $accordDTO->tickets);
        $sql->execute();

        if ($sql->rowCount() === 0) {
            throw RepositoryException::entityNotFound('accord', $accordDTO->id);
        }
    }

    /**
     * Adiciona o pagamento de uma parcela de um acordo
     *
     * @return array {status: number, message: string|void}
     */
    public function payInstallmentOfAccord(int $accord, int $installments): array
    {
        try {
            $sql = $this->connect()->prepare('UPDATE accords SET installmentspaid = :installments WHERE id = :id');
            $sql->bindValue(':installments', $installments);
            $sql->bindValue(':id', $accord);
            $sql->execute();

            if ($sql->rowCount() === 0) {
                return ['status' => 404, 'message' => 'Erro ao pagar a parcela do acordo', 'error: ' => $sql->errorInfo()];
            }

            return ['status' => 201, 'message' => ''];
        } catch (Exception $e) {
            throw new Exception('Erro ao pagar a parcela do acordo: '.$e->getMessage(), $e->getCode());
        }
    }

    /**
     * Atualiza os status de um acordo
     *
     * @return array {status: number, message: string|void}
     */
    public function updateStatusOfAccord(int $accord, string $status): array
    {
        try {
            $sql = $this->connect()->prepare('UPDATE accords SET status = :status WHERE id = :id');
            $sql->bindValue(':status', $status);
            $sql->bindValue(':id', $accord);
            $sql->execute();

            if ($sql->rowCount() === 0) {
                return ['status' => 404, 'message' => 'Erro ao atualizar o status do acordo', 'error: ' => $sql->errorInfo()];
            }

            return ['status' => 201, 'message' => ''];
        } catch (Exception $e) {
            throw new Exception('Erro ao atualizar o status do acordo: '.$e->getMessage(), $e->getCode());
        }
    }

    /**
     * Deleta um acordo
     *
     * @return array {status: number, message: string|void}
     */
    public function deleteAccord(int $accord): array
    {
        try {
            $sql = $this->connect()->prepare('UPDATE accords SET deleted = true WHERE id = :id');
            $sql->bindValue(':id', $accord);
            $sql->execute();

            if ($sql->rowCount() === 0) {
                return ['status' => 404, 'message' => 'Não foi possivel deletar o acordo', 'error: ' => $sql->errorInfo()];
            }

            return ['status' => 201, 'message' => ''];
        } catch (Exception $e) {
            throw new Exception('Erro ao deletar: '.$e->getMessage(), $e->getCode());
        }
    }
}
