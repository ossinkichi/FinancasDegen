<?php

namespace app\models;

use app\models\ConnectModel;

class PlansModel extends ConnectModel {

  private $db;

  public function __construct() {
    $this->db = $this->connect();
  }

  protected function getPlans(){}

  protected function setNewPlan(){}

  protected function updatePlan(){}

  protected function deletePlan(){}
}
