<?php

namespace App\Models;

use app\models\ConnectModel;

class RequestsModel extends ConnectModel {

  public function getRequest(int $id){}

  public function setNewRequest(array $data){}

  public function reject(int $id){}

  public function accept(int $id){}

  public function setPay(int $id, int $installment){}
  
}
