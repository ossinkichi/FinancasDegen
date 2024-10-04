<?php

namespace app\controllers;

class ClientController{

  private $clients;

  public function __construct(){
    $this->clients = new ClientModel;
  }

  public function getClient($id,$userHash){}

  public function getAllClients(){}
  
  public function setNewClient(array $data){}

  public function updateDataClient(array $data){}
  
  public function deleteClient(int $id){}

  private function verifyMethod($method,$message){
    if($_SERVER['REQUEST_METHOD'] != $method){
      header('Content-Type: application/json');
      http_response_code(405);
      echo json_encode(['Error' => $message]);
    }
  }
}