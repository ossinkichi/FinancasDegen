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
                $this->helper->message(['error' => 'Empresa não informada'],405);
                return;
            }

            $data = $this->getCompany($company['user']);
            
            if (empty($data)) {
                $this->helper->message(['message' => 'Empresa não encontrada'],405);
                return;
            }

            $this->helper->message(['data' => $data, 'message' => 'success']);
        } catch (Exception $e) {
            throw new Exception("company error: " . $e->getMessage());
        }
    }

    public function register(): void
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

            if($this->setNewCompany($companyData)){
                $this->helper->message(['message' => 'success new register']);
                return;
            }

            $this->helper->message(['message' => 'Ocorreu um erro ao cadastrar a empresa'],405);
        } catch (Exception $e) {
            throw new Exception('register of company error' . $e->getMessage());
        }
    }

    public function delete(): void {
        try{
            $company = get_object_vars(json_decode(file_get_contents("php://input")));

            if(empty($company['company'])){
                $this->helper->message(['error' => 'Empresa não informada'],405);
                return;
            }

            $this->deleteCompany(strval($company['company']));
        }catch(Exception $e){
            throw new Exception( $e->getMessage());
        }
    }
}
