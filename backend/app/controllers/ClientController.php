<?php

namespace app\controllers;

use \Exception;
use Klein\Request;
use Klein\Response;
use app\Classes\Helper;
use app\Classes\JwtHelper;
use app\models\ClientModel;

class ClientController extends ClientModel
{

    private Helper $helper;
    private JwtHelper $jwt;

    public function __construct()
    {
        $this->helper = new Helper;
        $this->jwt = new JwtHelper;
    }

    // Busca os clientes de uma empresa
    public function get(Request $request, Response $response): Response
    {
        try {
            $this->jwt->validate(); // Valida o token
            $param = $request->param('company'); // Recebe o parametro da empresa

            // Verifica se o parametro foi informado
            $this->helper->arrayValidate($param);
            $param = $this->helper->convertType([$param], ['string'])[0]; // Converte o tipo do parametro

            $clientsOfCompany = $this->getAllClientsOfCompany($param); // Busca os clientes da empresa
            // Verifica se a mensagem é um array e sanitiza
            if (is_array($clientsOfCompany['message'])) $clientsOfCompany['message'] = \array_map([$this->helper, 'sanitizeArray'], $clientsOfCompany['message']);

            // Verifica se a mensagem está vazia e retorna 204
            if (empty($clientsOfCompany) || !isset($clientsOfCompany['message'])) {
                return $response
                    ->code(204)
                    ->header('Content-Type', 'application/json')
                    ->body();
            }

            $clientsOfCompany['message'] = \array_map([$this->helper, 'sanitizeArray'], $clientsOfCompany['message']); // Sanitiza a mensagem
            // Retorna a resposta
            return $response
                ->code($clientsOfCompany['status'])
                ->header('Content-Type', 'application/json')
                ->body(\json_encode($clientsOfCompany['message']));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    // Cadastra um novo cliente em uma empresa
    public function register(Request $request, Response $response): Response
    {
        try {
            $this->jwt->validate(); // Valida o token
            $client = \json_decode($request->body()); // Recebe os dados do cliente

            // Verifica se os campos foram informados
            $this->helper->arrayValidate($client, [
                'company',
                'name',
                'email',
                'phone',
                'gender',
                'shippingaddress',
                'billingaddress'
            ]);
            $client = $this->helper->convertType($client, ['string', 'string', 'string', 'string', 'string', 'string', 'string', 'string']); // Converte os tipos dos campos

            // faz o pedido de cadastro do cliente e recebe a resposta
            $res = $this->setNewClientOfCompany($client['company'], $client['name'], $client['email'], $client['phone'], $client['gender'], $client['shippingaddress'], $client['billingaddress']);

            // verifica se o retorno é vazio e avisa o front
            if (empty($res) || !isset($res['message'])) {
                return $response
                    ->code(204)
                    ->header('Content-Type', 'application/json')
                    ->body();
            }

            // retorna a resposta
            return $response
                ->code($res['status'])
                ->header('Content-Type', 'application/json')
                ->body(\json_encode(['message' => $res['message'], 'error' => $res['error'] ?? []]));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    // Busca um cliente de uma empresa
    public function searchClient(Request $request, Response $response): Response
    {
        try {
            $this->jwt->validate(); // Valida o token
            $param = $request->params(['client', 'company']); // Recebe o parametro do cliente

            $this->helper->arrayValidate($param, ['id', 'company']); // Verifica se os campos foram informados
            $param = $this->helper->convertType($param, ['string', 'string']); // Converte os tipos dos campos

            $client = $this->getClient($param); // Busca o cliente

            // Verifica se a mensagem é um array e sanitiza
            if (is_array($client['message'])) $client['message'] = $this->helper->sanitizeArray($client['message']);

            // Verifica se a mensagem está vazia e retorna 204
            if (empty($client) || !isset($client['message'])) {
                return $response
                    ->code(204)
                    ->header('Content-Type', 'application/json')
                    ->body();
            }
            // Retorna a resposta
            return $response
                ->code($client['status'])
                ->header('Content-Type', 'application/json')
                ->body(\json_encode(['message' => $client['message'], 'error' => $client['error'] ?? []]));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    // Deleta um cliente de uma empresa
    public function delete(Request $request, Response $response): Response
    {
        try {
            $this->jwt->validate(); // Valida o token
            $param = $request->param('client'); // Recebe o parametro do cliente

            $this->helper->arrayValidate($param); // Verifica se o campo foi informado
            $param = $this->helper->convertType([$param], ['int'])[0]; // Converte o tipo do campo

            // Faz o pedido de exclusão do cliente e recebe a resposta
            $res = $this->deleteClientOfCompany($param);
            // Verifica se o retorno é vazio e retorna 204
            if (empty($res) || empty($res['message'])) {
                return $response
                    ->code(204)
                    ->header('Content-Type', 'application/json')
                    ->body();
            }

            // Retorna a resposta
            return $response
                ->code($res['status'])
                ->header('Content-Type', 'application/json')
                ->body(\json_encode(['message' => $res['message'], 'error' => $res['error'] ?? []]));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    // Atualiza um cliente de uma empresa
    public function update(Request $request, Response $response): Response
    {
        try {
            $this->jwt->validate(); // Valida o token
            $params = \json_decode($request->body()); // Recebe os dados do cliente

            // Verifica se os campos foram informados
            $this->helper->arrayValidate($params, [
                'id',
                'name',
                'email',
                'phone',
                'gender',
                'shippingaddress',
                'billingaddress'
            ]);
            // Converte os tipos dos campos
            $params = $this->helper->convertType($params, [
                'int',
                'string',
                'string',
                'string',
                'string',
                'string',
                'string',
                'string'
            ]);

            // Faz o pedido de atualização do cliente e recebe a resposta
            $res = $this->updateDataClientOfCompany($params['id'], $params['name'], $params['email'], $params['phone'], $params['gender'], $params['shippingaddress'], $params['billingaddress']);

            // Verifica se o retorno é vazio e retorna 204
            if (empty($res) || !isset($res['message'])) {
                return $response
                    ->code(204)
                    ->header('Content-Type', 'application/json')
                    ->body();
            }

            // Retorna a resposta
            return $response
                ->code($res['status'])
                ->header('Content-Type', 'application/json')
                ->body(\json_encode($res['message']));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
