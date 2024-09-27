<?php

namespace app\models;

use \PDO;
use \PDOException;

class ConnectModel{

  protected function connect(){
    try{
      define( 'DATABASE', './database/finance.db' );

      $db = new PDO( 'sqlite:' . DATABASE );

      return $db;
    }catch(PDOException $pe){
      die( $pe->getMessage() );
    }
  }    

  protected function usersTable(){
    try{
      $sql = $this->connect()->prepare('CREATE TABLE IF NOT EXISTS users(id INTEGER PRIMARY KEY AUTOINCREMENT, userhash INTEGER UNIQUE NOT NULL, name VARCHAR(340) NOT NULL, email VARCHAR(220) UNIQUE NOT NULL, password VARCHAR(130) NOT NULL, identfication VARCHAR(25) ,active BOOLEAN DEFAULT true, createdaccount DATETIME DEFAULT CURRENT_TIMESTAMP, dateofbirth DATE, gender VARCHAR(10), phone VARCHAR(20)');

      $sql->execute();

    }catch(PDOException $pe){
      throw new PDOException ( $pe->getMessage() );
    }
  }

  protected function clientsTable(){}
  
  protected function transactionsTable(){}
  
}