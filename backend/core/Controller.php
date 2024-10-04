<?php

namespace core;

use app\classes\Uri;
use \Exception;

class Controller{

  private $uri;
  private $controller;
  private $namespace;
  private $folder = "\app\controllers";

  public function __construct(){
    $this->uri = Uri::getUri();
  }

  public function load(){
    if($this->uri == "/"){
      return $this->home();
    }
    return $this->notHome();
  }

  private function home(){
    if(!$this->controllerExist('HomeController')){
      throw new Exception('Controller Not Found');
    }

    return $this->instantiate();
  }

  private function notHome(){
    $controller = $this->getController();

    if(!$this->controllerExist($controller)){
      throw new Exception('Controller Not Found');
    }

    return $this->instantiate();
  }

  private function getController(){
    if(subsrt_count($this->uri, '/') >= 1){
      list($controller) = array_values(array_filter(explode('/', $this->uri)));
      
      return ucfirts($controller).'Controller';
    }

    return ucfirst(ltrim($this->uri, '/')).'Controller';
  }
  
  private function controllerExist($controller){
    $controllerExist = false;
    
    if(class_exists($this->folder.'\\'.$controller)){
      $controllerExist = true;

      $this->namespace = $this->folder;
      $this->controller = $controller;
    }

    return $controllerExist;
  }
  
  private function instantiate(){
    $controller = $this->namespace.'\\'.$this->controller;

    return new $controller;
  }

  
  
}