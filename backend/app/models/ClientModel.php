<?php

namespace app\models;

use app\models\ConnectModel;
use \PDOException;

class ClientModel extends ConnectModel{

  private $db;
  
  public function __construct(){
    $this->db = new ConnectModel;
    $this->db->clientsTable($this->db);
  }

  protected function getAllClientsOfCompany(){
    // $clients = [];

    // try{

    //   $sql = $this->db->prepare('SELECT * FROM clients');
    //   $sql->execute();
    //   $clients = $sql->fetchAll(PDO::FETCH_ASSOC);

    //   return $clients;

    // }catch(PDOException $pe){
    //   return throw new PDOException("Erro ao buscar os clientes". $pe->getMessage());
    // }

  }
  
  protected static function getClientData($client){
    // try{
      
    // }catch(PDOException $pe){
    //   return throw new PDOException("Erro ao buscar o cliente $pe->getMessage()");
    // }
  }

  protected static function setNewClientOfCompany(array $clientData){}

  protected static function updateDataClientOfCompany(array $clientData){}
  
  protected static function deleteClientOfCompany(int $client){}
  
}