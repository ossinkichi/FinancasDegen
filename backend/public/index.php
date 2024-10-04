<?php

require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/../app/functions/helpers.php';

use core\Controller;
use core\Method;
use core\Paramethers;
use app\controllers\UserController;

try{
  $test = new UserController;
  $test->register([
    'name' => 'socorro seila por que',  
    'email' => 'socorro@gmail.com', 
    'password' => 'seila',
    'identification' => '13121312113', 
    'dateofbirth' => '12-04-2000', 
    'gender' => 'fusca', 
    'phone' => 1231223567
  ]);
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