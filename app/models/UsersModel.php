<?php

namespace app\models;

class UsersModel extends ConnectModel{

  public function __construct(){
    $this->usersTable();
  } 
  
  public static function getUser(array $data){}
  
  public static function getHashUser(int $hash){
    $data = []
    try{
      $sql = $this->dbConnect->prepare('SELECT active FROM users WHERE userhash = :hash');
      $sql->bindValue(':hash', $hash);
      $sql->execute();
      
      $data = $sql->fetch(PDO::FETCH_ASSOC);

      return data
      
    }catch(PDOException $pe){
      return throw new PDOException("Erro ao buscar o usuário $pe->getMessage()");
    }
  }
  
  public static function setNewUser(data){}
  
  public static function updateDataUser(data)

  public static function deleteUser(int $hash){}
}