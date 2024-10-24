<?php

namespace app\models;

use PDO;
use PDOException;
use app\models\ConnectModel;

class PlansModel extends ConnectModel
{

  private object $db;

  public function __construct()
  {
    $this->db = $this->connect();
  }

  protected function getPlans(): array
  {
    try {
      $sql = $this->db->prepare('SELECT * FROM plans');
      $sql->execute();

      $data =  $sql->fetchAll(PDO::FETCH_ASSOC) ?? [];
      return $data;
    } catch (PDOException $pe) {
      throw new PDOException('GetPlans Error: ' . $pe);
    }
  }

  protected function setNewPlan(array $dataPlan): void
  {
    try {
      $sql = $this->db->prepare('INSERT INTO plan(planname, plandescribe, numberofusers, numberofclients, price, type) VALUES(:planname, :plandescribe, :numberofusers, :numberofclients, :price, :type)');
      foreach ($dataPlan as $key => $value) {
        $sql->bindValue(':' . $key, $value);
      }
      $sql->execute();
    } catch (PDOException $pe) {
      throw new PDOException("SetNewPLan error " . $pe);
    }
  }

  protected function updatePlan(): void {}

  protected function deletePlan(): void {}
}
