<?php

namespace App\Controllers;

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
            $this->helper->arrayValidate([$param], [0]);

            $param = $this->helper->convertType([$param], ['string'])[0]; // Converte o tipo do parametro

            $clientsOfCompany = $this->getAllClientsOfCompany($param); // Busca os clientes da empresa

            // Verifica se a mensagem está vazia e retorna 204
            if (empty($clientsOfCompany) || !isset($clientsOfCompany['message'])) {
                return $response
                    ->code(204)
                    ->header('Content-Type', 'application/json')
                    ->body();
            }

            // Verifica se a mensagem é um array e sanitiza
            if (is_array($clientsOfCompany['message'])) $clientsOfCompany['message'] = \array_map([$this->helper, 'sanitizeArray'], $clientsOfCompany['message']);


            // Retorna a resposta
            return $response
                ->code($clientsOfCompany['status'])
                ->header('Content-Type', 'application/json')
                ->body(\json_encode(['message' => $clientsOfCompany['message'], 'error' => $clientsOfCompany['error'] ?? []]));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    // Cadastra um novo cliente em uma empresa
    public function register(Request $request, Response $response): Response
    {
        try {
            $this->jwt->validate(); // Valida o token
            $body = \json_decode($request->body(), true); // Recebe os dados do cliente

            // Verifica se os campos foram informados
            $this->helper->arrayValidate($body, [
                'company',
                'name',
                'email',
                'phone',
                'gender',
                'shippingaddress',
                'billingaddress'
            ]);

            $body = $this->helper->convertType($body, ['string', 'string', 'string',  'string', 'string', 'string', 'string']); // Converte os tipos dos campos

            // faz o pedido de cadastro do cliente e recebe a resposta
            $res = $this->setNewClientOfCompany($body['company'], $body['name'], $body['email'], $body['phone'], $body['gender'], $body['shippingaddress'], $body['billingaddress']);

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
            $param = $request->params(['id', 'company']); // Recebe o parametro do cliente

            $this->helper->arrayValidate($param, ['id', 'company']); // Verifica se os campos foram informados
            $param = $this->helper->convertType($param, ['int', 'string']); // Converte os tipos dos campos

            $client = $this->getClient($param['id'], $param['company']); // Busca o cliente

            // Verifica se a mensagem está vazia e retorna 204
            if (empty($client) || !isset($client['message'])) {
                return $response
                    ->code(204)
                    ->header('Content-Type', 'application/json')
                    ->body();
            }
            // Verifica se a mensagem é um array e sanitiza
            if (is_array($client['message'])) $client['message'] = $this->helper->sanitizeArray($client['message']);

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
            $body = \json_encode($request->body(), true); // Recebe os dados do cliente


            $this->helper->arrayValidate($body, ['client', 'company']); // Verifica se o campo foi informado
            $body = $this->helper->convertType((array) $body, ['int', 'string']); // Converte o tipo do campo

            // Faz o pedido de exclusão do cliente e recebe a resposta
            $res = $this->deleteClientOfCompany($body['client'], $body['company']);
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
            $body = \json_decode($request->body(), true); // Recebe os dados do cliente

            // Verifica se os campos foram informados
            $this->helper->arrayValidate($body, [
                'id',
                'name',
                'email',
                'phone',
                'gender',
                'shippingaddress',
                'billingaddress'
            ]);
            // Converte os tipos dos campos
            $body = $this->helper->convertType($body, [
                'int',
                'string',
                'string',
                'string',
                'string',
                'string',
                'string',
            ]);

            // Faz o pedido de atualização do cliente e recebe a resposta
            $res = $this->updateDataClientOfCompany($body['id'], $body['name'], $body['email'], $body['phone'], $body['gender'], $body['shippingaddress'], $body['billingaddress']);

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
                ->body(\json_encode(['message' => $res['message'], 'error' => $res['error'] ?? []]));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
