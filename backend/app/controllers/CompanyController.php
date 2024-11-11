<?php

namespace app\controllers;

use app\models\CompanyModel;
use app\classes\Helper;
use \Exception;

class CompanyController extends CompanyModel
{

    private Helper $helper;

    public function __construct()
    {
        $this->helper = new Helper;
    }

    public function index(): void
    {
        try {
            $this->helper->verifyMethod('GET');

            $companies = $this->getAllCompanies();
            foreach ($companies as $key => $value) {
                $companies[$key] = $this->helper->sanitizeArray($value);
            }
            $this->helper->message(['data' => $companies]);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function get(): void
    {
        try {
            $this->helper->verifyMethod('GET');
            $company = $_GET;

            if (empty($company['cnpj']) || !isset($company['cnpj'])) {
                $this->helper->message(['error' => 'Empmresa não informada'], 400);
                return;
            }

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

    public function register(): void
    {
        try {
            $this->helper->verifyMethod('POST');


            $company = file_get_contents("php://input");
            $company = $this->helper->getData($company);

            if (empty($company)) {
                $this->helper->message(['message' => 'Informações inconpletas'], 403);
                return;
            }

            $response = $this->setNewCompany($company['name'], $company['describe'], $company['cnpj'], $company['plan']);
            $this->helper->message(['message' => $response['message']], $response['status']);
        } catch (Exception $e) {
            throw new Exception('register of company error' . $e->getMessage());
        }
    }

    public function delete(): void
    {
        try {
            $this->helper->verifyMethod('DELETE');

            $company = $_GET;

            if (empty($company['cnpj'])) {
                $this->helper->message(['error' => 'Empresa não informada'], 405);
                return;
            }

            $response = $this->deleteCompany(strval($company['cnpj']));
            $this->helper->message([$response['message']], $response['status']);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function plan(): void
    {
        try {
            $this->helper->verifyMethod('PUT');

            $company = file_get_contents("php://input");
            $company = $this->helper->getData($company);

            // $datasOfCompany = [
            //     'cnpj' => filter_var($company['id'], FILTER_SANITIZE_SPECIAL_CHARS),
            //     'plan' => filter_var($company['plan'], FILTER_SANITIZE_NUMBER_INT)
            // ];
            $response = $this->updateTheCompanysPlan($company['cnpj'], $company['plan']);
            // $this->helper->message([$response['message']], $response['status']);
        } catch (Exception $e) {
            throw new Exception('newPlan error' . $e->getMessage());
        }
    }
}
