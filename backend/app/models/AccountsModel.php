<?php

namespace app\models;

use app\models\ConnectModel;
use \PDOException;

class AccountsModel extends ConnectModel
{

  private $db;

  public function __construct()
  {
    $this->db = $this->connect();
  }

  protected function getClientAccount(int $client):array{}

  protected function setNewClientAccount(array $accountData):void{}

  protected function updateClientAccount(array $accountData):void{}

  protected function deleteClientAccount(int $client):void{}
}
