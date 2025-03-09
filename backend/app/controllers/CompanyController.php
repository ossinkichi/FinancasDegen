<?php

namespace app\controllers;

use \Exception;
use Klein\Request;
use Klein\Response;
use app\classes\Helper;
use app\classes\JwtHelper;
use app\models\CompanyModel;

class CompanyController extends CompanyModel
{

    private Helper $helper;
    private JwtHelper $jwt;

    public function __construct()
    {
        $this->helper = new Helper;
        $this->jwt = new JwtHelper;
    }

    public function index(Request $request, Response $response): void
    {
        try {
            $this->helper->verifyMethod('GET');
            $this->jwt->validate();

            $companies = $this->getAllCompanies();
            foreach ($companies as $key => $value) {
                $companies[$key] = $this->helper->sanitizeArray($value);
            }
            $this->helper->message(['data' => $companies]);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function get(Request $request, Response $response): void
    {
        try {
            $this->helper->verifyMethod('GET');
            $this->jwt->validate();
            $company = $_GET;

            $this->helper->arrayValidate($company, ['cnpj']);

            $response = $this->getCompany($company['cnpj']);
            if ($response['status'] == 200) {
                $response['message'] = $this->helper->sanitizeArray($response['message']);
                if (empty($response['message'])) {
                    $response['message'] = 'Nenhuma empresa encontrada';
                }
            }

            $this->helper->message(['message' => $response['message']], $response['status']);
        } catch (Exception $e) {
            throw new Exception("company error: " . $e->getMessage());
        }
    }

    public function register(Request $request, Response $response): void
    {
        try {
            $this->helper->verifyMethod('POST');
            $this->jwt->validate();
            $company = file_get_contents("php://input");
            $this->helper->arrayValidate($company, ['name', 'describe', 'cnpj', 'plan', 'value']);
            $company = $this->helper->getData($company);

            $response = $this->setNewCompany($company['name'], $company['describe'], $company['cnpj'], $company['plan'], $company['value']);
            $this->helper->message(['message' => $response['message']], $response['status']);
        } catch (Exception $e) {
            throw new Exception('register of company error' . $e->getMessage());
        }
    }

    public function delete(Request $request, Response $response): void
    {
        try {
            $this->helper->verifyMethod('DELETE');
            $this->jwt->validate();
            $company = $_GET;
            $this->helper->arrayValidate($company, ['cnpj']);

            $response = $this->deleteCompany(strval($company['cnpj']));
            $this->helper->message([$response['message']], $response['status']);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function plan(Request $request, Response $response): void
    {
        try {
            $this->helper->verifyMethod('PUT');
            $this->jwt->validate();
            $company = file_get_contents("php://input");
            $this->helper->arrayValidate($company, ['cnpj', 'plan', 'value']);
            $company = $this->helper->getData($company);

            $response = $this->updateTheCompanysPlan($company['cnpj'], $company['plan'], $company['value']);
            $this->helper->message([$response['message']], $response['status']);
        } catch (Exception $e) {
            throw new Exception('newPlan error' . $e->getMessage());
        }
    }

    public function active(Request $request, Response $response): void
    {
        try {
            $this->helper->verifyMethod('GET');;
            $data = $_GET;

            if (empty($data) && !isset($data['compay'])) {
                $this->helper->message(['message' => 'Empresa não informada'], 403);
                return;
            }

            $response = $this->activateAccount($data['company']);
            $this->helper->message(['message' => $response['message']], $response['status']);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
