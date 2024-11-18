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

      if (!$sql->execute()) {
        return ['status' => 403, 'message' => 'Não foi possivel buscar os dados'];
      }

      $data = $sql->fetchAll(PDO::FETCH_ASSOC);
      return ['status' => 200, 'message' => $data ? $data : []];
    } catch (PDOException $pe) {
      throw new PDOException("Erro ao buscar o usuário " . $pe->getMessage());
    }
  }

  protected function getUser(string $user): array
  {
    $data = [];
    try {
      $sql = $this->connect()->prepare('SELECT * FROM users WHERE userhash = :user OR email = :user');
      $sql->bindValue(':user', $user);

      if (!$sql->execute()) {
        return ['status' => 403, 'message' => "Não foi possivel buscar o usuário"];
      }

      $data = $sql->fetch(PDO::FETCH_ASSOC);
      return ['status' => 200, 'message' => $data ? $data : []];
    } catch (PDOException $pe) {
      throw new PDOException("Erro ao buscar usuário " . $pe->getMessage());
    }
  }

  protected function setNewUser(array $data): array
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

      if (!$sql->execute()) {
        return ['status' => 403, 'message' => 'Não foi possivel cadastrar o usuario'];
      }
      return ['status' => 200, 'message' => 'Usuário cadastrado'];
    } catch (PDOException $pe) {
      if ($pe->getCode() == 23000) {
        return ['status' => 400, 'message' => 'Não foi possivel cadastrar o usuario, '];
      }
      throw new PDOException("Erro ao criar o usuário: " . $pe->getMessage());
    }
  }

  protected function updateDataUser(
    string $name,
    string $email,
    string $password,
    $dateofbirth,
    string $gender,
    string $phone,
    string $hash
  ): array {
    try {

      $sql = $this->connect()->prepare('UPDATE users SET name = :name, email = :email, password = :password, dateofbirth = :dateofbirth, gender = :gender, phone = :phone WHERE userhash = :hash');
      $sql->bindValue(':name', $name);
      $sql->bindValue(':email', $email);
      $sql->bindValue(':password', password_hash($password, PASSWORD_DEFAULT));
      $sql->bindValue(':dateofbirth', $dateofbirth);
      $sql->bindValue(':gender', $gender);
      $sql->bindValue(':phone', $phone);
      $sql->bindValue(':hash', $hash);

      if (!$sql->execute()) {
        return ['status' => 403, 'message' => 'Não foi possivel atualizar os dados do cliente'];
      }
      return ['status' => 200, 'message' => 'Dados atualizados'];
    } catch (PDOException $pe) {
      if ($pe->getCode() == 23000) {
        return ['status' => 403, 'message' => 'Não foi possivel atualizar os dados do cliente'];
      }
      throw new PDOException("Erro ao atualizar usuário: " . $pe->getMessage());
    }
  }

  protected function activateAccount(string $hash): array
  {
    try {

      $sql = $this->connect()->prepare('UPDATE users set emailverify = :value WHERE userhash = :hash');
      $sql->bindValue(':value', true);
      $sql->bindValue(':hash', $hash);

      if (!$sql->execute()) {
        return ['satus' => 400, 'message' => 'Não foi possivel verificar o email'];
      }
      return ['status' => 200, 'message' => 'Email verificado'];
    } catch (PDOException $pe) {
      throw new PDOException("Erro ao desativar usuário: " . $pe->getMessage());
    }
  }

  protected function deleteUser(string $hash): array
  {
    try {
      $sql = $this->connect()->prepare('DELETE FROM users WHERE userhash = :hash');
      $sql->bindValue(':hash', $hash);

      if (!$sql->execute()) {
        return ['status' => 400, 'message' => 'Não foi possivel deletar a conta do usuario'];
      }
      return ['status' => 200, 'message' => 'Conta deletada'];
    } catch (PDOException $pe) {
      throw new PDOException("Erro ao deletar o usuário: " . $pe->getMessage());
    }
  }

  protected function setCompany(string $company, string $hash)
  {
    try {

      $sql = $this->connect()->prepare('UPDATE users SET company = :company WHERE userhash = :hash');
      $sql->bindValue(':company', $company);
      $sql->bindValue(':hash', $hash);

      if (!$sql->execute()) {
        return ["status" => 400, "message" => "Erro ao entrar na empresa"];
      }

      return ["status" => 200, "message" => "Exito ao entrar na empresa"];
    } catch (PDOException $pe) {
      // if ($pe->getCode() == 23000) {
      //   return ["status" => 400, "message" => "Erro ao entrar na empresa"];
      // }
      throw new PDOException("Erro ao entrar na empresa: " . $pe->getMessage());
    }
  }

  protected function setNewPassword(string $hash, string $password)
  {
    try {
      $sql = $this->connect()->prepare('UPDATE users SET password = :password WHERE userhash = :hash');
      $sql->bindValue(':password', password_hash($password, PASSWORD_DEFAULT));
      $sql->bindValue(':hash', $hash);

      if (!$sql->execute()) {
        return ['status' => 400, 'message' => 'Não foi possivel atualizar a senha'];
      }
      return ['status' => 200, 'message' => 'Senha atualizada'];
    } catch (PDOException $pe) {
      throw new PDOException('' . $pe->getMessage());
    }
  }
}
