<?php

namespace app\models;

use \PDO;
use \PDOException;

class ConnectModel{

  protected function connect(){
    try{
      define( 'DATABASE','\db\finance.db' );

      $db = new PDO( 'sqlite:' . DATABASE );
      $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      return $db;
    }catch(PDOException $pe){
      throw new PDOException('Error ao conectar: '. $pe->getMessage().' code: '. $pe->getCode() .' line'. $pe->getLine());
    }
  }    

  protected function usersTable($database){
    try{
      $sql = $database->prepare('CREATE TABLE IF NOT EXISTS users(id INTEGER PRIMARY KEY AUTOINCREMENT, userhash INTEGER UNIQUE NOT NULL, name VARCHAR(340) NOT NULL, email VARCHAR(220) UNIQUE NOT NULL, password VARCHAR(130) NOT NULL, identification VARCHAR(25) ,active BOOL DEFAULT true, createdaccount DATETIME DEFAULT CURRENT_TIMESTAMP, dateofbirth DATE, gender VARCHAR(10), phone VARCHAR(20));');

      return $sql->execute();

    }catch(PDOException $pe){
      throw new PDOException ("Users Error: ". $pe->getMessage() );
    }
  }

  protected function clientsTable(){}
  
  protected function transactionsTable(){}
  
}