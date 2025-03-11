<?php

namespace app\Models;

use app\models\ConnectModel;
use \Exception;
use \PDOException;

class TicketModel extends ConnectModel
{
    protected function createTicket()
    {
        try {
            $sql = $this->connect()->prepare('INSERT INTO ticket(request, price, numberofinstallment, dateofpayment, paid, fees) VALUE(:request, :price, :numberofinstallment, :dateofpayment, :paid, :fees)');
            $sql->$sql->execute();

            if ($sql->rowCount() == 0) {
                return ['status' => 404, 'message' => 'Houve um erro ao buscar o dado'];
            }
            return ['status' => 201, 'message' => ''];
        } catch (PDOException $pe) {
        } catch (Exception $e) {
        }
    }

    protected function getTickets(int $account) {}

    protected function payinstallment(int $account, int $ticket) {}
}
