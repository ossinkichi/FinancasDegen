<?php

namespace App\controllers;

use App\models\RequestsModel;
use App\Shared\Helper;
use App\Shared\JWT;
use Exception;
use Klein\Request;
use Klein\Response;

class RequestsController extends RequestsModel
{
    private Helper $helper;

    private JWT $jwt;

    public function __construct()
    {
        $this->helper = new Helper;
        $this->jwt = new JWT;
    }

    // Busca todos os pedidos do cliente
    public function get(Request $request, Response $response): Response
    {
        try {
            $this->jwt->validate(); // Validate o token
            $param = $request->param('client'); // Recebe o parametro

            arrayValidate([$param]); // Verifica se o parametro existe
            $param = sanitizeArray([$param])[0]; // Sanitiza o parametro
            $param = convertType([$param], ['int'])[0]; // Converte o tipo do parametro
            $res = $this->getRequest($param); // Busca as contas do cliente

            // Verifica se o retorno da busca é vazio ou não é um array
            if (empty($response) || ! \is_array($res)) {
                return $response->code(404)->header('Content-Type', 'application/json')->body(\json_encode(['message' => 'Nenhum boleto encontrado']));
            }

            // Sanitiza o retorno da busca
            \is_array($res['message']) ? $res['message'] = \array_map([$this->helper, 'sanitizeArray'], $res['message']) : null;

            // Dá um retorno ao front
            return $response
                ->code($res['status'])
                ->header('Content-Type', 'application/json')
                ->body(\json_encode(['message' => $res['message'], 'error' => $res['error'] ?? []]));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    // Registra uma nova pedido
    public function register(Request $request, Response $response): Response
    {
        try {
            $this->jwt->validate();
            $body = \json_decode($request->body(), true);
            // \dd($body);

            arrayValidate($body, ['client', 'title', 'describe', 'price', 'installments', 'fees']);
            $body = sanitizeArray($body);
            $body = convertType($body, ['int', 'string', 'string', 'decimals', 'int', 'decimals']);

            $res = $this->setNewRequest($body['client'], $body['title'], $body['describe'], $body['price'], $body['installments'], $body['fees']);

            return $response
                ->code($res['status'])
                ->header('Content-Type', 'application/json')
                ->body(\json_encode(['message' => $res['message'], 'error' => $res['error'] ?? []]));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    // Aceita um pedido
    public function recive(Request $request, Response $response): Response
    {
        try {
            $this->jwt->validate(); // Valida o token
            $body = \json_decode($request->body(), true); // Recebe o body da requisição
            arrayValidate($body, ['client', 'account']); // Verifica se todos os dados foram enviados
            $body = sanitizeArray($body); // Sanitiza os dados
            $body = convertType($body, ['int', 'int']); // Converte os tipos dos dados

            // Faz o pedido ao banco e recebe o retorno
            $res = $this->updateStatus($body['account'], 'Aceito');

            // Verifica se o retorno da busca é vazio ou não é um array
            if (empty($res)) {
                return $response->code(404)->header('Content-Type', 'application/json')->body(\json_encode(['message' => 'Nenhum boleto encontrado']));
            }

            // Dá um retorno ao front
            return $response
                ->code($res['status'])
                ->header('Content-Type', 'application/json')
                ->body(\json_encode(['message' => $res['message'], 'error' => $res['error'] ?? []]));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    // Nega um pedido
    public function discard(Request $request, Response $response): Response
    {
        try {
            $this->jwt->validate(); // Valida o token
            $body = \json_decode($request->body(), true); // Recebe o body da requisição

            arrayValidate($body, ['client', 'account']); // Verifica se todos os dados foram enviados
            $body = sanitizeArray($body);
            $body = convertType($body, ['int', 'int']); // Converte os tipos dos dados

            // Faz o pedido ao banco e recebe o retorno
            $res = $this->updateStatus($body['account'], 'Recusado');

            if (empty($res)) {
                return $response->code(404)->header('Content-Type', 'application/json')->body(\json_encode(['message' => 'Nenhum boleto encontrado']));
            }

            // Dá um retorno ao front
            return $response->code($res['status'])->header('Content-Type', 'application/json')->body(\json_encode(['message' => $res['message'] ?? [], 'error' => $res['error'] ?? []]));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    // Atualiza a quantidade de parcelas pagas
    public function payInInstallment(Request $request, Response $response): Response
    {
        try {
            $this->jwt->validate(); // Valida o token
            $body = \json_decode($request->body(), true); // Recebe o body da requisição

            arrayValidate($body, ['id', 'installments']); // Verifica se todos os dados foram enviados
            $body = convertType($body, ['int', 'int']); // Converte os tipos dos dados
            $body = sanitizeArray($body); // Sanitiza os dados

            // Faz o pedido ao banco e recebe o retorno
            $res = $this->setPay($body['id'], $body['installments']);

            // Verifica se o retorno da busca é vazio ou não é um array
            if (empty($res) || ! \is_array($res)) {
                return $response->code(404)->header('Content-Type', 'application/json')->body(\json_encode(['message' => 'Nenhum boleto encontrado']));
            }

            // Dá um retorno ao front
            return $response->code($res['status'])->header('Content-Type', 'application/json')->body(\json_encode(['message' => $res['message'], 'error' => $res['error'] ?? []]));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
