<?php

use Dotenv\Dotenv;

function cors(){
    
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
    $dotenv->load();

    if (!defined('DOMAIN')) define('DOMAIN',$_ENV['DOMAIN']);
    // Permitir acesso de qualquer origem
    header("Access-Control-Allow-Origin:".DOMAIN);

    // Permitir métodos específicos
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

    // Permitir cabeçalhos específicos
    header("Access-Control-Allow-Headers: Content-Type, Authorization");

}