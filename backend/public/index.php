<?php

require __DIR__ . '/../vendor/autoload.php';
include __DIR__ . '/../app/functions/helpers.php';

use core\Controller;
use core\Method;
use core\Paramethers;
use app\classes\Ultils;

try {
  $ultils = new Ultils;
  dd($ultils::createTables());

  $controller = new Controller();
  $controller = $controller->load();

  $method = new Method();
  $method = $method->load($controller);

  $paramethers = new Paramethers();
  $paramethers = $paramethers->load();

  $controller->$method($paramethers);
} catch (Exception $e) {
  dd($e->getMessage());
}
