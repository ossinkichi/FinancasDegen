<?php

namespace app\controllers;

use \Exception;
use Klein\Request;
use Klein\Response;
use app\Classes\Helper;
use app\Classes\JwtHelper;
use app\models\ClientModel;

class ClientController extends ClientModel
{

    private Helper $helper;
    private JwtHelper $jwt;

    public function __construct()
    {
        $this->helper = new Helper;
        $this->jwt = new JwtHelper;
    }

    public function get(Request $request, Response $response): void
    {
        try {
            $this->helper->verifyMethod('GET');
            $this->jwt->validate();

            $company = $request->param();

            if (empty($company) || !isset($company['company'])) {
                $this->helper->message(['message' => 'empresa não informada'], 400);
            }

            $res = $this->getAllClientsOfCompany($company['company']);
            if (is_array($response['message'])) {
                foreach ($response['message'] as $key => $value) {
                    $response['message'][$key] = $this->helper->sanitizeArray($response['message'][$key]);
                }
            }

            if (empty($response['message'])) {
                $this->helper->message(['message' => 'Nenhum cliente cadastrado']);
                return;
            }
            return $response->code($res['status'])->header('Content-Type', 'application/json')->body(\json_encode($res['message']));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function register(Request $request, Response $response): void
    {
        try {
            $this->helper->verifyMethod('POST');
            $this->jwt->validate();
            $client = \json_decode($request->body());
            $this->helper->arrayValidate($client, [
                'company',
                'name',
                'email',
                'phone',
                'gender',
                'shippingaddress',
                'billingaddress'
            ]);

            $client = $this->helper->getData($client);

            $res = $this->setNewClientOfCompany($client['company'], $client['name'], $client['email'], $client['phone'], $client['gender'], $client['shippingaddress'], $client['billingaddress']);
            return $response->code($res['status'])->header('Content-Type', 'application/json')->body(\json_encode($res['message']));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function client(Request $request, Response $response): void
    {
        try {
            $this->helper->verifyMethod('GET');
            $this->jwt->validate();
            $client = $request->param();
            $this->helper->arrayValidate($client, ['id', 'company']);
            $res = $this->getClient($client);

            $this->helper->arrayValidate($response, ['message', 'status']);
            if (is_array($response['message'])) {
                $response['message'] = $this->helper->sanitizeArray($response['message']);
            }
            return $response->code($res['status'])->header('Content-Type', 'application/json')->body(\json_encode($res['message']));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function delete(Request $request, Response $response): void
    {
        try {
            $this->helper->verifyMethod('DELETE');
            $this->jwt->validate();
            $client = $request->param();
            $this->helper->arrayValidate($client, ['client']);

            $res = $this->deleteClientOfCompany($client['client']);
            return $response->code($res['status'])->header('Content-Type', 'application/json')->body(\json_encode($res['message']));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function update(Request $request, Response $response): void
    {
        try {
            $this->helper->verifyMethod('PUT');
            $this->jwt->validate();
            $data = \json_decode($request->body());
            $this->helper->arrayValidate($data, [
                'id',
                'name',
                'email',
                'phone',
                'gender',
                'shippingaddress',
                'billingaddress'
            ]);
            $data = $this->helper->getData($data);

            $res = $this->updateDataClientOfCompany($data['id'], $data['name'], $data['email'], $data['phone'], $data['gender'], $data['shippingaddress'], $data['billingaddress']);
            return $response->code($res['status'])->header('Content-Type', 'application/json')->body(\json_encode($res['message']));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
