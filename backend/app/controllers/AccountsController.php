<?php

namespace app\controllers;

use app\models\AccountsModel;

class AccountsController{

  private object $accounts;

  public function __construct(){
    $this->accounts = new AccountsModel;
  }
  
}