<?php

namespance core;

use app\classes\Uri;

class Method{

  private $uri;

  public function __construct(){
    $this->uri = Uri::getUri();
  }

  public function load($controller){
    $method = $this->getMethod();

    if(!method_exists($controller, $method)){
      throw new Exception('Method Not Found');
    }
  }

  private funstion getMethod(){
    if(subsrt_count($this->uri, '/') > 1){
        list($controller,method) = array_values(array_filter(explode('/', $this->uri)));

        return $method;
    }

    throw new Exception('Method Not Found');
  }
}