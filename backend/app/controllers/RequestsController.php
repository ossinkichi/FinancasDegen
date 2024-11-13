<?php

namespace app\controllers;

use app\models\RequestsModel;
use app\Classes\Helper;
use \Exception;

class RequestsController extends RequestsModel
{

  private Helper $helper;

  public function __construct()
  {
    $this->helper = new Helper;
  }

  public function get()
  {
    try {
      $this->helper->verifyMethod('GET');

      $client = $_GET;
      
      if(empty($client['client']) || !isset($client['client'])){
        $this->helper->message(['message' => 'Cliente não informado'],403);
        return;
      }

      $response = $this->getRequest($client['client']);

      if(is_array($response['message'])){
        foreach($response['message'] as $key => $value){
        $response['message'][$key] = $this->helper->sanitizetArray($response['message'][$key]);
        }
      }

      if(empty($response['message'])){
        $response['message'] = 'O cliente não possui nenhum boleto';
      }

      $this->helper->message(['message' => $response['message']], $response['status']);
    } catch (Exception $e) {
      throw new Exception($e->getMessage());
    }
  }

  public function register()
  {
    try {
      $this->helper->verifyMethod('POST');
      $datas = file_get_contents('php://input');
      
      if(empty($datas)){
        $this->helper->message(['message' => 'Informações incompletas'],403);
        return;
      }
        
      $datas = $this->helper->getData($datas);
      $request = $this->helper->sanitizeArray($datas);

      $response = $this->setNewRequest($request['client'], $request['price'], $request['installments']);
      $this->helper->message(['message' => $response['message']], $response['status']);
    } catch (Exception $e) {
      throw new Exception($e->getMessage());
    }
  }

  public function recive()
  {
    try {
      $this->helper->verifyMethod('GET');
      $request = $_GET;

      if(empty($request['account']) || !isset($request['account'])){
        $this->helper->message(['message' => 'Pedido não informado'],403);
        return;
      }
      
      $request = $this->helper->sanitizeArray($request);

      $response = $this->updateStatus($request['account'], 'Aceito');
      $this->helper->message(['message' => $response['message']], $response['status']);
    } catch (Exception $e) {
      throw new Exception($e->getMessage());
    }
  }

  public function discard()
  {
    try {
      $this->helper->verifyMethod('GET');
      $request = $_GET;
      if(empty($request['account']) || !isset($request['account'])){
        $this->helper->message(['message' => 'Pedido não informado'],403);
        return;
      }
      $request = $this->helper->sanitizeArray($request);

      $response = $this->updateStatus($request['account'], 'Recusado');
      $this->helper->message(['message' => $response['message']], $response['status']);
    } catch (Exception $e) {
      throw new Exception($e->getMessage());
    }
  }

  public function payInInstallment()
  {
    try {
      $this->helper->verifyMethod('PUT');

      $request = file_get_contents('php://input');

      if(empty($request)){
        $this->helper->message(['message'=>'Nenhum dado informado'],403);
        return;
      }
      
      $request = $this->helper->getData();
      $request = $this->helper->sanitizeArray($request);

      $response = $this->setPay($request['id'], $request['installment']);
      $this->helper->message(['message' => $response['message']], $response['status']);
    } catch (Exception $e) {
      throw new Exception($e->getMessage());
    }
  }
}
