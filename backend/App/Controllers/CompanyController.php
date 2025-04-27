<?php

namespace App\controllers;

use App\models\CompanyModel;
use App\Shared\Helper;
use App\Shared\JWT;
use Exception;
use Klein\Request;
use Klein\Response;

class CompanyController extends CompanyModel
{
    private Helper $helper;

    private JWT $jwt;

    public function __construct()
    {
        $this->helper = new Helper;
        $this->jwt = new JWT;
    }

    // Puxa todas as empresas do banco de dados
    public function index(Request $request, Response $response): Response
    {
        try {
            $this->jwt->validate(); // Verifica se o token é válido

            $companies = $this->getAllCompanies(); // Puxa todas as empresas do banco de dados

            // Verifica se o retorno é um array e se está vazio
            if (! is_array($companies) || empty($companies)) {
                return $response->code(404)->header('Content-Type', 'aplication/json')->body(['message' => 'Nenhuma empresa encontrada']);
            }

            // Sanitiza os dados retornados
            \is_array($companies['message']) ? $companies['message'] = \array_map(function ($company) {
                $company = sanitizeArray($company);
                unset($company['id']);

                return $company;
            }, $companies['message'] ?? []) : null;

            // Dá um retorno para o front
            return $response
                ->code($companies['status'])
                ->header('Content-Type', 'aplication/json')
                ->body(\json_encode(['message' => $companies['message'], 'error' => $companies['error'] ?? []]));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    // Pega os dados de uma empresa específica
    public function get(Request $request, Response $response): Response
    {
        try {
            $this->jwt->validate(); // Verifica se o token é válido
            $company = $request->param('company'); // Pega o cnpj da empresa

            arrayValidate([$company], [0]); // Verifica se o cnpj foi enviado
            $company = sanitizeArray([$company])[0]; // Sanitiza o cnpj
            $company = convertType([$company], ['string'])[0]; // Converte o tipo do dado

            $res = $this->getCompany($company); // Puxa os dados da empresa do banco de dados

            // Verifica se o retorno é um array e se está vazio
            if (! is_array($res) || empty($res)) {
                return $response->code(404)->header('Content-Type', 'aplication/json')->body(['message' => 'Nenhuma empresa encontrada']);
            }

            if ($res['status'] == 200) {
                $res['message'] = sanitizeArray($res['message']);
                if (is_array($res['message'])) {
                    unset($res['message']['id']);
                }
                if (empty($res['message'])) {
                    $res['message'] = 'Nenhuma empresa encontrada';
                }
            }

            return $response
                ->code($res['status'])
                ->header('Content-Type', 'application/json')
                ->body(\json_encode($res['message']));
        } catch (Exception $e) {
            throw new Exception('company error: '.$e->getMessage());
        }
    }

    // Cadastra uma nova empresa
    public function register(Request $request, Response $response): Response
    {
        try {
            $this->jwt->validate(); // Verifica se o token é válido
            $company = ! empty($request->body()) ? \json_decode($request->body(), true) : []; // Pega os dados da empresa do body da requisição

            arrayValidate($company, ['name', 'describe', 'cnpj', 'plan']); // Verifica se os dados foram enviados
            $company = sanitizeArray($company); // Sanitiza os dados da empresa
            $company = convertType($company, ['string', 'string', 'string', 'decimals']); // Converte o tipo dos dados da empresa

            $res = $this->setNewCompany($company['name'], $company['describe'], $company['cnpj'], $company['plan']); // Cadastra a empresa no banco de dados

            // Verifica se o retorno é um array e se está vazio
            if (! is_array($res) || empty($res)) {
                return $response->code(404)->header('Content-Type', 'aplication/json')->body(['message' => 'Nenhuma empresa encontrada']);
            }

            return $response
                ->code($res['status'])
                ->header('Content-Type', 'application/json')
                ->body(\json_encode(['message' => $res['message'], 'error' => $res['error'] ?? []]));
        } catch (Exception $e) {
            throw new Exception('register of company error'.$e->getMessage());
        }
    }

    // Deleta uma empresa
    public function delete(Request $request, Response $response): Response
    {
        try {
            $this->jwt->validate(); // Verifica se o token é válido
            $company = $request->param('company'); // Pega o cnpj da empresa

            arrayValidate([$company], [0]); // Verifica se o cnpj foi enviado
            $company = sanitizeArray([$company])[0]; // Sanitiza o cnpj
            $company = convertType([$company], ['string'])[0]; // Converte o tipo do dado

            $res = $this->deleteCompany($company); // Puxa os dados da empresa do banco de dados

            // Verifica se o retorno é um array e se está vazio
            if (! is_array($res) || empty($res)) {
                return $response->code(404)->header('Content-Type', 'aplication/json')->body(\json_encode(['message' => 'Nenhuma empresa encontrada']));
            }

            // Dá um retorno para o front
            return $response
                ->code($res['status'])
                ->header('Content-Type', 'aplication/json')
                ->body(\json_encode(['message' => $res['message'], 'error' => $res['error'] ?? []]));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    // Atualiza o plano de uma empresa
    public function changeOfPlan(Request $request, Response $response): Response
    {
        try {
            $this->jwt->validate(); // Verifica se o token é válido
            $body = \json_decode($request->body(), true); // Pega os dados do body da requisição

            arrayValidate($body, ['cnpj', 'plan']); // Verifica se os dados foram enviados
            $body = sanitizeArray($body); // Sanitiza os dados da empresa
            $body = convertType($body, ['string', 'int']); // Converte o tipo dos dados da empresa

            // Verifica se o cnpj é válido
            $exist = $this->companyExist($body['cnpj']);

            // Verifica se alguma empresa foi encontrada
            if (empty($exist)) {
                return $response->code(404)->header('Content-Type', 'aplication/json')->body(['message' => 'Nenhuma empresa encontrada']);
            }

            // Faz o pedido ao banco e recebe sua resposta
            $res = $this->updateTheCompanysPlan($body['cnpj'], $body['plan']);

            \dd($res);

            // Dá um retorno para o front
            return $response
                ->code($res['status'])
                ->header('Content-Type', 'aplication/json')
                ->body(['message' => $res['message'], 'error' => $res['error'] ?? []]);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function update(Request $request, Response $response) {}

    // Verifica se o empresa existe
    private function companyExist(string $cnpj): array
    {
        // Verifica se o cnpj foi enviado
        if (empty($cnpj)) {
            return [];
        }

        // Faz o pedido ao banco e recebe sua resposta
        $company = $this->getCompany($cnpj);

        // Verifica se o retorno é um array e se está vazio
        if (! empty($company) || $company['status'] != 200 || ! is_array($company)) {
            throw new Exception($company['message'] ?? ['message' => 'Nenhuma empresa encontrada']);
            mensagem(['message' => 'Nenhuma empresa encontrada', 'error' => $company['error'] ?? []], 404);

            return [];
        }

        // Dá um retorno a funçaõ
        return $company['message'];
    }
}
