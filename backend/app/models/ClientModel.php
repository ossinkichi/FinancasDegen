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

  public static function getClient($id,$userHash){
    // try{
      
    // }catch(PDOException $pe){
    //   return throw new PDOException("Erro ao buscar o cliente $pe->getMessage()");
    // }
  }

  public function getAllClients(){
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

  public static function setNewClient(array $data){}

  public static function updateDataClient(array $data){}

  public static function deleteClient(int $id){}
  
}