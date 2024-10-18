<?php

require __DIR__.'/../vendor/autoload.php';
include __DIR__.'/../app/functions/helpers.php';

use core\Controller;
use core\Method;
use core\Paramethers;
use app\models\ConnectModel;

try{
  $db = new ConnectModel;
  $db->connect();
  $db->usersTable();
  $db->companyTable();
  $db->clientsTable();
  $db->accountClientTable();
  dd('success');

  $controller = new Controller();
  $controller = $controller->load();
  
  $method = new Method();
  $method = $method->load($controller);

  $paramethers = new Paramethers();
  $paramethers = $paramethers->load();

  $controller->$method($paramethers);

}catch(Exception $e){
  dd($e->getMessage());
}