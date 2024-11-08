<?php

namespace app\models;

use app\models\ConnectModel;
use \PDO;
use \PDOException;

class PlansModel extends ConnectModel
{
  protected function getPlans(): array
  {
    try {
      $sql = $this->connect()->prepare('SELECT * FROM plans');
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
      $sql = $this->connect()->prepare('INSERT INTO plan(planname, plandescribe, numberofusers, numberofclients, price, type) VALUES(:planname, :plandescribe, :numberofusers, :numberofclients, :price, :type)');
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
