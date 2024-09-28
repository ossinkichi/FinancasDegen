<?php

namespace app\controllers;

use \app\models\UsersModel;

class UserController {
  private $users;

  public function __construct(){
    $this->users = new UsersModel;
  }

  public function getAllUser() {
    return $this->users->getAllUser();
  }

  public function getUser(array $data){}

  public function setNewUser(array $data){
    foreach ($data as $key => $value) {
      $data[$key] = htmlspecialchars($value);
    }
      // $data['userhash'] = filter_var($data['userhash'], FILTER_SANITIZE_NUMBER_INT);
      // $data['name'];
      // $data['email'] = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
      // $data['password'] = password_hash($data['password'],PASSWORD_DEFAULT);
      // $data['identification'] = filter_var($data['identification'], FILTER_SANITIZE_NUMBER_INT);
      // $data['dateofbirth'];
      // $data['gender'];
      // $data['phone'];
    
  }

  private function userExist(int $hash){}

  private function userActiavted(array $user){}
}