<?php

namespace app\models;

use \PDO;
use \PDOException;
use app\models\ConnectModel;

class RequestsModel extends ConnectModel
{

    /**
     * @return array {status: number, message: string|void}
     */
    public function getRequest(int $client): array
    {
        try {
            $sql = $this->connect()->prepare('SELECT * FROM requests WHERE client = :client');
            $sql->bindValue(':client', $client);
            $sql->execute();

            // if ($sql->rowCount() === 0) {
            //     return ['status' => 403, 'message' => 'Não foi possivel buscar as contas do cliente', 'error' => $sql->errorInfo()];
            // }

            return ['status' => 200, 'message' => $sql->fetchAll(PDO::FETCH_ASSOC) ?? []];
        } catch (PDOException $pe) {
            throw new PDOException($pe->getMessage(), $pe->getCode());
        }
    }

    /**
     * @return array {status: number, message: string|void}
     */
    public function setNewRequest(int $client, string $title, string $describe, string $price, int $numberofinstallments, string $fees): array
    {
        try {
            $sql = $this->connect()->prepare('INSERT INTO requests(client, title, describe, price, numberofinstallments, fees) VALUES(:client, :title, :describe, :price, :numberofinstallments,:fees)');
            $sql->bindValue(':client', $client);
            $sql->bindValue(':title', $title);
            $sql->bindValue(':describe', $describe);
            $sql->bindValue(':price', $price);
            $sql->bindValue(':numberofinstallments', $numberofinstallments);
            $sql->bindValue(':fees', $fees);
            $sql->execute();

            if ($sql->rowCount() === 0) {
                return ['status' => 403, 'message' => 'Não foi possivel emitir esse pedido', 'error' => $sql->errorInfo()];
            }
            return ['status' =>  201, 'message' => ''];
        } catch (PDOException $pe) {
            throw new PDOException($pe->getMessage(), $pe->getCode());
        }
    }

    /**
     * @return array {status: number, message: string|void}
     */
    public function updateStatus(int $request, string $status): array
    {
        try {
            $sql =  $this->connect()->prepare('UPDATE requests SET status = :status WHERE id = :id');
            $sql->bindValue(':status', $status);
            $sql->bindValue(':id', $request);
            $sql->execute();

            if ($sql->rowCount() === 0) {
                return ['status' => 403, 'message' => 'Não foi possivel modificar o status do pedido', 'error' => $sql->errorInfo()];
            }
            return ['status' => 201, 'message' => ''];
        } catch (PDOException $pe) {
            throw new PDOException($pe->getMessage(), $pe->getCode());
        }
    }

    /**
     * @return array {status: number, message: string|void}
     */
    public function setPay(int $request, int $installment): array
    {
        try {
            $sql = $this->connect()->prepare('UPDATE requests SET installmentspaid = :installmentspaid WHERE id = :id');
            $sql->bindValue(':id', $request);
            $sql->bindValue(':installmentspaid', $installment);
            $sql->execute();

            if ($sql->rowCount() === 0) {
                return ['status' => 200, 'message' => 'Não foi possivel efetuar o pagamente', 'error' => $sql->errorInfo()];
            }
            return ['status' => 201, 'message' => ''];
        } catch (PDOException $pe) {
            throw new PDOException($pe->getMessage(), $pe->getCode());
        }
    }
}
