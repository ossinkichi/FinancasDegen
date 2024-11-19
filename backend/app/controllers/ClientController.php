<?php

namespace app\controllers;

use app\models\ClientModel;
use app\Classes\Helper;
use App\Classes\JwtHelper;
use \Exception;

class ClientController extends ClientModel
{

    private Helper $helper;
    private JwtHelper $jwt;

    public function __construct()
    {
        $this->helper = new Helper;
        $this->jwt = new JwtHelper;
    }

    public function get(): void
    {
        try {
            $this->helper->verifyMethod('GET');
            $this->jwt->validate();

            $company = $_GET;

            if (empty($company) || !isset($company['company'])) {
                $this->helper->message(['message' => 'empresa não informada'], 400);
            }

            $response = $this->getAllClientsOfCompany($company['company']);
            if (is_array($response['message'])) {
                foreach ($response['message'] as $key => $value) {
                    $response['message'][$key] = $this->helper->sanitizeArray($response['message'][$key]);
                }
            }

            if (empty($response['message'])) {
                $this->helper->message(['message' => 'Nenhum cliente cadastrado']);
                return;
            }
            $this->helper->message(['message' => $response['message']], $response['status']);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function register(): void
    {
        try {
            $this->helper->verifyMethod('POST');
            $this->jwt->validate();

            $client = file_get_contents("php://input");
            if (empty($client)) {
                $this->helper->message(['message' => 'Dados não informado'], 400);
            }

            $client = $this->helper->getData($client);

            $response = $this->setNewClientOfCompany($client['company'], $client['name'], $client['email'], $client['phone'], $client['gender'], $client['shippingaddress'], $client['billingaddress']);
            $this->helper->message(['message' => $response['message']], $response['status']);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function client(): void
    {
        try {
            $this->helper->verifyMethod('GET');
            $this->jwt->validate();

            $client = $_GET;

            if (empty($client) || !isset($client["id"]) || !isset($client['company']) || empty($client["id"]) || empty($client['company'])) {
                $this->helper->message(['message' => 'Cliente não informado'], 400);
                return;
            }

            $response = $this->getClient($client);

            if (empty($response['message'])) {
                $this->helper->message(['message' => 'Nenhum dado encontrado!'], 400);
                return;
            }

            if (is_array($response['message'])) {
                $response['message'] = $this->helper->sanitizeArray($response['message']);
            }

            $this->helper->message(['message' => $response['message']], $response['status']);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function delete(): void
    {
        try {
            $this->helper->verifyMethod('DELETE');
            $this->jwt->validate();

            $client = $_GET;

            if (empty($client) || !isset($client['client']) || empty($client['client'])) {
                $this->helper->message(['message' => 'Cliente não informado'], 400);
                return;
            }

            $response = $this->deleteClientOfCompany($client['client']);
            $this->helper->message(['message' => $response['message']], $response['status']);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function update(): void
    {
        try {
            $this->helper->verifyMethod('PUT');
            $this->jwt->validate();

            $data = file_get_contents("php://input");

            if (empty($data)) {
                $this->helper->message(['message' => 'Dados não informados'], 400);
                return;
            }

            $data = $this->helper->getData($data);
            $response = $this->updateDataClientOfCompany($data['id'], $data['name'], $data['email'], $data['phone'], $data['gender'], $data['shippingaddress'], $data['billingaddress']);
            $this->helper->message(['message' => $response['message']], $response['status']);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
