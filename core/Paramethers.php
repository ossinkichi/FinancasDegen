<?php

namespace core;

use app\classes\Uri;

class Paramethers{

  private $uri;

  public function __construct(){
    $this->uri = Uri::getUri();
  }

  public function load(){
    $param = $this->getParam();
  }

  private function getParam(){
    if(substr_count($this->uri, '/') > 2){
      $param = array_values(array_filter(explode('/', $this->uri)));

      return (object) [
        'paramether' => $param[2],
        'outher' => $param[3],
      ];
    }
  }
  
}