<?php

namespace app\models;

class ClientModel extends ConnectModel{

  private $dbConnect;
  
  public function __construct(){
    $this->dbConnect = $this->connect();
    $this->clientsTable();
  }

  public static function getClient(){
    try{
      
    }catch(PDOException $pe){
      return throw new PDOException("Erro ao buscar o cliente $pe->getMessage()");
    }
  }

  public static function getAllClients(){

    try{

      $sql = $this->dbConnect->prepare('SELECT * FROM clients');
      $sql->execute();
      $clients = $sql->fetchAll(PDO::FETCH_ASSOC);

      return $clients;
      
    }catch(PDOException $pe){
      return throw new PDOException("Erro ao buscar os clientes $pe->getMessage()");
    }
    
  }

  public static function setNewClient(array $data){}

  public static function updateDataClient(array $data){}

  public static function deleteClient(int $id){}
  
}