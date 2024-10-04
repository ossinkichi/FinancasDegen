<?php

function dd($dump){
  echo '<pre>';
  var_dump($dump);
  echo '</pre>';
  die();
}