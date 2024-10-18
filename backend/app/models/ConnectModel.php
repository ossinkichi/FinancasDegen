<?php

namespace app\models;

use \PDO;
use \PDOException;
use Dotenv\Dotenv;

class ConnectModel{

  public function connect(){
    try{
      
      $dotenv = Dotenv::createImmutable(__DIR__.'/../../');
      $dotenv->load();
      
      if(!defined('DATABASE')) define( 'DATABASE', __DIR__.'/'.$_ENV['DATABASE']);

      $db = new PDO( 'sqlite:' . DATABASE );
      $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      $db->exec('PRAGMA foreign_keys = ON;');
      return $db;
    }catch(PDOException $pe){
      throw new PDOException('Error ao conectar: '. $pe->getMessage());
    }
  }    

  public function usersTable(){
    try{
      $database = $this->connect();

      $sql = $database->prepare('CREATE TABLE IF NOT EXISTS users(id INTEGER PRIMARY KEY AUTOINCREMENT, userhash VARCHAR(64) UNIQUE, name VARCHAR(340) NOT NULL, email VARCHAR(220) UNIQUE NOT NULL, emailverify BOOL DEFAULT false,password VARCHAR(130) NOT NULL, cpf VARCHAR(11) ,active BOOL DEFAULT false, createdaccount DATETIME DEFAULT CURRENT_TIMESTAMP, dateofbirth DATE, gender VARCHAR(10), phone VARCHAR(20), FOREIGN KEY (idcompany)  REFERENCES company(id) ON DELETE CASCADE);');

      $sql->execute();

    }catch(PDOException $pe){
      throw new PDOException ("Users Error: ". $pe->getMessage() );
    }
  }

  public function clientsTable(){
    try{
      $database = $this->connect();
      
      $sql = $database->prepare('CREATE TABLE IF NOT EXISTS clients(id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(350) NOT NULL, phone INTEGER(11) NOT NULL, email VARCHAR(220) NOT NULL, shippingaddress VARCHAR(220), billingaddress VARCHAR(220), FOREIGN KEY (companyid) REFERENCES company(id) ON DELETE CASCADE);');

      $sql->execute();

    }catch(PDOException $pe){
      throw new PDOException('ClientsTable error: '.$pe->getMessage());
    }
  }
  
  public function companyTable(){
    try{
      $database = $this->connect();
      
      $sql = $database->prepare('CREATE TABLE IF NOT EXISTS company(id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, companyname VACHAR(200) NOT NULL, cnpj VARCHAR(14), FOREIGN KEY (idUser) REFERENCES users(id) ON DELETE CASCADE, FOREIGN KEY (idClient) REFERENCES clients(id) ON DELETE CASCADE);');

      $sql->execute();

    }catch(PDOException $pe){
      throw new PDOException('CompanyTable error: '.$pe->getMessage());
    }
  }

  public function accountClientTable(){
    try{
      $database = $this->connect();

      $sql = $database->prepare('CREATE TABLE IF NOT EXISTS accountclient(id INTEGER PRIMARY UNIQUE AUTOINCREMENT NOT NULL, cost VARCHAR(10), FOREIGN KEY (idclient) REFERENCES clients(id);');

      $sql->execute();
    }catch(PDOException $pe){
      throw new PDOException('AccountClientTable error: '.$pe->getMessage());
    }
  }
  
}