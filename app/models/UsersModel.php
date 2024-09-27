<?php

namespace app\models;

use \PDO;
use \PDOException;


class UsersModel extends ConnectModel{

  private static $db;

  public function __construct(){
    $this->db = $this->connect();
    $this->usersTable();
  } 
  
  public static function getUser(object|array $data){}
  
  public static function getHashUser(int $hash):object|array {
    $data = [];

    try{
      $sql = self::$db->prepare('SELECT active FROM users WHERE userhash = :hash');
      $sql->bindValue(':hash', $hash);
      $sql->execute();
      
      $data = $sql->fetch(PDO::FETCH_ASSOC);

      return $data;
      
    }catch(PDOException $pe){
      return throw new PDOException("Erro ao buscar o usuário ". $pe->getMessage());
    }
  }
  
  public static function setNewUser(object|array $data){}
  
  public static function updateDataUser(object|array $data){}

  public static function deleteUser(int $hash){}
}