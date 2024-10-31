<?php

namespace App\Controllers;

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

    public function company(): void
    {
        try {
            $this->helper->verifyMethod('GET');

            $company = get_object_vars(json_decode(file_get_contents("php://input")));

            if (empty($company['company']) || !isset($company['company'])) {
                $this->helper->message(['error' => 'Empresa não informada']);
                return;
            }

            $data = $this->getCompany($company['user']);
            if (empty($data)) {
                $this->helper->message(['message' => 'Sem dados dessa empresa']);
                return;
            }

            $this->helper->message(['data' => $data, 'message' => 'success']);
        } catch (Exception $e) {
            throw new Exception("company error: " . $e->getMessage());
        }
    }

    public function register(string $name, string $describe, string $cnpj, int $plan): void
    {
        $this->helper->verifyMethod('POST');

        try {
            $company = get_object_vars(json_decode(file_get_contents("php://input")));

            $companyData = [
                'companyname' => filter_var($company['name'], FILTER_SANITIZE_SPECIAL_CHARS),
                'companydescribe' => filter_var($company['describe'], FILTER_SANITIZE_SPECIAL_CHARS),
                'cnpj' => filter_var($company['cnpj'], FILTER_SANITIZE_SPECIAL_CHARS),
                'plan' => filter_var($company['plan'], FILTER_SANITIZE_SPECIAL_CHARS)
            ];

            if (empty($companyData)) {
                $this->helper->message(['error' => 'Data not found'], 405);
                return;
            }

            $this->setNewCompany($companyData);
            $this->helper->message(['message' => 'success new register']);
        } catch (Exception $e) {
            throw new Exception('register of company error' . $e->getMessage());
        }
    }

    public function delete(object $company): void {}
}
