<?php

namespace App\Controllers;

use app\models\ClientModel;
use app\Classes\Helper;
use \Exception;

class ClientController extends ClientModel
{

    private object $helper;

    public function __construct()
    {
        $this->helper = new Helper;
    }

    public function get(): void
    {
        try {
            $this->helper->verifyMethod('GET');
            $company = $_GET;

            if (empty($company) || !isset($company['company'])) {
                $this->helper->message(['message' => 'empresa não informada'], 400);
            }

            $response = $this->getAllClientsOfCompany($company['company']);
            if (is_array($response['message'])) {
                $response['message'] = $this->helper->sanitizeArray($response['message']);
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

            $data = get_object_vars(json_decode(file_get_contents("php://input")));
            $client = [
                'name' => filter_var($data['name'], FILTER_SANITIZE_SPECIAL_CHARS),
                'email' => filter_var($data['email'], FILTER_SANITIZE_EMAIL),
                'phone' => filter_var($data['phone'], FILTER_SANITIZE_SPECIAL_CHARS),
                'shippingaddress' => filter_var($data['shippingaddress'], FILTER_SANITIZE_SPECIAL_CHARS),
                'billingaddress' => filter_var($data['billingaddress'], FILTER_SANITIZE_SPECIAL_CHARS),
                'company' => filter_var($data['company'], FILTER_SANITIZE_NUMBER_INT)
            ];

            $response = $this->setNewClientOfCompany($client);
            $this->helper->message(['message' => $response['message']], $response['status']);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function client(): void
    {
        try {
            $this->helper->verifyMethod('GET');

            $client = get_object_vars(json_decode(file_get_contents("php://input")));

            if (empty($client) || !isset($client["id"]) || !isset($client['company'])) {
                $this->helper->message(['message' => 'Cliente não informado'], 400);
            }

            $this->getClient($client);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function delete(): void
    {
        try {
            $this->helper->verifyMethod('DELETE');

            $client = get_object_vars(json_decode(file_get_contents("php://input")));

            if (empty($client) || !isset($client['client'])) {
                $this->helper->message(['message' => 'Cliente não informado'], 400);
                return;
            }

            $client = $this->helper->sanitizeArray($client);
            $response = $this->deleteClientOfCompany($client);

            $this->helper->message(['message' => $response['message']], $response['status']);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function update(): void
    {
        try {
            $this->helper->verifyMethod('PUT');
            $data = get_object_vars(json_decode(file_get_contents("php://input")));

            if (empty($data) || !isset($data['client'])) {
                $this->helper->message(['message' => 'Dados não informados'], 400);
                return;
            }

            $response = $this->updateDataClientOfCompany($data);
            $this->helper->message(['message' => $response['message']], $response['status']);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
