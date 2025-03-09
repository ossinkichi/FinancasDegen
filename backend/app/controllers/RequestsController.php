<?php

namespace app\controllers;

use \Exception;
use Klein\Request;
use Klein\Response;
use app\Classes\Helper;
use app\Classes\JwtHelper;
use app\models\RequestsModel;

class RequestsController extends RequestsModel
{

    private Helper $helper;
    private JwtHelper $jwt;

    public function __construct()
    {
        $this->helper = new Helper;
        $this->jwt = new JwtHelper;
    }

    public function get()
    {
        try {
            $this->helper->verifyMethod('GET');
            $this->jwt->validate();
            $client = $_GET;
            $this->helper->arrayValidate($client, ['client']);
            $response = $this->getRequest($client['client']);

            if (is_array($response['message'])) {
                foreach ($response['message'] as $key => $value) {
                    $response['message'][$key] = $this->helper->sanitizeArray($response['message'][$key]);
                }
            }

            if (empty($response['message'])) {
                $response['message'] = 'O cliente não possui nenhum boleto';
            }

            $this->helper->message(['message' => $response['message']], $response['status']);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function register(Request $request, Response $response)
    {
        try {
            $this->helper->verifyMethod('POST');
            $this->jwt->validate();
            $datas = file_get_contents('php://input');
            $this->helper->arrayValidate($datas, ['client', 'price', 'installments']);
            $datas = $this->helper->getData($datas);
            $request = $this->helper->sanitizeArray($datas);

            $response = $this->setNewRequest($request['client'], $request['price'], $request['installments']);
            $this->helper->message(['message' => $response['message']], $response['status']);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function recive(Request $request, Response $response)
    {
        try {
            $this->helper->verifyMethod('GET');
            $request = $_GET;
            $this->helper->arrayValidate($request, ['account']);
            $request = $this->helper->sanitizeArray($request);

            $response = $this->updateStatus($request['account'], 'Aceito');
            $this->helper->message(['message' => $response['message']], $response['status']);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function discard(Request $request, Response $response)
    {
        try {
            $this->helper->verifyMethod('GET');
            $this->jwt->validate();
            $request = $_GET;
            $this->helper->arrayValidate($request, ['account']);
            $request = $this->helper->sanitizeArray($request);

            $response = $this->updateStatus($request['account'], 'Recusado');
            $this->helper->message(['message' => $response['message']], $response['status']);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function payInInstallment(Request $request, Response $response)
    {
        try {
            $this->helper->verifyMethod('PUT');
            $this->jwt->validate();
            $request = file_get_contents('php://input');
            $this->helper->arrayValidate($request, ['id', 'installment']);
            $request = $this->helper->getData($request);
            $request = $this->helper->sanitizeArray($request);

            $response = $this->setPay($request['id'], $request['installment']);
            $this->helper->message(['message' => $response['message']], $response['status']);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
