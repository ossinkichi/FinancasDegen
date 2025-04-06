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

    public function index(Request $request, Response $response): Response
    {
        try {
            $this->jwt->validate();

            $companies = $this->getAllCompanies();

            if (empty($companies)) {
                return $response->code(404)->header('Content-Type', 'aplication/json')->body(['message' => 'Nenhuma empresa encontrada']);
            }

            \is_array($companies['message']) ? $companies['message'] = \array_map([$this->helper, 'sanitizeArray'], $companies['message'] ?? []) : null;


            return $response
                ->code($companies['status'])
                ->header('Content-Type', 'aplication/json')
                ->body(\json_encode(['message' => $companies['message'], 'error' => $companies['error'] ?? []]));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function get(Request $request, Response $response): Response
    {
        try {

            $this->jwt->validate();
            $company = $request->param('company');

            $this->helper->arrayValidate($company, [0]);
            $res = $this->getCompany($company['cnpj']);
            if ($res['status'] == 200) {
                $res['message'] = $this->helper->sanitizeArray($res['message']);
                if (empty($res['message'])) {
                    $res['message'] = 'Nenhuma empresa encontrada';
                }
            }

            return $response->code($res['status'])->header('Content-Type', 'application/json')->body(\json_encode($res['message']));
        } catch (Exception $e) {
            throw new Exception("company error: " . $e->getMessage());
        }
    }

    public function register(Request $request, Response $response): Response
    {
        try {
            $this->jwt->validate();
            $company = \json_decode($request->body());
            $this->helper->arrayValidate($company, ['name', 'describe', 'cnpj', 'plan', 'value']);
            $company = $this->helper->getData($company);

            $res = $this->setNewCompany($company['name'], $company['describe'], $company['cnpj'], $company['plan'], $company['value']);
            return $response->code($res['status'])->header('Content-Type', 'application/json')->body(\json_encode($res['message']));
        } catch (Exception $e) {
            throw new Exception('register of company error' . $e->getMessage());
        }
    }

    public function delete(Request $request, Response $response): Response
    {
        try {

            $this->jwt->validate();
            $company = $request->param('company');
            $this->helper->arrayValidate($company, [0]);

            $res = $this->deleteCompany(strval($company['cnpj']));
            return $response->code($response['status'])->header('Content-Type', 'aplication/json')->body($res['message']);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function plan(Request $request, Response $response): Response
    {
        try {
            $this->jwt->validate();
            $company = \json_decode($request->body());
            $this->helper->arrayValidate($company, ['cnpj', 'plan', 'value']);

            $res = $this->updateTheCompanysPlan($company['cnpj'], $company['plan'], $company['value']);
            return $response->code($res['status'])->header('Content-Type', 'aplication/json')->body($res['message']);
        } catch (Exception $e) {
            throw new Exception('newPlan error' . $e->getMessage());
        }
    }
}
