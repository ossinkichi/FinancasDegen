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

    public function get(Request $request, Response $response): Response
    {
        try {
            $this->jwt->validate();
            $client = $request->param('client');
            $this->helper->arrayValidate($client, [0]);
            $res = $this->getRequest($client);

            $res['message'] = \is_array($res['message'])  ? \array_map([$this->helper, 'sanitizeArray'], $res['message']) : $res['message'];

            if (empty($response['message'])) {
                $response['message'] = 'O cliente não possui nenhum boleto';
            }

            return $response->code($res['status'])->header('Content-Type', 'application/json')->body(\json_encode($res['message']));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function register(Request $request, Response $response): Response
    {
        try {

            $this->jwt->validate();
            $datas = \json_decode($request->body());
            $this->helper->arrayValidate($datas, ['client', 'price', 'installments']);

            $request = $this->helper->sanitizeArray($datas);

            $res = $this->setNewRequest($request['client'], $request['price'], $request['installments']);
            return $response->code($res['status'])->header('Content-Type', 'application/json')->body(\json_encode($res['message']));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function recive(Request $request, Response $response): Response
    {
        try {

            $request = \json_decode($request->body());
            $this->helper->arrayValidate($request, [0]);
            $request = $this->helper->sanitizeArray($request);

            $res = $this->updateStatus($request['account'], 'Aceito');
            return $response->code($res['status'])->header('Content-Type', 'application/json')->body(\json_encode($res['message']));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function discard(Request $request, Response $response): Response
    {
        try {
            $this->jwt->validate();
            $req = \json_decode($request->body());
            $this->helper->arrayValidate($req, ['client', 'account', 'company']);
            $req = $this->helper->sanitizeArray($req);

            $res = $this->updateStatus($request['account'], 'Recusado');
            return $response->code($res['status'])->header('Content-Type', 'application/json')->body(\json_encode($res['message']));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function payInInstallment(Request $request, Response $response): Response
    {
        try {

            $this->jwt->validate();
            $req = \json_decode($request->body());
            $this->helper->arrayValidate($req, ['id', 'installment']);

            $req = $this->helper->sanitizeArray($req);

            $res = $this->setPay($request['id'], $request['installment']);
            return $response->code($res['status'])->header('Content-Type', 'application/json')->body(\json_encode($res['message']));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
