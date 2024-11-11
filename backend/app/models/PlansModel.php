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

  protected function setNewPlan($planname, $plandescribe, $numberofusers, $numberofclients, $price, $type): void
  {
    try {
      $sql = $this->connect()->prepare('INSERT INTO plans(planname, plandescribe, numberofusers, numberofclients, price, type) VALUES(:planname, :plandescribe, :numberofusers, :numberofclients, :price, :type)');
      $sql->bindValue(':planname', $planname);
      $sql->bindValue(':plandescribe', $plandescribe);
      $sql->bindValue(':numberofusers', $numberofusers);
      $sql->bindValue(':numberofclients', $numberofclients);
      $sql->bindValue(':price', $price);
      $sql->bindValue('type', $type);

      $sql->execute();
    } catch (PDOException $pe) {
      throw new PDOException("SetNewPLan error " . $pe);
    }
  }

  protected function updatePlan(): void {}

  protected function deletePlan(): void {}
}
