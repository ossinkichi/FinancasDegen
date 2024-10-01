<?php

namespace app\models;

use \PDO;
use \PDOException;
use app\models\ConnectModel;


class UsersModel extends ConnectModel{

  private $db;

  public function __construct(){
    $this->db = $this->connect();
    $this->usersTable($this->connect());
  } 
  
  public function getAllUser():array{
    try{
      $sql = $this->db->prepare('SELECT * FROM users');
      $sql->execute();

      $data = $sql->fetchAll(PDO::FETCH_ASSOC);

      return $data;

    }catch(PDOException $pe){
      throw new PDOException("Erro ao buscar o usuário ". $pe->getMessage());
    }
  }

  public function getUser(object|array $user):array{
    try{
      $sql = $this->db->prepare('SELECT * FROM users WHERE userhash = :user OR email = :user');
      $sql->bindValue(':user', $user['user']);
      $sql->execute();

      $data = $sql->fetchAll(PDO::FETCH_ASSOC);

      return $data;

    }catch(PDOException $pe){
      throw new PDOException("Erro ao buscar usuário ". $pe->getMessage());
    }
  }
  
  public function setNewUser(object|array $data):bool{
    try{
      $sql = $this->db->prepare('INSERT INTO users(name, email, password, identification, dateofbirth, gender, phone) VALUES(:name, :email, :password, :identification, :dateofbirth, :gender, :phone);');

      foreach ($data as $key => $value){
        if($key == 'password'){
          $data[$key] = password_hash($value, PASSWORD_DEFAULT);
        }
        $sql->bindValue(':'.$key, $data[$key]]);
      }
      
      $sql->execute()
      
    }catch(PDOException $pe){
      throw new PDOException("Erro ao criar o usuário: ". $pe->getMessage());
    }
  }
  
  public function updateDataUser(object|array $data){
    try{

      $sql = $this->db->prepare('UPDATE users SET hash = :hash name = :name, email = :email, password = :password, identification = :identification, dateofbirth = :dateofbirth, gender = :gender, phone = :phone WHERE id = :id OR userhash = :userhash');
      
      $sql->bindValue(':name', $data['name']);
      $sql->bindValue(':email', $data['email']);
      $sql->bindValue(':password', password_hash($data['password'], PASSWORD_DEFAULT));
      $sql->bindValue(':identification', $data['identification']);
      $sql->bindValue(':dateofbirth', $data['dateofbirth']);
      $sql->bindValue(':gender', $data['gender']);
      $sql->bindValue(':phone', $data['phone']);
      $sql->bindValue(':hash', $data['hash']);
      $sql->bindValue(':id', $data['id']);

      if($sql->execute()){
        return true;
      }else{
        return false;
      }
      
    }catch(PDOException $pe){
      throw new PDOException("Erro ao atualizar usuário: ". $pe->getMessage());
    }
  }

  public function desactivateAccount(int $hash){
    try{

      $sql = $this->db->prepare('UPDATE users set active = :value WHERE userhash = :hash');
      $sql->bindValue(':value',false);
      $sql->bindValue(':hash',$hash);

      $sql->execute();

    }catch(PDOException $pe){
      throw new PDOException("Erro ao desativar usuário: ". $pe->getMessage());
    }
  }

  public function deleteUser(int $hash){
    try{
      $sql = $this->db->prepare('DELETE FROM users WHERE userhash = :hash');
      $sql->bindValue(':hash', $hash);
      
      $sql->execute();
    }catch(PDOException $pe){
      throw new PDOException("Erro ao deletar o usuário: ". $pe->getMessage());
    }
  }

}