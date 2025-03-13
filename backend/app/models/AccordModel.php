<?php

namespace app\Models;

use PDO;
use \PDOException;
use app\models\ConnectModel;
use Exception;

class AccordModel extends ConnectModel
{
    /**
     * @return array {status: number, data: array}
     */
    protected function getAccords(): array
    {
        try {
            $sql = $this->connect()->prepare("SELECT * FROM accords");
            $sql->execute();

            if ($sql->rowCount() == 0) {
                return ['status' => 404, 'data' => 'Nenhum acordo encontrado'];
            }
            return ['status' => 200, 'data' => $sql->fetchAll(PDO::FETCH_ASSOC)];
        } catch (PDOException $pe) {
            throw new PDOException('Erro ao buscar os acordos: ' . $pe->getMessage(), $pe->getCode());
        }
    }

    /**
     * @return array {status: number, message: string|void}
     */
    protected function setNewAccord(
        int $client,
        string $price,
        int $numberofinstallments,
        int $installmentspaid,
        string $fees,
        array|string $requests,
        array|string $tickets
    ): array {
        try {
            $sql = $this->connect()->prepare('INSERT INTO accords(client, price, numberofinstallments, installmentspaid, fees, requests, tickets) VALUES(:client, :price, :numberofinstallments, :installmentspaid, :fees, :requests, :tickets)');
            $sql->bindValue(':client', $client);
            $sql->bindValue(':price', $price);
            $sql->bindValue(':numberofinstallments', $numberofinstallments);
            $sql->bindValue(':installmentspaid', $installmentspaid);
            $sql->bindValue(':fees', $fees);
            $sql->bindValue(':requests', $requests);
            $sql->bindValue(':tickets', $tickets);
            $sql->execute();

            if ($sql->rowCount() == 0) {
                return ['status' => 404, 'message' => 'Erro ao criar um novo acordo', 'error: ' => $sql->errorInfo()];
            }
            return ['status' => 201, 'message' => ''];
        } catch (Exception $e) {
            throw new Exception('Erro ao criar um novo acordo: ' . $e->getMessage(), $e->getCode());
        }
    }

    /**
     * @return array {status: number, message: string|void}
     */
    protected function payInstallmentOfAccord(int $accord, int $installments): array
    {
        try {
            $sql = $this->connect()->prepare('UPDATE accords SET installmentspaid = :installments WHERE id = :id');
            $sql->bindValue(':installments', $installments);
            $sql->bindValue(':id', $accord);
            $sql->execute();

            if ($sql->rowCount() == 0) {
                return ['status' => 404, 'message' => 'Erro ao pagar a parcela do acordo', 'error: ' => $sql->errorInfo()];
            }

            return ['status' => 201, 'message' => ''];
        } catch (Exception $e) {
            throw new Exception('Erro ao pagar a parcela do acordo: ' . $e->getMessage(), $e->getCode());
        }
    }

    /**
     * @return array {status: number, message: string|void}
     */
    protected function updateStatusOfAccord(int $accord, string $status): array
    {
        try {
            $sql = $this->connect()->prepare('UPDATE accords SET status = :status WHERE id = :id');
            $sql->bindValue(':status', $status);
            $sql->bindValue(':id', $accord);
            $sql->execute();

            if ($sql->rowCount() == 0) {
                return ['status' => 404, 'message' => 'Erro ao atualizar o status do acordo', 'error: ' => $sql->errorInfo()];
            }

            return ['status' => 201, 'message' => ''];
        } catch (Exception $e) {
            throw new Exception('Erro ao atualizar o status do acordo: ' . $e->getMessage(), $e->getCode());
        }
    }
}
