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
            $this->connect()->prepare('');
        } catch (PDOException $pe) {
        } catch (Exception $e) {
        }
    }

    protected function getTickets(int $account) {}

    protected function payinstallment(int $account, int $ticket) {}
}
