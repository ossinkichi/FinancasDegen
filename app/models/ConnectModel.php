<?php

namespace app\models;

use \PDO;
use \PDOException;

class ConnectModel{

  public function connect(){
    try{
      define( 'DATABASE', __DIR__.'/database/finance.sqlite');

      // dd(file_exists(DATABASE));

      $db = new PDO( 'sqlite:' . DATABASE );
      $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      return $db;
    }catch(PDOException $pe){
      throw new PDOException('Error ao conectar: '. $pe->getMessage());
    }
  }    

  protected function usersTable($database){
    try{
      $sql = $database->prepare('CREATE TABLE IF NOT EXISTS users(id INTEGER PRIMARY KEY AUTOINCREMENT, userhash VARCHAR(64) UNIQUE, name VARCHAR(340) NOT NULL, email VARCHAR(220) UNIQUE NOT NULL, password VARCHAR(130) NOT NULL, identification VARCHAR(25) ,active BOOL DEFAULT true, createdaccount DATETIME DEFAULT CURRENT_TIMESTAMP, dateofbirth DATE, gender VARCHAR(10), phone VARCHAR(20));');

      $sql->execute();

    }catch(PDOException $pe){
      throw new PDOException ("Users Error: ". $pe->getMessage() );
    }
  }

  protected function clientsTable($database){
    try{

      $sql = $database->prepare('CREATE TABLE IF NOT EXISTS clients(id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(350) NOT NULL);');

      $sql->execute();

    }catch(PDOException $pe){
      throw new PDOException('ClientsTable error: '.$pe->getMessage());
    }
  }
  
  protected function companyTable($database){
    try{

      $sql = $database->prepare('CREATE TABLE IF NOT EXISTS company(id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, companyhash INTEGER UNIQUE NOT NULL, companyname VACHAR(200) NOT NULL, userconected INTEGER NOT NULL DEFAULT 1)');

      $sql->execute();

    }catch(PDOException $pe){
      throw new PDOException('CompanyTable error: '.$pe->getMessage());
    }
  }
  
}