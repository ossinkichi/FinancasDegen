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

      $client = get_object_vars(json_decode(file_get_contents('php://input')));
      $requests = $this->helper->sanitizeArray($client);

      $response = $this->getRequest($requests['client']);

      $this->helper->message(['message' => $response['message']], $response['status']);
    } catch (Exception $e) {
      throw new Exception($e->getMessage());
    }
  }

  public function register()
  {
    try {
      $this->helper->verifyMethod('POST');
      $datas = get_object_vars(json_decode(file_get_contents('php://input')));
      $request = $this->helper->sanitizeArray($datas);

      $response = $this->setNewRequest($request['client'], $request['prica'], $request['installments']);
      $this->helper->message(['message' => $response['message']], $response['status']);
    } catch (Exception $e) {
      throw new Exception($e->getMessage());
    }
  }

  public function recive()
  {
    try {
      $this->helper->verifyMethod('GET');
      $request = get_object_vars(json_decode(file_get_contents("php://input")));
      $request = $this->helper->sanitizeArray($request);

      $response = $this->updateStatus($request['id'], 'Acceito');
      $this->helper->message(['message' => $response['message']], $response['status']);
    } catch (Exception $e) {
      throw new Exception($e->getMessage());
    }
  }

  public function discard()
  {
    try {
      $this->helper->verifyMethod('GET');
      $request = get_object_vars(json_decode(file_get_contents("php://input")));
      $request = $this->helper->sanitizeArray($request);

      $response = $this->updateStatus($request['id'], 'Recusado');
      $this->helper->message(['message' => $response['message']], $response['status']);
    } catch (Exception $e) {
      throw new Exception($e->getMessage());
    }
  }

  public function payInInstallment()
  {
    try {
      $this->helper->verifyMethod('PUT');

      $request = $this->helper->getData();
      $request = $this->helper->sanitizeArray($request);

      $response = $this->setPay($request['id'], $request['installment']);
      $this->helper->message(['message' => $response['message']], $response['status']);
    } catch (Exception $e) {
      throw new Exception($e->getMessage());
    }
  }
}
