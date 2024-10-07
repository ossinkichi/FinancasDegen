<?php

namespace app\models;

use app\models\ConnectModel;
use \PDOException;

class AccountsModel extends ConnectModel{

  private $db;
  
  public function __construct()
  {
    $this->db = $this->connect();
    $this->db->AccountsTable();
  }  
  
}