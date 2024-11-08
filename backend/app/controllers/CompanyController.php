<?php

namespace app\controllers;

use app\models\CompanyModel;
use app\classes\Helper;
use Exception;

class CompanyController extends CompanyModel
{

    private $helper;

    public function __construct()
    {
        $this->helper = new Helper;
    }

    public function index(): void
    {
        try {
            $this->helper->verifyMethod('GET');

            $companies = $this->getAllCompanies();
            $this->helper->message(['data' => $companies]);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function company(): void
    {
        try {
            $this->helper->verifyMethod('GET');

            $company = get_object_vars(json_decode(file_get_contents("php://input")));

            if (empty($company['company']) || !isset($company['company'])) {
                $this->helper->message(['error' => 'Empresa não informada'], 405);
                return;
            }

            $data = $this->getCompany($company['user']);

            if (empty($data)) {
                $this->helper->message(['message' => 'Empresa não encontrada'], 405);
                return;
            }

            $this->helper->message(['data' => $data, 'message' => 'success']);
        } catch (Exception $e) {
            throw new Exception("company error: " . $e->getMessage());
        }
    }

    public function register(): void
    {
        try {
            $this->helper->verifyMethod('POST');

            $company = get_object_vars(json_decode(file_get_contents("php://input")));
            $dataOfCompant = [
                'companyname' => filter_var($company['name'], FILTER_SANITIZE_SPECIAL_CHARS),
                'companydescribe' => filter_var($company['describe'], FILTER_SANITIZE_SPECIAL_CHARS),
                'cnpj' => filter_var($company['cnpj'], FILTER_SANITIZE_SPECIAL_CHARS),
                'plan' => filter_var($company['plan'], FILTER_SANITIZE_SPECIAL_CHARS)
            ];

            $response = $this->setNewCompany($dataOfCompant);
            $this->helper->message(['message' => $response['message']], $response['status']);
        } catch (Exception $e) {
            throw new Exception('register of company error' . $e->getMessage());
        }
    }

    public function delete(): void
    {
        try {
            $this->helper->verifyMethod('DELETE');

            $company = get_object_vars(json_decode(file_get_contents("php://input")));

            if (empty($company['company'])) {
                $this->helper->message(['error' => 'Empresa não informada'], 405);
                return;
            }

            $response = $this->deleteCompany(strval($company['company']));
            $this->helper->message([$response['message']], $response['status']);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function newPlan(): void
    {
        try {
            $this->helper->verifyMethod('PUT');

            $company = get_object_vars(json_decode(file_get_contents("php://input")));

            $datasOfCompany = [
                'cnpj' => filter_var($company['id'], FILTER_SANITIZE_SPECIAL_CHARS),
                'plan' => filter_var($company['plan'], FILTER_SANITIZE_NUMBER_INT)
            ];
            $response = $this->updateTheCompanysPlan($datasOfCompany);
            $this->helper->message([$response['message']], $response['status']);
        } catch (Exception $e) {
            throw new Exception('newPlan error' . $e->getMessage());
        }
    }
}
