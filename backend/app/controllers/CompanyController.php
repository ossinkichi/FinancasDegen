<?php

namespace App\Controllers;

use app\models\CompanyModel;
use app\classes\Helper;
use Exception;

class CompanyController extends CompanyModel
{

    private $db;
    private $helper;

    public function __construct()
    {
        $this->helper = new Helper;
    }

    public function company(object $company): void
    {
        try {
            $this->helper->verifyMethod('GET');

            $company = htmlspecialchars($company->paramether);

            if (!$company) {
                $this->helper->message(['error' => 'Empresa não informada']);
                return;
            }

            $data = $this->getCompany($company);
            if (!$data) {
                $this->helper->message(['message' => 'Sem dados dessa empresa']);
                return;
            }

            $this->helper->message(['data' => $data]);
            return;
        } catch (Exception $e) {
        } {
            throw new Exception("company error: " . $e->getMessage());
        }
    }

    public function register(string $name, string $describe, string $cnpj, int $plan): void
    {
        try {
            $this->helper->verifyMethod('POST');

            $companyData = [
                'companyname' => filter_var($name, FILTER_SANITIZE_SPECIAL_CHARS),
                'companydescribe' => filter_var($describe, FILTER_SANITIZE_SPECIAL_CHARS),
                'cnpj' => filter_var($cnpj, FILTER_SANITIZE_SPECIAL_CHARS),
                'plan' => filter_var($plan, FILTER_SANITIZE_SPECIAL_CHARS)
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
