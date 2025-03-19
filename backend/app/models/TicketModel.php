<?php

namespace app\Models;

use \Exception;
use \PDOException;
use app\models\ConnectModel;
use PDO;

class TicketModel extends ConnectModel
{
    /**
     * Registra um novo boleto
     * @return array {status: int, message: string|void}
     */
    protected function setNewTicket(int $request, string $price, int $numberofinstallment, mixed $dateofpayment, int|bool $paid, mixed $fees): array
    {
        try {
            $sql = $this->connect()->prepare('INSERT INTO ticket(request, price, numberofinstallment, dateofpayment, paid, fees) VALUE(:request, :price, :numberofinstallment, :dateofpayment, :paid, :fees)');
            $sql->bindValue(':request', $request);
            $sql->bindValue(':price', $price);
            $sql->bindValue(':numberofinstallment', $numberofinstallment);
            $sql->bindValue(':dateofpayment', $dateofpayment);
            $sql->bindValue(':paid', $paid);
            $sql->bindValue(':fees', $fees);
            $sql->execute();

            if ($sql->rowCount() == 0) {
                return ['status' => 404, 'message' => 'Houve um erro ao buscar o dado', 'error' => $sql->errorInfo()];
            }
            return ['status' => 201, 'message' => ''];
        } catch (PDOException $pe) {
            throw new PDOException('Erro ao Criar o boleto: ' . $pe->getMessage(), $pe->getCode());
        }
    }

    /**
     * Busca todos os boletos de uma conta especifica
     * @return array {status: int, message: array}
     */
    protected function getTickets(int $account): array
    {
        try {
            $sql = $this->connect()->prepare('SELECT * FROM ticket WHERE request = :account');
            $sql->bindValue(':account', $account);
            $sql->execute();

            if ($sql->rowCount() == 0) {
                return ['status' => 404, 'message' => 'Nenhum dado encontrado', 'error' => $sql->errorInfo()];
            }
            return ['status' => 200, 'message' => $sql->fetchAll(PDO::FETCH_ASSOC)];
        } catch (PDOException $pe) {
            throw new PDOException('Erro ao buscar os boletos: ' . $pe->getMessage(), $pe->getCode());
        }
    }

    /**
     * Busca um boleto especifico
     * @return array {status: int, message: array}
     */
    protected function getTicket(int $request, int $account): array
    {
        try {
            $sql = $this->connect()->prepare('SELECT * FROM ticket WHERE request = :reques AND id = :account');
            $sql->bindValue(':reques', $request);
            $sql->bindValue(':account', $account);
            $sql->execute();

            if ($sql->rowCount() == 0) {
                return ['status' => 404, 'message' => 'Nenhum dado encontrado', 'error' => $sql->errorInfo()];
            }

            return ['status' => 200, 'message' => $sql->fetch(PDO::FETCH_ASSOC)];
        } catch (Exception $e) {
            throw new Exception('Erro ao buscar o boleto: ' . $e->getMessage(), $e->getCode());
        }
    }

    /**
     * Paga uma parcela de um boleto
     * @return array {status: int, message: string|void}
     */
    protected function payinstallment(int $account, int $ticket): array
    {
        try {
            $sql = $this->connect()->prepare('UPDATE ticket SET paid = 1 WHERE request = :account AND id = :ticket');
            $sql->bindValue(':account', $account);
            $sql->bindValue(':ticket', $ticket);
            $sql->execute();

            if ($sql->rowCount() == 0) {
                return ['status' => 404, 'message' => 'Houve um erro ao pagar a parcela', 'error' => $sql->errorInfo()];
            }

            return ['status' => 201, 'message' => ''];
        } catch (PDOException $pe) {
            throw new PDOException('Erro ao pagar a parcela: ' . $pe->getMessage(), $pe->getCode());
        }
    }
}
