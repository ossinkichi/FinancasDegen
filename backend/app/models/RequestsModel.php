<?php

namespace app\models;

use app\models\ConnectModel;
use \PDO;
use \PDOException;

class RequestsModel extends ConnectModel
{

  public function getRequest(int $client): array
  {
    try {
      $sql = $this->connect()->prepare('SELECT * FROM requests WHERE client = :client');
      $sql->bindValue(':client', $client);
      if (!$sql->execute()) {
        return ['status' => 403, 'message' => 'Não foi possivel buscar as contas do cliente'];
      }

      $data = $sql->fetchAll(PDO::FETCH_ASSOC) ?? [];
      return ['status' => 200, 'message' => $data];
    } catch (PDOException $pe) {
      throw new PDOException($pe->getMessage());
    }
  }

  public function setNewRequest(int $client, string $price, int $numberofinstallments): array
  {
    try {
      $sql = $this->connect()->prepare('INSERT INTO requests(client, price, numberofinstallments) VALUES(:client, :price, :numberofinstallments)');
      $sql->bindValue(':client', $client);
      $sql->bindValue(':price', $price);
      $sql->bindValue(':numberofinstallments', $numberofinstallments);

      if (!$sql->execute()) {
        return ['status' => 403, 'message' => 'Não foi possivel emitir esse pedido'];
      }
      return ['status' =>  200, 'message' => 'pedido feito com sucesso'];
    } catch (PDOException $pe) {
      throw new PDOException($pe->getMessage());
    }
  }

  public function updateStatus(int $request, string $status): array
  {
    try {
      $sql =  $this->connect()->prepare('UPDATE requests SET status = :status WHERE id = :id');
      $sql->bindValue(':status', $status);
      $sql->bindValue(':id', $request);

      if (!$sql->execute()) {
        return ['status' => 403, 'message' => 'Não foi possivel modificar o status do pedido'];
      }
      return ['status' => 200, 'message' => 'Status modificado com sucesso'];
    } catch (PDOException $pe) {
      throw new PDOException($pe->getMessage());
    }
  }

  public function setPay(int $request, int $installment): array
  {
    try {
      $sql = $this->connect()->prepare('UPDATE requests SET installmentspaid = :installmentspaid WHERE id = :id');
      $sql->bindValue(':id', $request);
      $sql->bindValue(':installmentspaid', $installment);

      if (!$sql->execute()) {
        return ['status' => 200, 'message' => 'Não foi possivel efetuar o pagamente'];
      }
      return ['status' => 200, 'message' => 'Pagamento efetuado com sucesso'];
    } catch (PDOException $pe) {
      throw new PDOException($pe->getMessage());
    }
  }
}
