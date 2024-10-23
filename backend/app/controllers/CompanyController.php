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
            }

            $data = $this->getCompany($company);
            if (!$data) {
                $this->helper->message(['message' => 'Sem dados dessa empresa']);
            }

            $this->helper->message(['data' => $data]);
        } catch (Exception $e) {
        } {
            throw new Exception("company error: " . $e->getMessage());
        }
    }

    public function register(array $companyData): void {}

    public function delete(object $company): void {}
}
