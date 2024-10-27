<?php

namespace app\models;

use \PDO;
use \PDOException;
use \app\models\ConnectModel;


class UsersModel extends ConnectModel
{

  protected function getAllUser(): array
  {
    try {
      $sql = $this->connect()->prepare('SELECT * FROM users');
      $sql->execute();

      $data = $sql->fetchAll(PDO::FETCH_ASSOC);

      return $data;
    } catch (PDOException $pe) {
      throw new PDOException("Erro ao buscar o usuário " . $pe->getMessage());
    }
  }

  protected function getUser(string|array $user): array
  {
    try {
      $sql = $this->connect()->prepare('SELECT * FROM users WHERE userhash = :user OR email = :user');
      $sql->bindValue(':user', $user['user']);
      $sql->execute();

      $data = $sql->fetch(PDO::FETCH_ASSOC);

      return $data;
    } catch (PDOException $pe) {
      throw new PDOException("Erro ao buscar usuário " . $pe->getMessage());
    }
  }

  protected function setNewUser(array $data)
  {
    try {

      $sql = $this->connect()->prepare('
        INSERT INTO users(userhash,name, email, password, cpf, dateofbirth, gender, phone) 
        VALUES(:userhash,:name, :email, :password, :cpf, :dateofbirth, :gender, :phone)
        ');
      foreach ($data as $key => $value) {
        if ($key == 'password') {
          $data[$key] = password_hash($value, PASSWORD_DEFAULT);
        }
        $sql->bindValue(':' . $key, $data[$key]);
      }

      $sql->execute();
    } catch (PDOException $pe) {
      throw new PDOException("Erro ao criar o usuário: " . $pe->getMessage());
    }
  }

  protected function updateDataUser(object|array $data)
  {
    try {

      $sql = $this->connect()->prepare('UPDATE users SET hash = :hash name = :name, email = :email, password = :password, cpf = :cpf, dateofbirth = :dateofbirth, gender = :gender, phone = :phone WHERE id = :id OR userhash = :userhash');

      $sql->bindValue(':name', $data['name']);
      $sql->bindValue(':email', $data['email']);
      $sql->bindValue(':password', password_hash($data['password'], PASSWORD_DEFAULT));
      $sql->bindValue(':cpf', $data['cpf']);
      $sql->bindValue(':dateofbirth', $data['dateofbirth']);
      $sql->bindValue(':gender', $data['gender']);
      $sql->bindValue(':phone', $data['phone']);
      $sql->bindValue(':hash', $data['hash']);
      $sql->bindValue(':id', $data['id']);

      if ($sql->execute()) {
        return true;
      } else {
        return false;
      }
    } catch (PDOException $pe) {
      throw new PDOException("Erro ao atualizar usuário: " . $pe->getMessage());
    }
  }

  protected function desactivateAccount(int $hash)
  {
    try {

      $sql = $this->connect()->prepare('UPDATE users set active = :value WHERE userhash = :hash');
      $sql->bindValue(':value', false);
      $sql->bindValue(':hash', $hash);

      $sql->execute();
    } catch (PDOException $pe) {
      throw new PDOException("Erro ao desativar usuário: " . $pe->getMessage());
    }
  }

  protected function deleteUser(int $hash)
  {
    try {
      $sql = $this->connect()->prepare('DELETE FROM users WHERE userhash = :hash');
      $sql->bindValue(':hash', $hash);

      $sql->execute();
    } catch (PDOException $pe) {
      throw new PDOException("Erro ao deletar o usuário: " . $pe->getMessage());
    }
  }
}
