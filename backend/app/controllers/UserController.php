<?php

namespace app\controllers;

use \app\models\UsersModel;

class UserController
{
  private $users;

  public function __construct()
  {
    $this->users = new UsersModel;
  }

  public function index()
  {
    echo json_encode($this->users->getAllUser());
    return;
  }

  public function login(array $data)
  {
    $this->verifyMethod('POST', 'Não é possível enviar os dados por GET');

    foreach ($data as $key => $value) {
      $data[$key] = htmlspecialchars($value);
      if (empty($value)) {
        $field[] = $key;
      }
    }

    if (!empty($field)) {
      $this->handlerError(['message' => 'O campo obrigatorio não preenchido'], $field, 400);
      return;
    }

    $userData = $this->users->getUser($data);

    if (!$userData['active'] || !$userData) {
      $this->message(['error' => 'Usuário está com a conta inativa ou inexistente'], 403);
      return;
    };

    if (!password_verify($data['password'], $userData['password'])) {
      $this->message(['message' => 'Senha ou usuário incorreta'], 401);
      return;
    }

    $this->message(['user' => $userData['userhash'], 'message' => 'Login efetuado com sucesso'], 200);
  }

  public function register(array $data)
  {
    $this->verifyMethod('POST','Não é possível enviar os dados por GET');

    $user = [
      'name' => filter_var($data['name'], FILTER_SANITIZE_SPECIAL_CHARS),
      'email' => filter_var($data['email'], FILTER_SANITIZE_EMAIL),
      'password' => filter_var($data['password'], FILTER_SANITIZE_SPECIAL_CHARS),
      'cpf' => $data['cpf'],
      'dateofbirth' => $data['dateofbirth'],
      'gender' => filter_var($data['gender'], FILTER_SANITIZE_SPECIAL_CHARS),
      'phone' => filter_var($data['name'], FILTER_SANITIZE_SPECIAL_CHARS)
    ];

    foreach ($user as $key => $value) {
      if (empty($value)) {
        $error[] = $key;
      }
    }

    if (!empty($error)) {
      http_response_code(400);
      echo json_encode(['error' => $error]);
      return;
    }

    $user['userhash'] = $this->createHash($user['cpf']);

    $this->users->setNewUser($user);
  }

  public function getDataUser(string $hash)
  {
    $this->verifyMethod('GET', 'Não é possível enviar os dados por POST');

    if (empty($hash)) {
      http_response_code(400);
      echo json_encode(['error' => 'É necessario passar o hash para que a busca seja feita']);
      return;
    }

    $userData = $this->users->getUser(['user' => $hash]);

    if (!$userData['active']) {
      http_response_code(403);
      echo json_encode(['error' => 'Usuario está com a conta inativa, para acessar novamente nossa aplicacao e necessario que ative a sua conta']);
      return;
    };

    echo json_encode(['user' => $userData]);
    return;
  }

  public function update(array $data)
  {
    $this->verifyMethod('POST', 'Não é possível enviar os dados por GET');

    $user = [
      'userhash' => filter_var($data['userhash'], FILTER_SANITIZE_SPECIAL_CHARS),
      'name' => filter_var($data['name'], FILTER_SANITIZE_SPECIAL_CHARS),
      'email' => filter_var($data['email'], FILTER_SANITIZE_EMAIL),
      'password' => filter_var($data['password'], FILTER_SANITIZE_SPECIAL_CHARS),
      'cpf' => $data['cpf'],
      'dateofbirth' => $data['dateofbirth'],
      'gender' => filter_var($data['gender'], FILTER_SANITIZE_SPECIAL_CHARS),
      'phone' => filter_var($data['name'], FILTER_SANITIZE_SPECIAL_CHARS)
    ];

    foreach ($user as $key => $value) {
      if (empty($value)) {
        $error[] = $key;
      }
    }

    if (!empty($error)) {
      http_response_code(400);
      echo json_encode(['error' => $error]);
      return;
    }
  }

  public function delete(string $hash) {}

  public function desactivateAccount(int $hash)
  {
    $this->verifyMethod('GET', 'Não é possível enviar os dados por POST');
  }

  private function createHash(string $hash): string
  {
    return hash('sha256', $hash);
  }

  private function verifyMethod($method, $message)
  {
    if ($_SERVER['REQUEST_METHOD'] != $method) {
      header('Content-Type: application/json');
      http_response_code(405);
      echo json_encode(['Error' => $message]);
    }
  }

  private function handlerError(array $message, array $fields, int $code)
  {
    http_response_code($code);
    echo json_encode(['error' => $message, 'filds' => $fields]);
  }

  private function message(array $message, int $code = 200)
  {
    http_response_code($code);
    echo json_encode($message);
  }
}
