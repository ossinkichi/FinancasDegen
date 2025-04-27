<?php

namespace App\controllers;

use App\models\PlansModel;
use App\Shared\Helper;
use Exception;
use Klein\Request;
use Klein\Response;

class PlansController extends PlansModel
{
    private Helper $helper;

    public function __construct()
    {
        $this->helper = new Helper;
    }

    // Puxa todos os planos
    public function plans(Request $request, Response $response): Response
    {
        try {
            $plans = $this->getPlans(); // Envia um pedido ao banco e recebe sua resposta

            // Verifica se tem alguma resposta
            if (empty($plans)) {
                return $response->code(203)->header('Content-Type', 'application/json')->body(\json_encode(['message' => 'nenhum plano encontrado']));
            }

            // Verifica se é um array e sanitiza
            if (is_array($plans['message'])) {
                $plans['message'] = \array_map([$this->helper, 'sanitizeArray'], $plans['message']);
            }

            // retorna a resposta final ao front
            return $response
                ->code($plans['status'])
                ->header('Content-Type', 'application/json')
                ->body(\json_encode(['message' => $plans['message'], 'error' => $plans['error'] ?? []]));
        } catch (Exception $e) {
            throw new Exception('Planos não encontrados: '.$e->getMessage(), (int) $e->getCode());
        }
    }

    // Registra um novo plano
    public function register(Request $request, Response $response)
    {
        try {
            $planParams = \json_decode($request->body(), true); // Pega os dados do body

            arrayValidate($planParams, ['name', 'describe', 'users', 'clients', 'price', 'type']); // Valida se os campos foram enviados
            $planParams = sanitizeArray($planParams); // Sanitiza os dados dos campos
            $planParams = convertType($planParams, ['string', 'string', 'int', 'int', 'decimals', 'string']); // Converte os tipos

            // return \print_r(\gettype($planParams['users']));

            // Verifica se o typo do plano é aceitavel
            if (\strtolower($planParams['type']) !== 'anual' && strtolower($planParams['type']) !== 'mensal') {
                return $response->code(422)->header('Content-Type', 'application/json')->body(\json_encode(['message' => 'Tipo de plano invalido']));
            }

            $res = $this->setNewPlan($planParams['name'], $planParams['describe'], $planParams['users'], $planParams['clients'], $planParams['price'], $planParams['type']); // Envia um pedido ao banco de dados e recebe sua resposta

            // Envia a resposta final ao front
            return $response->code(\intval($res['status']))->header('Content-Type', 'application/json')->body(\json_encode(['message' => $res['message'], 'error' => $res['error'] ?? []]));
        } catch (Exception $e) {
            // \var_dump($e->getCode());
            throw new Exception($e->getMessage());
        }
    }

    // Atualiza um plano existente
    public function update(Request $request, Response $response): Response
    {
        try {
            $planData = \json_decode($request->body(), true); // Pega os dados enviados do front

            // Verifica se todods os dados foram enviados
            arrayValidate($planData, ['id', 'name', 'describe', 'users', 'clients', 'price', 'type']);
            // Converte os tipos dos dados
            $planData = convertType($planData, ['int', 'string', 'string', 'int', 'int', 'decimals', 'string']);
            $planData = sanitizeArray($planData); // Sanitiza os dados enviados

            // Verifica se o tipo de plano é aceitavel
            if (\strtolower($planData['type']) !== 'anual' && \strtolower($planData['type']) !== 'mensal') {
                return $response->code(400)->header('Content-Type', 'application/json')->body(['message' => 'Tipo de plano invalido']);
            }

            // Envia o pedido ao banco de dados e recebe sua resposta
            $res = $this->updatePlan($planData['id'], $planData['name'], $planData['describe'], $planData['users'], $planData['clients'], $planData['price'], $planData['type']);

            // Envia uma resposta ao front
            return $response->code($res['status'])->header('Content-Type', 'application/json')->body(\json_encode(['message' => $res['message'], 'error' => $res['error'] ?? []]));
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), (int) $e->getCode());
        }
    }

    // Ativa um plano
    public function enable(Request $request, Response $response): Response
    {
        try {
            $plan = \json_decode($request->body(), true); // Pega os dados enviados

            arrayValidate($plan, ['plan']); // Verifica se os dados foram enviados
            $plan = sanitizeArray($plan); // Sanitiza os dados recebidos
            $plan = convertType($plan, ['int']); // Converte o tipo dos dados

            $res = $this->enableThePlan($plan['plan']); // Envia o pedido ao banco de dados e recebe sua resposta

            // Envia uma resposta ao front
            return $response->code($res['status'])->header('Content-Type', 'application/json')->body(\json_encode(['message' => $res['message'], 'erro' => $res['error'] ?? []]));
        } catch (Exception $e) {
            throw new Exception('Erro ao executar o pedido: '.$e->getMessage(), (int) $e->getCode());
        }
    }

    // Desativa um plano
    public function disable(Request $request, Response $response): Response
    {
        try {
            $plan = \json_decode($request->body(), true); // Pega os dados enviados

            arrayValidate($plan, ['plan']); // Verifica se os dados foram enviados
            $plan = sanitizeArray($plan); // Sanitiza os dados recebidos
            $plan = convertType($plan, ['int']); // Converte o tipo dos dados

            $res = $this->disableThePlan($plan['plan']); // Envia o pedido ao // Verifica se há um retorno

            // Envia uma resposta ao front
            return $response->code($res['status'])->header('Content-Type', 'application/json')->body(\json_encode(['message' => $res['message'], 'erro' => $res['error'] ?? []]));
        } catch (Exception $e) {
            throw new Exception('Erro ao executar o pedido: '.$e->getMessage(), (int) $e->getCode());
        }
    }
}
