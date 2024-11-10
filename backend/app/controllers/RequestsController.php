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

      $response = $this->setNewRequest($request['client'], $request['prica'], $request['']);
    } catch (Exception $e) {
      throw new Exception($e->getMessage());
    }
  }

  public function recive() {}

  public function discard() {}

  public function payInInstallment() {}
}
